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
            ]);

            // Enviar notificación de error
            $this->sendErrorNotification($telegramService, $e->getMessage());

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

        if ($document->issuer) {
            $message .= "👤 <b>Emisor:</b> {$document->issuer}\n";
        }

        if ($document->amount) {
            $currency = $document->currency ?? $this->user->tenant->currency_code ?? 'USD';
            $message .= "💰 <b>Monto:</b> {$document->amount} {$currency}\n";
        }

        if ($document->document_date) {
            $message .= "📅 <b>Fecha:</b> {$document->document_date->format('d/m/Y')}\n";
        }

        if ($document->ocr_data && isset($document->ocr_data['concept'])) {
            $concept = substr($document->ocr_data['concept'], 0, 100);
            $message .= "📝 <b>Concepto:</b> {$concept}\n";
        }

        $message .= "\n📁 El documento ha sido guardado y organizado automáticamente.";
        $message .= "\n\n🌐 Puedes verlo en: https://dataflow.guaraniappstore.com/documents";

        $telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * Enviar notificación de error
     */
    protected function sendErrorNotification(TelegramService $telegramService, string $error): void
    {
        $message = "❌ <b>Error al procesar la factura</b>\n\n";
        $message .= "Lo sentimos, hubo un problema al procesar tu documento.\n\n";
        $message .= "<b>Error:</b> {$error}\n\n";
        $message .= "Por favor, intenta nuevamente o contacta al soporte si el problema persiste.";

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
            $this->sendErrorNotification($telegramService, 'El documento no pudo ser procesado después de varios intentos.');
        } catch (\Exception $e) {
            Log::error('No se pudo enviar notificación de fallo', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
