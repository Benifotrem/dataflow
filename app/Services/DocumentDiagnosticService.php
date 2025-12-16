<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de diagnóstico inteligente de documentos
 * Analiza errores y proporciona soluciones al usuario
 */
class DocumentDiagnosticService
{
    /**
     * Analizar error de documento y proporcionar diagnóstico
     */
    public function diagnoseDocumentError(Document $document): array
    {
        $error = $document->rejection_reason ?? 'Error desconocido';
        $ocrData = $document->ocr_data ?? [];

        // Detectar tipo de error
        if (str_contains($error, 'No se pudo descargar')) {
            return $this->diagnosticDownloadError($document);
        }

        if (str_contains($error, 'OpenAI') || str_contains($error, 'OCR')) {
            return $this->diagnosticOcrError($document, $ocrData);
        }

        if (str_contains($error, 'DNIT') || str_contains($error, 'SET')) {
            return $this->diagnosticValidationError($document, $ocrData);
        }

        if (str_contains($error, 'PDF')) {
            return $this->diagnosticPdfError($document);
        }

        if (empty($ocrData) || $this->isEmptyImage($ocrData)) {
            return $this->diagnosticEmptyImageError($document);
        }

        // Error genérico
        return [
            'type' => 'unknown',
            'severity' => 'medium',
            'message' => "Error al procesar documento",
            'reason' => substr($error, 0, 200),
            'solutions' => [
                "1. Verifica que la imagen sea clara y legible",
                "2. Asegúrate de que sea una factura válida",
                "3. Intenta subirla desde la plataforma web",
                "4. Contacta a soporte si el problema persiste"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => true,
        ];
    }

    /**
     * Diagnóstico: Error al descargar archivo de Telegram
     */
    protected function diagnosticDownloadError(Document $document): array
    {
        return [
            'type' => 'download_failed',
            'severity' => 'high',
            'message' => "No se pudo descargar el archivo de Telegram",
            'reason' => "El archivo no está disponible en los servidores de Telegram",
            'solutions' => [
                "1. Envía el archivo de nuevo",
                "2. Si es muy pesado, comprime la imagen antes",
                "3. Usa /app para subir con compresión automática",
                "4. Intenta con formato JPG en lugar de PNG"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => false,
        ];
    }

    /**
     * Diagnóstico: Error en OCR (OpenAI Vision)
     */
    protected function diagnosticOcrError(Document $document, array $ocrData): array
    {
        return [
            'type' => 'ocr_failed',
            'severity' => 'high',
            'message' => "No se pudo extraer información de la imagen",
            'reason' => "La IA no pudo leer el texto del documento",
            'solutions' => [
                "1. Toma una foto más clara y con buena iluminación",
                "2. Asegúrate de que todo el documento esté visible",
                "3. Evita reflejos y sombras",
                "4. Endereza el documento antes de capturar la imagen",
                "5. Si es PDF, verifica que no esté protegido"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => true,
        ];
    }

    /**
     * Diagnóstico: Error en validación fiscal (DNIT/SET)
     */
    protected function diagnosticValidationError(Document $document, array $ocrData): array
    {
        $hasBasicData = !empty($ocrData['ruc_emisor']) && !empty($ocrData['timbrado']);

        if (!$hasBasicData) {
            return [
                'type' => 'missing_fiscal_data',
                'severity' => 'medium',
                'message' => "Faltan datos fiscales obligatorios",
                'reason' => "No se detectó RUC o Timbrado en el documento",
                'solutions' => [
                    "1. Verifica que sea una factura oficial válida",
                    "2. Asegúrate de que el RUC y Timbrado sean legibles",
                    "3. Edita manualmente los datos desde la plataforma web",
                    "4. Para facturas manuscritas, ingresa los datos manualmente"
                ],
                'can_retry' => false,
                'manual_upload_recommended' => true,
            ];
        }

        return [
            'type' => 'validation_failed',
            'severity' => 'medium',
            'message' => "La factura no pasó la validación de la SET",
            'reason' => "El RUC o Timbrado no están registrados correctamente",
            'solutions' => [
                "1. Verifica que el RUC del emisor esté correcto",
                "2. Confirma que el Timbrado esté vigente",
                "3. Revisa los montos calculados",
                "4. Edita manualmente desde la plataforma web",
                "5. Algunos campos pueden requerir corrección manual"
            ],
            'can_retry' => false,
            'manual_upload_recommended' => true,
        ];
    }

    /**
     * Diagnóstico: Error con archivos PDF
     */
    protected function diagnosticPdfError(Document $document): array
    {
        return [
            'type' => 'pdf_conversion_failed',
            'severity' => 'medium',
            'message' => "No se pudo procesar el PDF",
            'reason' => "El servidor no pudo convertir el PDF a imagen",
            'solutions' => [
                "1. Envía la factura como FOTO (JPG o PNG)",
                "2. Toma una captura de pantalla del PDF",
                "3. Usa la app de cámara de tu teléfono para capturar el documento",
                "4. Si es PDF protegido, desprotégelo primero"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => false,
        ];
    }

    /**
     * Diagnóstico: Imagen vacía o sin texto
     */
    protected function diagnosticEmptyImageError(Document $document): array
    {
        return [
            'type' => 'empty_image',
            'severity' => 'low',
            'message' => "La imagen no contiene texto legible",
            'reason' => "No se detectó información fiscal en la imagen",
            'solutions' => [
                "1. Verifica que enviaste la imagen correcta",
                "2. Asegúrate de que la imagen tenga texto visible",
                "3. Aumenta el brillo si la foto está oscura",
                "4. Toma una nueva foto más clara",
                "5. Si es una imagen de prueba, envía una factura real"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => false,
        ];
    }

    /**
     * Detectar si la imagen está vacía (sin datos extraídos)
     */
    protected function isEmptyImage(array $ocrData): bool
    {
        $criticalFields = [
            'ruc_emisor',
            'razon_social_emisor',
            'timbrado',
            'numero_factura',
            'monto_total'
        ];

        $extractedCount = 0;
        foreach ($criticalFields as $field) {
            if (!empty($ocrData[$field])) {
                $extractedCount++;
            }
        }

        // Si extrajo menos de 2 campos críticos, probablemente imagen vacía
        return $extractedCount < 2;
    }

    /**
     * Generar mensaje amigable para Telegram con el diagnóstico
     */
    public function formatDiagnosticMessage(array $diagnostic, Document $document): string
    {
        $severityEmoji = [
            'low' => '⚠️',
            'medium' => '⚠️',
            'high' => '❌',
        ];

        $emoji = $severityEmoji[$diagnostic['severity']] ?? '❌';

        $message = "{$emoji} <b>{$diagnostic['message']}</b>\n\n";
        $message .= "📄 <b>Documento:</b> {$document->original_filename}\n";
        $message .= "🔍 <b>Motivo:</b> {$diagnostic['reason']}\n\n";

        $message .= "💡 <b>Cómo solucionarlo:</b>\n";
        foreach ($diagnostic['solutions'] as $solution) {
            $message .= "{$solution}\n";
        }

        if ($diagnostic['can_retry']) {
            $message .= "\n🔄 Puedes enviar el documento de nuevo.";
        }

        if ($diagnostic['manual_upload_recommended']) {
            $message .= "\n\n🌐 También puedes subirlo manualmente:\n";
            $message .= "https://dataflow.guaraniappstore.com/documents/create";
        }

        $message .= "\n\n💬 Si tienes dudas, pregúntame sobre fiscalidad paraguaya.";

        return $message;
    }
}
