<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\User;
use App\Services\OcrVisionService;
use App\Services\DnitConnector;
use App\Services\TelegramService;
use App\Services\FiscalValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Job de orquestación para procesamiento de facturas con OCR y validación fiscal
 *
 * Este Job coordina:
 * 1. Extracción de datos con OpenAI Vision (OCR)
 * 2. Validación fiscal con DNIT (SET de Paraguay)
 * 3. Almacenamiento del documento
 * 4. Notificaciones al usuario vía Telegram
 */
class OcrInvoiceProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de reintentos (0 para evitar bucles)
     */
    public $tries = 1;

    /**
     * Timeout en segundos (más largo para OCR + validación)
     */
    public $timeout = 120;

    /**
     * Delay entre reintentos (en segundos)
     */
    public $backoff = [];

    protected User $user;
    protected ?string $fileId;
    protected string $fileName;
    protected string $mimeType;
    protected ?int $chatId;
    protected ?string $promptContext;
    protected ?string $fileContent; // Contenido del archivo ya descargado (para miniapp)

    /**
     * Create a new job instance.
     *
     * @param User $user Usuario que envió el documento
     * @param string|null $fileId ID del archivo en Telegram (null si viene de miniapp)
     * @param string $fileName Nombre del archivo
     * @param string $mimeType Tipo MIME
     * @param int|null $chatId ID del chat de Telegram (null si viene de miniapp)
     * @param string|null $promptContext Contexto adicional para el OCR
     * @param string|null $fileContent Contenido del archivo en base64 (para miniapp)
     */
    public function __construct(
        User $user,
        ?string $fileId,
        string $fileName,
        string $mimeType,
        ?int $chatId,
        ?string $promptContext = null,
        ?string $fileContent = null
    ) {
        $this->user = $user;
        $this->fileId = $fileId;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->chatId = $chatId;
        $this->promptContext = $promptContext;
        $this->fileContent = $fileContent;
    }

    /**
     * Execute the job.
     */
    public function handle(
        TelegramService $telegramService,
        OcrVisionService $ocrVisionService,
        DnitConnector $dnitConnector,
        FiscalValidationService $fiscalValidation
    ): void {
        try {
            Log::info('🚀 Iniciando procesamiento de factura con validación fiscal', [
                'user_id' => $this->user->id,
                'file_id' => $this->fileId ?? 'miniapp',
                'file_name' => $this->fileName,
                'source' => $this->fileId ? 'telegram' : 'miniapp',
            ]);

            // PASO 1: Obtener contenido del archivo
            if ($this->fileContent) {
                // Viene de miniapp con contenido ya descargado
                $fileData = [
                    'content' => base64_decode($this->fileContent),
                    'size' => strlen(base64_decode($this->fileContent)),
                ];
                Log::info('📱 Archivo recibido desde miniapp', ['size' => $fileData['size']]);
            } elseif ($this->fileId) {
                // Viene de Telegram, necesita descargar
                $fileData = $telegramService->downloadFile($this->fileId);
                if (!$fileData) {
                    throw new \Exception('No se pudo descargar el archivo de Telegram');
                }
                Log::info('📥 Archivo descargado desde Telegram', ['size' => $fileData['size']]);
            } else {
                throw new \Exception('No se proporcionó ni fileId ni fileContent');
            }

            // PASO 2: Crear registro de documento inicial
            $document = Document::create([
                'tenant_id' => $this->user->tenant_id,
                'entity_id' => $this->user->tenant->entities()->first()?->id,
                'user_id' => $this->user->id,
                'type' => 'invoice',
                'file_path' => '', // Se actualizará después
                'original_filename' => $this->fileName,
                'mime_type' => $this->mimeType,
                'file_size' => $fileData['size'],
                'ocr_status' => 'pending',
            ]);

            Log::info('📄 Documento creado', ['document_id' => $document->id]);

            // PASO 3: Guardar archivo temporalmente
            $tempPath = "documents/telegram/temp/{$document->id}_{$this->fileName}";
            Storage::put($tempPath, $fileData['content']);
            $document->update(['file_path' => $tempPath, 'ocr_status' => 'processing']);

            // PASO 4: Procesar OCR con OpenAI Vision
            Log::info('🔍 Iniciando extracción OCR', ['document_id' => $document->id]);

            // Si es PDF, convertir a imagen
            $imageContent = $fileData['content'];
            $imageMimeType = $this->mimeType;

            if (str_starts_with($this->mimeType, 'application/pdf')) {
                // Verificar que las dependencias estén instaladas
                if (!class_exists(\Spatie\PdfToImage\Pdf::class)) {
                    throw new \Exception(
                        "El servidor aún no está configurado para procesar PDFs.\n\n" .
                        "Por favor:\n" .
                        "1. Envía la factura como FOTO (JPG/PNG)\n" .
                        "2. Toma una foto clara con tu celular\n" .
                        "3. Asegúrate de que todos los datos sean legibles\n\n" .
                        "El administrador será notificado para completar la configuración."
                    );
                }

                Log::info('📄 PDF detectado, convirtiendo a imagen...', ['document_id' => $document->id]);

                $pdfConverter = app(\App\Services\PdfConverterService::class);

                // Crear directorio temp si no existe
                $tempDir = storage_path('app/temp');
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                // Guardar PDF temporalmente
                $pdfTempPath = $tempDir . '/pdf_' . $document->id . '.pdf';
                file_put_contents($pdfTempPath, $fileData['content']);

                // Convertir a imagen
                $conversion = $pdfConverter->convertToImage($pdfTempPath);

                if (!$conversion['success']) {
                    // Si falla la conversión, informar al usuario
                    throw new \Exception(
                        "No se pudo convertir el PDF. " . $conversion['error'] . "\n\n" .
                        "Por favor intenta:\n" .
                        "1. Enviar la factura como foto (JPG/PNG)\n" .
                        "2. Asegurarte de que el PDF no esté protegido"
                    );
                }

                // Leer imagen convertida (ahora en PNG para mejor calidad)
                $imageContent = file_get_contents($conversion['image_path']);
                $imageMimeType = 'image/png';

                // Limpiar archivos temporales
                @unlink($pdfTempPath);
                @unlink($conversion['image_path']);

                Log::info('✅ PDF convertido a imagen exitosamente', ['document_id' => $document->id]);
            }

            $base64Image = base64_encode($imageContent);
            $ocrResult = $ocrVisionService->extractInvoiceData(
                $base64Image,
                $imageMimeType,
                $this->promptContext ?? ''
            );

            if (!$ocrResult['success']) {
                throw new \Exception('Error en OCR: ' . ($ocrResult['error'] ?? 'Desconocido'));
            }

            $extractedData = $ocrResult['data'];
            $validation = $ocrResult['validation'];

            Log::info('✅ OCR completado', [
                'document_id' => $document->id,
                'completeness' => $validation['completeness'] ?? 0,
                'errors' => $validation['errors'] ?? [],
            ]);

            // Actualizar documento con datos de OCR
            $document->update([
                'ocr_data' => $extractedData,
                'amount' => $extractedData['monto_total'] ?? null,
                'currency' => $extractedData['moneda'] ?? 'PYG',
                'document_date' => $extractedData['fecha_emision'] ?? null,
                'issuer' => $extractedData['razon_social_emisor'] ?? null,
                'invoice_number' => $extractedData['numero_factura'] ?? null,
                'tax_amount' => $extractedData['total_iva'] ?? null,
                'tax_base' => $extractedData['subtotal'] ?? null,
            ]);

            // PASO 4.5: Validación Matemática Fiscal
            Log::info('🔢 Validando coherencia matemática de importes', ['document_id' => $document->id]);

            $fiscalValidationResult = $fiscalValidation->validateInvoiceAmounts([
                'total_amount' => $extractedData['monto_total'] ?? 0,
                'iva_10_base' => $extractedData['subtotal_gravado_10'] ?? 0,
                'iva_10' => $extractedData['iva_10'] ?? 0,
                'iva_5_base' => $extractedData['subtotal_gravado_5'] ?? 0,
                'iva_5' => $extractedData['iva_5'] ?? 0,
                'exentas' => $extractedData['subtotal_exentas'] ?? 0,
            ]);

            // Si hay errores matemáticos, intentar auto-corregir
            if (!$fiscalValidationResult['valid']) {
                Log::warning('⚠️ Errores matemáticos detectados, intentando auto-corrección', [
                    'document_id' => $document->id,
                    'errors' => $fiscalValidationResult['errors'],
                ]);

                $correctedData = $fiscalValidation->autoCorrectAmounts([
                    'total_amount' => $extractedData['monto_total'] ?? 0,
                    'iva_10_base' => $extractedData['subtotal_gravado_10'] ?? 0,
                    'iva_10' => $extractedData['iva_10'] ?? 0,
                    'iva_5_base' => $extractedData['subtotal_gravado_5'] ?? 0,
                    'iva_5' => $extractedData['iva_5'] ?? 0,
                    'exentas' => $extractedData['subtotal_exentas'] ?? 0,
                ]);

                // Si se pudo auto-corregir, actualizar extractedData
                if ($correctedData['corrected']) {
                    $extractedData['subtotal_gravado_10'] = $correctedData['iva_10_base'];
                    $extractedData['iva_10'] = $correctedData['iva_10'];
                    $extractedData['subtotal_gravado_5'] = $correctedData['iva_5_base'];
                    $extractedData['iva_5'] = $correctedData['iva_5'];
                    $extractedData['subtotal_exentas'] = $correctedData['exentas'];

                    // Re-calcular total IVA
                    $extractedData['total_iva'] = $fiscalValidation->calculateTotalIvaCredito($correctedData);

                    // Actualizar documento con datos corregidos
                    $document->update([
                        'ocr_data' => $extractedData,
                        'tax_amount' => $extractedData['total_iva'],
                    ]);

                    Log::info('✅ Importes auto-corregidos exitosamente', ['document_id' => $document->id]);

                    // Re-validar después de corrección
                    $fiscalValidationResult = $fiscalValidation->validateInvoiceAmounts([
                        'total_amount' => $extractedData['monto_total'] ?? 0,
                        'iva_10_base' => $extractedData['subtotal_gravado_10'] ?? 0,
                        'iva_10' => $extractedData['iva_10'] ?? 0,
                        'iva_5_base' => $extractedData['subtotal_gravado_5'] ?? 0,
                        'iva_5' => $extractedData['iva_5'] ?? 0,
                        'exentas' => $extractedData['subtotal_exentas'] ?? 0,
                    ]);
                }
            }

            // Agregar errores de validación fiscal a los errores de validación general
            if (!$fiscalValidationResult['valid']) {
                $validation['errors'] = array_merge(
                    $validation['errors'] ?? [],
                    $fiscalValidationResult['errors']
                );
                $validation['valid'] = false;
            }

            // Agregar warnings de validación fiscal
            if (!empty($fiscalValidationResult['warnings'])) {
                $validation['warnings'] = array_merge(
                    $validation['warnings'] ?? [],
                    $fiscalValidationResult['warnings']
                );
            }

            // PASO 5: Validar con DNIT si tenemos los datos necesarios
            $dnitValidation = null;
            $needsManualCheck = false;

            if ($validation['valid']) {
                Log::info('🔐 Iniciando validación fiscal con DNIT', ['document_id' => $document->id]);

                try {
                    $dnitValidation = $dnitConnector->validateInvoice([
                        'ruc_emisor' => $extractedData['ruc_emisor'] ?? null,
                        'timbrado' => $extractedData['timbrado'] ?? null,
                        'fecha_emision' => $extractedData['fecha_emision'] ?? null,
                        'monto_total' => $extractedData['monto_total'] ?? null,
                        'tipo_factura' => $extractedData['tipo_factura'] ?? null, // CRÍTICO: Necesario para detectar facturas electrónicas
                    ]);

                    Log::info('🏛️ Validación DNIT completada', [
                        'document_id' => $document->id,
                        'valid' => $dnitValidation['valid'],
                        'errors' => $dnitValidation['errors'] ?? [],
                    ]);

                    // Si la validación DNIT falla, marcar para revisión manual
                    if (!$dnitValidation['valid']) {
                        $needsManualCheck = true;
                        $rejectionReasons = implode(', ', $dnitValidation['errors']);

                        $document->update([
                            'rejection_reason' => "Validación fiscal fallida: {$rejectionReasons}",
                            'validated' => false,
                        ]);
                    } else {
                        // Validación exitosa
                        $document->update([
                            'validated' => true,
                            'validated_at' => now(),
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::warning('⚠️ Error en validación DNIT', [
                        'document_id' => $document->id,
                        'error' => $e->getMessage(),
                    ]);

                    $needsManualCheck = true;
                    $document->update([
                        'rejection_reason' => "Error al validar con DNIT: {$e->getMessage()}",
                    ]);
                }
            } else {
                // OCR incompleto o con errores
                $needsManualCheck = true;
                $ocrErrors = implode(', ', $validation['errors']);

                $document->update([
                    'rejection_reason' => "Datos de OCR incompletos: {$ocrErrors}",
                ]);
            }

            // PASO 6: Reorganizar archivo en estructura de carpetas
            if ($document->issuer && $document->document_date) {
                $newPath = $this->organizeDocument($document, $fileData['content']);

                if ($newPath) {
                    Storage::delete($tempPath);
                    $document->update(['file_path' => $newPath]);

                    Log::info('📁 Documento organizado', [
                        'document_id' => $document->id,
                        'new_path' => $newPath,
                    ]);
                }
            }

            // PASO 7: Actualizar estado final de OCR
            $document->update(['ocr_status' => 'completed']);

            // PASO 8: Enviar notificación al usuario (solo si viene de Telegram)
            if ($this->chatId) {
                if ($needsManualCheck) {
                    $this->sendManualCheckNotification(
                        $telegramService,
                        $document,
                        $validation,
                        $dnitValidation
                    );
                } else {
                    $this->sendSuccessNotification(
                        $telegramService,
                        $document,
                        $validation,
                        $dnitValidation
                    );
                }
            } else {
                Log::info('📱 Notificación omitida (origen: miniapp)', [
                    'document_id' => $document->id,
                    'needs_manual_check' => $needsManualCheck,
                ]);
            }

            Log::info('✨ Procesamiento de factura completado exitosamente', [
                'document_id' => $document->id,
                'user_id' => $this->user->id,
                'needs_manual_check' => $needsManualCheck,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error crítico en procesamiento de factura', [
                'user_id' => $this->user->id,
                'file_id' => $this->fileId ?? 'miniapp',
                'source' => $this->fileId ? 'telegram' : 'miniapp',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Marcar documento como fallido si existe
            if (isset($document)) {
                try {
                    $document->update([
                        'ocr_status' => 'failed',
                        'rejection_reason' => substr($e->getMessage(), 0, 500),
                    ]);
                } catch (\Exception $updateError) {
                    Log::error('No se pudo actualizar documento fallido', [
                        'error' => $updateError->getMessage(),
                    ]);
                }
            }

            // Enviar notificación de error (solo si viene de Telegram)
            if ($this->chatId) {
                try {
                    $this->sendErrorNotification($telegramService, $e->getMessage(), $document ?? null);
                } catch (\Exception $notifError) {
                    Log::error('No se pudo enviar notificación de error', [
                        'error' => $notifError->getMessage(),
                    ]);
                }
            }

            // NO relanzar la excepción para evitar reintentos infinitos
            // El job se marcará como completado aunque haya fallado
            Log::info('Job finalizado con errores (no se reintentará)', [
                'user_id' => $this->user->id,
                'file_id' => $this->fileId ?? 'miniapp',
            ]);
        }
    }

    /**
     * Organizar documento en estructura de carpetas
     */
    protected function organizeDocument(Document $document, string $fileContent): ?string
    {
        try {
            $issuer = $this->sanitizeFileName($document->issuer);
            $year = $document->document_date->format('Y');
            $month = $document->document_date->format('m');

            $basePath = "contadores/{$this->user->id}/facturas/{$issuer}/{$year}/{$month}";
            $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
            $uniqueName = time() . '_' . $document->id . '.' . $extension;
            $fullPath = "{$basePath}/{$uniqueName}";

            Storage::put($fullPath, $fileContent);

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
        $name = preg_replace('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-_]/', '', $name);
        $name = preg_replace('/\s+/', '_', $name);
        $name = trim($name, '_-');

        return substr($name, 0, 50) ?: 'sin_nombre';
    }

    /**
     * Enviar notificación de éxito (factura validada)
     */
    protected function sendSuccessNotification(
        TelegramService $telegramService,
        Document $document,
        array $validation,
        ?array $dnitValidation
    ): void {
        $message = "✅ <b>¡Factura procesada y validada con la SET!</b>\n\n";

        $message .= "🆔 <b>ID:</b> #{$document->id}\n";
        $message .= "📄 <b>Archivo:</b> {$document->original_filename}\n\n";

        // Datos fiscales
        if ($document->ocr_data) {
            $data = $document->ocr_data;

            // Usar RUC validado con formato correcto (RUC-DV)
            if (isset($data['ruc_emisor'])) {
                $rucFormateado = $data['ruc_emisor'];

                // Si hay validación de DNIT, usar el RUC validado con formato correcto
                if ($dnitValidation && isset($dnitValidation['data']['ruc_validation']['data']['ruc'])) {
                    $rucValidado = $dnitValidation['data']['ruc_validation']['data']['ruc'];
                    $dvValidado = $dnitValidation['data']['ruc_validation']['data']['dv'] ?? '';
                    $rucFormateado = $dvValidado ? "{$rucValidado}-{$dvValidado}" : $rucValidado;
                }

                $message .= "🏢 <b>RUC Emisor:</b> {$rucFormateado} ✓\n";
            }

            // Usar Razón Social validada de la SET si está disponible
            $razonSocial = $data['razon_social_emisor'] ?? null;
            if ($dnitValidation && isset($dnitValidation['data']['ruc_validation']['data']['razon_social'])) {
                $razonSocial = $dnitValidation['data']['ruc_validation']['data']['razon_social'];
            }

            if ($razonSocial) {
                $message .= "📋 <b>Razón Social:</b> {$razonSocial}\n";
            }

            if (isset($data['timbrado'])) {
                $message .= "🔐 <b>Timbrado:</b> {$data['timbrado']} ✓\n";
            }

            if (isset($data['numero_factura'])) {
                $message .= "📑 <b>Nº Factura:</b> {$data['numero_factura']}\n";
            }

            if ($document->document_date) {
                $message .= "📅 <b>Fecha:</b> {$document->document_date->format('d/m/Y')}\n";
            }

            // Receptor si existe
            if (isset($data['ruc_receptor'])) {
                $message .= "🏪 <b>RUC Receptor:</b> {$data['ruc_receptor']}\n";
            }
            if (isset($data['razon_social_receptor'])) {
                $message .= "👤 <b>Cliente:</b> {$data['razon_social_receptor']}\n";
            }

            $message .= "\n💰 <b>MONTOS EXTRAÍDOS:</b>\n";

            if (isset($data['subtotal_gravado_10']) && $data['subtotal_gravado_10'] > 0) {
                $message .= "   • Gravado 10%: ₲ " . number_format($data['subtotal_gravado_10'], 0, ',', '.') . "\n";
            }
            if (isset($data['iva_10']) && $data['iva_10'] > 0) {
                $message .= "   • IVA 10%: ₲ " . number_format($data['iva_10'], 0, ',', '.') . "\n";
            }

            if (isset($data['subtotal_gravado_5']) && $data['subtotal_gravado_5'] > 0) {
                $message .= "   • Gravado 5%: ₲ " . number_format($data['subtotal_gravado_5'], 0, ',', '.') . "\n";
            }
            if (isset($data['iva_5']) && $data['iva_5'] > 0) {
                $message .= "   • IVA 5%: ₲ " . number_format($data['iva_5'], 0, ',', '.') . "\n";
            }

            if (isset($data['subtotal_exentas']) && $data['subtotal_exentas'] > 0) {
                $message .= "   • Exentas: ₲ " . number_format($data['subtotal_exentas'], 0, ',', '.') . "\n";
            }

            if (isset($data['total_iva']) && $data['total_iva'] > 0) {
                $message .= "   • <b>Total IVA:</b> ₲ " . number_format($data['total_iva'], 0, ',', '.') . "\n";
            }

            if (isset($data['monto_total'])) {
                $message .= "   • <b>TOTAL: " . number_format($data['monto_total'], 0, ',', '.') . " PYG</b>\n";
            }
        }

        $message .= "\n🎯 <b>Completitud:</b> {$validation['completeness']}%\n";
        $message .= "🏛️ <b>Estado SET:</b> Validado correctamente\n";

        $message .= "\n📁 El documento ha sido guardado y está listo para contabilizar.";
        $message .= "\n\n🌐 Ver en plataforma: " . config('app.url') . "/documents/{$document->id}";

        $telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * Enviar notificación de revisión manual
     */
    protected function sendManualCheckNotification(
        TelegramService $telegramService,
        Document $document,
        array $validation,
        ?array $dnitValidation
    ): void {
        $message = "⚠️ <b>Factura requiere revisión manual</b>\n\n";

        $message .= "🆔 <b>ID:</b> #{$document->id}\n";
        $message .= "📄 <b>Archivo:</b> {$document->original_filename}\n\n";

        $message .= "📊 <b>DATOS EXTRAÍDOS:</b>\n";

        if ($document->ocr_data) {
            $data = $document->ocr_data;

            // Usar RUC validado si está disponible (con formato correcto)
            $rucFormateado = $data['ruc_emisor'] ?? '❌ No detectado';
            if ($dnitValidation && isset($dnitValidation['data']['ruc_validation']['data']['ruc'])) {
                $rucValidado = $dnitValidation['data']['ruc_validation']['data']['ruc'];
                $dvValidado = $dnitValidation['data']['ruc_validation']['data']['dv'] ?? '';
                $rucFormateado = $dvValidado ? "{$rucValidado}-{$dvValidado}" : $rucValidado;
            }

            $message .= "   • RUC: " . $rucFormateado . "\n";

            // Usar Razón Social validada si está disponible
            $razonSocial = $data['razon_social_emisor'] ?? null;
            if ($dnitValidation && isset($dnitValidation['data']['ruc_validation']['data']['razon_social'])) {
                $razonSocial = $dnitValidation['data']['ruc_validation']['data']['razon_social'];
            }
            if ($razonSocial) {
                $message .= "   • Razón Social: " . $razonSocial . "\n";
            }

            $message .= "   • Timbrado: " . ($data['timbrado'] ?? '❌ No detectado') . "\n";
            $message .= "   • Fecha: " . ($data['fecha_emision'] ?? '❌ No detectada') . "\n";

            if (isset($data['ruc_receptor'])) {
                $message .= "   • RUC Receptor: " . $data['ruc_receptor'] . "\n";
            }
            if (isset($data['razon_social_receptor'])) {
                $message .= "   • Cliente: " . $data['razon_social_receptor'] . "\n";
            }

            $message .= "\n   💰 <b>Montos:</b>\n";

            if (isset($data['subtotal_gravado_10']) && $data['subtotal_gravado_10'] > 0) {
                $message .= "      • Gravado 10%: ₲ " . number_format($data['subtotal_gravado_10'], 0, ',', '.') . "\n";
            }
            if (isset($data['iva_10']) && $data['iva_10'] > 0) {
                $message .= "      • IVA 10%: ₲ " . number_format($data['iva_10'], 0, ',', '.') . "\n";
            }
            if (isset($data['subtotal_gravado_5']) && $data['subtotal_gravado_5'] > 0) {
                $message .= "      • Gravado 5%: ₲ " . number_format($data['subtotal_gravado_5'], 0, ',', '.') . "\n";
            }
            if (isset($data['iva_5']) && $data['iva_5'] > 0) {
                $message .= "      • IVA 5%: ₲ " . number_format($data['iva_5'], 0, ',', '.') . "\n";
            }
            if (isset($data['subtotal_exentas']) && $data['subtotal_exentas'] > 0) {
                $message .= "      • Exentas: ₲ " . number_format($data['subtotal_exentas'], 0, ',', '.') . "\n";
            }
            if (isset($data['total_iva']) && $data['total_iva'] > 0) {
                $message .= "      • Total IVA: ₲ " . number_format($data['total_iva'], 0, ',', '.') . "\n";
            }

            $message .= "      • <b>TOTAL: " . (isset($data['monto_total']) ? "₲ " . number_format($data['monto_total'], 0, ',', '.') : '❌ No detectado') . "</b>\n";
        }

        $message .= "\n⚠️ <b>MOTIVOS DE REVISIÓN:</b>\n";

        // Errores de OCR
        if (!empty($validation['errors'])) {
            foreach ($validation['errors'] as $error) {
                $message .= "   • {$error}\n";
            }
        }

        // Errores de DNIT
        if ($dnitValidation && !empty($dnitValidation['errors'])) {
            foreach ($dnitValidation['errors'] as $error) {
                $message .= "   • {$error}\n";
            }
        }

        $message .= "\n💡 <b>RECOMENDACIONES:</b>\n";
        $message .= "   1. Verifica la calidad de la imagen\n";
        $message .= "   2. Asegúrate de que todos los datos fiscales sean legibles\n";
        $message .= "   3. Puedes editar manualmente los datos desde la plataforma web\n";

        $message .= "\n🌐 Revisar documento: " . config('app.url') . "/documents/{$document->id}";

        $telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * Enviar notificación de error
     */
    protected function sendErrorNotification(
        TelegramService $telegramService,
        string $error,
        ?Document $document = null
    ): void {
        $message = "❌ <b>Error al procesar factura</b>\n\n";

        if ($document) {
            $message .= "🆔 <b>ID:</b> #{$document->id}\n";
            $message .= "📄 <b>Archivo:</b> {$document->original_filename}\n\n";
        }

        $message .= "⚠️ <b>Error:</b> {$error}\n\n";
        $message .= "Por favor, intenta nuevamente o contacta al soporte si el problema persiste.";

        $telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job OcrInvoiceProcessingJob falló definitivamente', [
            'user_id' => $this->user->id,
            'file_id' => $this->fileId ?? 'miniapp',
            'source' => $this->fileId ? 'telegram' : 'miniapp',
            'error' => $exception->getMessage(),
        ]);

        // Solo enviar notificación de Telegram si viene de Telegram
        if ($this->chatId) {
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
}
