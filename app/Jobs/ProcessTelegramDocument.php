<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\User;
use App\Services\OcrService;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessTelegramDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que se puede intentar el job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * El número de segundos que el job puede ejecutarse antes de timeout.
     *
     * @var int
     */
    public $timeout = 300;

    protected User $user;
    protected string $fileId;
    protected string $fileName;
    protected string $mimeType;
    protected int $chatId;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $fileId, string $fileName, string $mimeType, int $chatId)
    {
        $this->user = $user;
        $this->fileId = $fileId;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegramService, OcrService $ocrService): void
    {
        try {
            Log::info('Iniciando procesamiento de documento de Telegram', [
                'user_id' => $this->user->id,
                'file_id' => $this->fileId,
                'file_name' => $this->fileName,
            ]);

            // 1. Descargar archivo de Telegram
            $fileData = $telegramService->downloadFile($this->fileId);

            if (!$fileData) {
                throw new \Exception('No se pudo descargar el archivo de Telegram');
            }

            // 2. Crear registro de documento (inicialmente sin organización por emisor)
            $document = Document::create([
                'tenant_id' => $this->user->tenant_id,
                'entity_id' => $this->user->tenant->entities()->first()?->id, // Primera entidad del tenant
                'user_id' => $this->user->id,
                'type' => 'invoice',
                'file_path' => '', // Se actualizará después
                'original_filename' => $this->fileName,
                'mime_type' => $this->mimeType,
                'file_size' => $fileData['size'],
                'ocr_status' => 'pending',
            ]);

            // 3. Guardar archivo temporalmente
            $tempPath = "documents/telegram/temp/{$document->id}_{$this->fileName}";
            Storage::put($tempPath, $fileData['content']);

            $document->update(['file_path' => $tempPath]);

            Log::info('Archivo guardado temporalmente', [
                'document_id' => $document->id,
                'temp_path' => $tempPath,
            ]);

            // 4. Procesar con OCR
            $processedDocument = $ocrService->processDocument($document);

            // 5. Reorganizar archivo según emisor/año/mes
            if ($processedDocument->isCompleted() && $processedDocument->issuer) {
                $newPath = $this->organizeDocument($processedDocument, $fileData['content']);

                if ($newPath) {
                    // Eliminar archivo temporal
                    Storage::delete($tempPath);

                    $processedDocument->update(['file_path' => $newPath]);

                    Log::info('Documento reorganizado', [
                        'document_id' => $processedDocument->id,
                        'new_path' => $newPath,
                    ]);
                }
            }

            // 6. Enviar notificación de éxito
            $this->sendSuccessNotification($telegramService, $processedDocument);

            Log::info('Documento de Telegram procesado exitosamente', [
                'document_id' => $processedDocument->id,
                'user_id' => $this->user->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar documento de Telegram', [
                'user_id' => $this->user->id,
                'file_id' => $this->fileId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $document->id ?? null,
            ]);

            // Enviar notificación de error con detalles del documento
            $this->sendErrorNotification($telegramService, $e->getMessage(), $document ?? null);

            throw $e;
        }
    }

    /**
     * Organizar documento en estructura de carpetas
     */
    protected function organizeDocument(Document $document, string $fileContent): ?string
    {
        try {
            // Sanitizar nombre del emisor
            $issuer = $this->sanitizeFileName($document->issuer);

            // Obtener año y mes del documento
            $year = $document->document_date ? $document->document_date->format('Y') : now()->format('Y');
            $month = $document->document_date ? $document->document_date->format('m') : now()->format('m');

            // Construir ruta: contadores/{user_id}/facturas/{emisor}/{año}/{mes}/
            $basePath = "contadores/{$this->user->id}/facturas/{$issuer}/{$year}/{$month}";

            // Generar nombre único para el archivo
            $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
            $uniqueName = time() . '_' . $document->id . '.' . $extension;

            $fullPath = "{$basePath}/{$uniqueName}";

            // Guardar archivo en nueva ubicación
            Storage::put($fullPath, $fileContent);

            Log::info('Documento organizado en carpetas', [
                'document_id' => $document->id,
                'issuer' => $issuer,
                'year' => $year,
                'month' => $month,
                'path' => $fullPath,
            ]);

            return $fullPath;
        } catch (\Exception $e) {
            Log::error('Error al organizar documento', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Sanitizar nombre de archivo
     */
    protected function sanitizeFileName(string $name): string
    {
        // Eliminar caracteres especiales y espacios
        $name = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_]/', '', $name);
        $name = preg_replace('/\s+/', '_', $name);
        $name = trim($name, '_-');

        // Limitar longitud
        $name = substr($name, 0, 50);

        return $name ?: 'sin_nombre';
    }

    /**
     * Enviar notificación de éxito
     */
    protected function sendSuccessNotification(TelegramService $telegramService, Document $document): void
    {
        $message = "✅ <b>¡Factura procesada exitosamente!</b>\n\n";

        // ID del documento para referencia
        $message .= "🆔 <b>Documento ID:</b> #{$document->id}\n";
        $message .= "📄 <b>Archivo:</b> {$document->original_filename}\n\n";

        // Número de factura si existe
        if ($document->invoice_number) {
            $invoiceRef = $document->invoice_series
                ? "{$document->invoice_series}-{$document->invoice_number}"
                : $document->invoice_number;
            $message .= "📋 <b>Nº Factura:</b> {$invoiceRef}\n";
        }

        if ($document->issuer) {
            $message .= "👤 <b>Emisor:</b> {$document->issuer}\n";
        }

        if ($document->document_date) {
            $message .= "📅 <b>Fecha:</b> {$document->document_date->format('d/m/Y')}\n";
        }

        // Desglose de IVA si está disponible
        if ($document->tax_base && $document->tax_amount) {
            $currency = $document->currency ?? $this->user->tenant->currency_code ?? 'EUR';
            $message .= "\n💵 <b>Desglose:</b>\n";
            $message .= "   • Base imponible: " . number_format($document->tax_base, 2, ',', '.') . " {$currency}\n";

            if ($document->tax_rate) {
                $message .= "   • IVA ({$document->tax_rate}%): " . number_format($document->tax_amount, 2, ',', '.') . " {$currency}\n";
            } else {
                $message .= "   • IVA: " . number_format($document->tax_amount, 2, ',', '.') . " {$currency}\n";
            }

            $message .= "   • <b>Total: " . number_format($document->total_with_tax, 2, ',', '.') . " {$currency}</b>\n";
        } elseif ($document->amount) {
            $currency = $document->currency ?? $this->user->tenant->currency_code ?? 'EUR';
            $message .= "\n💰 <b>Importe Total:</b> " . number_format($document->amount, 2, ',', '.') . " {$currency}\n";
        }

        if ($document->ocr_data && isset($document->ocr_data['concept'])) {
            $concept = substr($document->ocr_data['concept'], 0, 100);
            $message .= "\n📝 <b>Concepto:</b> {$concept}\n";
        }

        $message .= "\n📁 El documento ha sido guardado y organizado automáticamente.";
        $message .= "\n\n🌐 Ver en plataforma: https://dataflow.guaraniappstore.com/documents/{$document->id}";

        $telegramService->sendMessage($this->chatId, $message);

        // Enviar también notificación por email
        try {
            $brevoService = new \App\Services\BrevoService();
            if ($brevoService->isConfigured()) {
                $documentUrl = url("/documents/{$document->id}");
                $brevoService->sendDocumentProcessedNotification(
                    $this->user->email,
                    $this->user->name,
                    $document->original_filename,
                    $documentUrl
                );
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo enviar email de notificación de documento: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de error
     */
    protected function sendErrorNotification(TelegramService $telegramService, string $error, ?Document $document = null): void
    {
        $message = "❌ <b>Documento rechazado</b>\n\n";

        // Incluir ID del documento si está disponible
        if ($document) {
            $message .= "🆔 <b>Documento ID:</b> #{$document->id}\n";
            $message .= "📄 <b>Archivo:</b> {$document->original_filename}\n\n";
        }

        // Determinar el tipo de rechazo y proporcionar mensaje específico
        if (str_contains($error, 'no es una factura') || str_contains($error, 'No se pudo determinar el tipo')) {
            $message .= "🚫 <b>Motivo:</b> El documento enviado no es una factura válida.\n\n";
            $message .= "⚠️ <b>Requisitos para facturas:</b>\n";
            $message .= "   • Debe contener datos fiscales completos\n";
            $message .= "   • Debe tener emisor, receptor, fecha e importe\n";
            $message .= "   • No envíes fotos personales, capturas de pantalla o memes\n\n";
            $message .= "💡 <b>Tipos de documentos aceptados:</b>\n";
            $message .= "   ✅ Facturas con datos fiscales\n";
            $message .= "   ✅ Recibos de proveedores\n";
            $message .= "   ❌ Extractos bancarios (súbelos desde la web)\n";
            $message .= "   ❌ Notas de entrega sin datos fiscales\n";
        } elseif (str_contains($error, 'Calidad de imagen') || str_contains($error, 'insuficiente')) {
            $message .= "📸 <b>Motivo:</b> Calidad de imagen insuficiente para lectura.\n\n";
            $message .= "💡 <b>Recomendaciones:</b>\n";
            $message .= "   • Asegúrate de que la imagen esté bien iluminada\n";
            $message .= "   • Evita sombras o reflejos\n";
            $message .= "   • Enfoca correctamente el documento\n";
            $message .= "   • Si es posible, envía el PDF original\n";
            $message .= "   • No envíes imágenes borrosas o pixeladas\n\n";
            $message .= "🔄 Por favor, vuelve a enviar la factura con mejor calidad.";
        } elseif (str_contains($error, 'Límite de documentos')) {
            $message .= "🚨 <b>Motivo:</b> Límite mensual de documentos alcanzado.\n\n";
            $message .= "💳 Para continuar procesando documentos, puedes:\n";
            $message .= "   • Adquirir un addon de 500 documentos por $9.99\n";
            $message .= "   • Esperar hasta el próximo mes\n\n";
            $message .= "Usa /status para ver tu consumo actual.";
        } else {
            // Error genérico
            $message .= "⚠️ <b>Motivo:</b> {$error}\n\n";
            $message .= "Por favor, verifica el documento y vuelve a intentarlo.\n";
            $message .= "Si el problema persiste, contacta al soporte.";
        }

        $telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job ProcessTelegramDocument falló definitivamente', [
            'user_id' => $this->user->id,
            'file_id' => $this->fileId,
            'error' => $exception->getMessage(),
        ]);

        // Intentar enviar notificación de fallo
        try {
            $telegramService = app(TelegramService::class);
            $this->sendErrorNotification(
                $telegramService,
                'El documento no pudo ser procesado después de varios intentos.',
                null
            );
        } catch (\Exception $e) {
            Log::error('No se pudo enviar notificación de fallo', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
