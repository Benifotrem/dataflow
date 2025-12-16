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

        // Detectar si es factura extranjera
        $isForeignInvoice = isset($ocrData['invoice_type']) && $ocrData['invoice_type'] === 'foreign';

        // Si es factura extranjera con datos válidos, proporcionar mensaje específico
        if ($isForeignInvoice && !empty($ocrData['vendor_name'])) {
            return $this->diagnosticForeignInvoice($document, $ocrData);
        }

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
        $ocrData = $document->ocr_data ?? [];

        // Verificar si hay ALGÚN texto extraído
        $hasAnyText = false;
        foreach ($ocrData as $key => $value) {
            if (!empty($value) && is_string($value) && strlen(trim($value)) > 0) {
                $hasAnyText = true;
                break;
            }
        }

        if ($hasAnyText) {
            // Hay texto pero no es una factura paraguaya válida
            return [
                'type' => 'not_fiscal_document',
                'severity' => 'medium',
                'message' => "El documento no es una factura paraguaya válida",
                'reason' => "Se detectó texto pero no se encontraron datos fiscales (RUC, Timbrado, montos IVA)",
                'solutions' => [
                    "1. Verifica que sea una factura de Paraguay",
                    "2. Asegúrate de que tenga RUC y Timbrado visible",
                    "3. Confirma que muestre IVA 10% o IVA 5%",
                    "4. Si es de otro país, este sistema solo procesa facturas paraguayas",
                    "5. Intenta con una factura que incluya todos los datos fiscales"
                ],
                'can_retry' => true,
                'manual_upload_recommended' => false,
            ];
        }

        // Realmente no hay texto legible
        return [
            'type' => 'empty_image',
            'severity' => 'high',
            'message' => "No se pudo leer texto en la imagen",
            'reason' => "La imagen está muy oscura, borrosa o no contiene texto",
            'solutions' => [
                "1. Verifica que enviaste la imagen correcta",
                "2. Toma una foto con buena iluminación",
                "3. Asegúrate de que la imagen esté enfocada",
                "4. Evita fotos borrosas o con exceso de sombras",
                "5. Si es PDF protegido, desprotégelo primero"
            ],
            'can_retry' => true,
            'manual_upload_recommended' => false,
        ];
    }

    /**
     * Diagnóstico: Factura extranjera procesada correctamente
     */
    protected function diagnosticForeignInvoice(Document $document, array $ocrData): array
    {
        $vendorName = $ocrData['vendor_name'] ?? 'Proveedor extranjero';
        $currency = $ocrData['currency'] ?? 'USD';
        $amount = $ocrData['monto_total'] ?? 0;
        $invoiceNumber = $ocrData['invoice_number'] ?? 'N/A';
        $country = $ocrData['vendor_country'] ?? 'internacional';

        // Formatear monto según moneda
        $formattedAmount = $currency . ' ' . number_format($amount, 2, ',', '.');

        return [
            'type' => 'foreign_invoice_ok',
            'severity' => 'low',
            'message' => "✅ Factura internacional procesada correctamente",
            'reason' => "Factura de {$vendorName} ({$country}) - {$formattedAmount}",
            'solutions' => [
                "✓ Proveedor: {$vendorName}",
                "✓ Factura N°: {$invoiceNumber}",
                "✓ Monto: {$formattedAmount}",
                "✓ Tipo: Servicio internacional",
                "",
                "💡 Esta factura ha sido registrada como gasto de servicio extranjero.",
                "Puedes revisar y editar los detalles desde la plataforma web si es necesario."
            ],
            'can_retry' => false,
            'manual_upload_recommended' => false,
        ];
    }

    /**
     * Detectar si la imagen está vacía (sin datos extraídos)
     */
    protected function isEmptyImage(array $ocrData): bool
    {
        // Si es factura extranjera con datos, NO está vacía
        if (isset($ocrData['invoice_type']) && $ocrData['invoice_type'] === 'foreign') {
            return empty($ocrData['vendor_name']) && empty($ocrData['monto_total']);
        }

        // Para facturas paraguayas, verificar campos críticos
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
