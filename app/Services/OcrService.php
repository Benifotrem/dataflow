<?php

namespace App\Services;

use App\Models\Document;
use App\Models\AiUsage;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OcrService
{
    public function processDocument(Document $document)
    {
        // Verificar límite de documentos
        if ($document->tenant->hasExceededDocumentLimit()) {
            throw new \Exception('Límite de documentos mensuales excedido. Por favor, adquiere un addon.');
        }

        $document->update(['ocr_status' => 'processing']);

        try {
            // Obtener configuración de IA
            $provider = SystemSetting::get('ai_provider', 'openai');
            $model = SystemSetting::get('ai_model', 'gpt-4o-mini');
            $apiKey = SystemSetting::get('openai_api_key');

            if (!$apiKey) {
                throw new \Exception('API Key de IA no configurada');
            }

            // Leer archivo
            $fileContent = Storage::get($document->file_path);
            $base64 = base64_encode($fileContent);

            // PASO 1: Validación previa - ¿Es una factura? ¿Es de calidad aceptable?
            $validation = $this->validateDocument($base64, $document->mime_type, $apiKey, $model);

            if (!$validation['is_invoice']) {
                // No es una factura válida
                $document->update([
                    'ocr_status' => 'failed',
                    'is_invoice' => false,
                    'quality_status' => $validation['quality'],
                    'rejection_reason' => $validation['rejection_reason'],
                    'ocr_data' => ['validation_error' => $validation['rejection_reason']]
                ]);

                throw new \Exception($validation['rejection_reason']);
            }

            if ($validation['quality'] === 'poor' || $validation['quality'] === 'unreadable') {
                // Calidad insuficiente
                $document->update([
                    'ocr_status' => 'failed',
                    'is_invoice' => true,
                    'quality_status' => $validation['quality'],
                    'rejection_reason' => 'Calidad de imagen insuficiente. Por favor, envía una imagen más clara o escanea el documento con mejor resolución.',
                    'ocr_data' => ['quality_error' => 'Imagen de baja calidad']
                ]);

                throw new \Exception('Calidad de imagen insuficiente para procesamiento');
            }

            // PASO 2: Si validó OK, extraer información completa de la factura
            $extractedData = $this->extractInvoiceData($base64, $document->mime_type, $apiKey, $model);

            // Actualizar documento con todos los datos extraídos
            $document->update([
                'ocr_status' => 'completed',
                'is_invoice' => true,
                'quality_status' => 'good',
                'ocr_data' => $extractedData,
                'amount' => $extractedData['amount'] ?? null,
                'document_date' => $extractedData['document_date'] ?? null,
                'issuer' => $extractedData['issuer'] ?? null,
                'recipient' => $extractedData['recipient'] ?? null,
                'invoice_number' => $extractedData['invoice_number'] ?? null,
                'invoice_series' => $extractedData['invoice_series'] ?? null,
                'tax_base' => $extractedData['tax_base'] ?? null,
                'tax_rate' => $extractedData['tax_rate'] ?? null,
                'tax_amount' => $extractedData['tax_amount'] ?? null,
                'total_with_tax' => $extractedData['total_with_tax'] ?? null,
                'currency' => $extractedData['currency'] ?? 'EUR',
            ]);

            // Registrar uso de IA (2 llamadas: validación + extracción)
            $this->recordAiUsage($document->tenant_id, $document->id, $provider, 2);

            return $document;

        } catch (\Exception $e) {
            $document->update([
                'ocr_status' => 'failed',
                'ocr_data' => ['error' => $e->getMessage()]
            ]);

            throw $e;
        }
    }

    /**
     * Validar si el documento es una factura y verificar calidad
     */
    protected function validateDocument($base64, $mimeType, $apiKey, $model)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un experto en validación de documentos fiscales. Analiza si el documento es una factura válida y evalúa su calidad.'
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Analiza este documento y responde en JSON con:
1. "is_invoice": true/false (si es una factura/invoice válida con datos fiscales)
2. "quality": "good"/"poor"/"unreadable" (calidad de legibilidad)
3. "rejection_reason": texto explicando por qué no es factura o calidad insuficiente (si aplica)

Criterios para ser factura:
- Debe tener emisor, receptor, importe, fecha
- Debe ser documento fiscal/comercial (no fotos personales, capturas de pantalla, memes, etc.)
- Si es recibo, extracto bancario, nota de entrega sin datos fiscales → NO es factura

Responde SOLO con JSON válido.'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 300,
        ]);

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Error en validación de documento');
        }

        $validation = json_decode($data['choices'][0]['message']['content'], true);

        // Asegurar que siempre devuelve estructura válida
        return [
            'is_invoice' => $validation['is_invoice'] ?? false,
            'quality' => $validation['quality'] ?? 'unreadable',
            'rejection_reason' => $validation['rejection_reason'] ?? 'No se pudo determinar el tipo de documento'
        ];
    }

    /**
     * Extraer datos completos de la factura incluyendo desglose de IVA
     */
    protected function extractInvoiceData($base64, $mimeType, $apiKey, $model)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un experto contador especializado en facturas de España e Hispanoamérica. Extrae información fiscal con precisión.'
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Extrae de esta factura la siguiente información en formato JSON:

{
  "invoice_number": "número de factura",
  "invoice_series": "serie de la factura (si existe)",
  "document_date": "fecha en formato YYYY-MM-DD",
  "issuer": "nombre del emisor/proveedor",
  "recipient": "nombre del receptor/cliente",
  "currency": "código de moneda ISO (EUR, USD, MXN, etc.)",
  "tax_base": número decimal (base imponible SIN IVA),
  "tax_rate": número decimal (% de IVA/VAT aplicado, ej: 21.00),
  "tax_amount": número decimal (importe del IVA/VAT),
  "total_with_tax": número decimal (total FINAL con IVA incluido),
  "amount": número decimal (igual que total_with_tax),
  "concept": "descripción breve del servicio/producto"
}

IMPORTANTE:
- tax_base + tax_amount = total_with_tax
- Si no hay IVA explícito, intenta calcularlo a partir del total
- Usa punto decimal (no comas) para números
- Si un campo no existe, usa null

Responde SOLO con JSON válido, sin texto adicional.'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 1000,
        ]);

        $data = $response->json();

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Error en extracción de datos');
        }

        $extracted = json_decode($data['choices'][0]['message']['content'], true);

        if (!$extracted) {
            throw new \Exception('No se pudo parsear la respuesta de IA');
        }

        return $extracted;
    }

    protected function recordAiUsage($tenantId, $documentId, $provider, $apiCalls = 1)
    {
        $year = date('Y');
        $month = date('n');

        $usage = AiUsage::firstOrCreate(
            ['tenant_id' => $tenantId, 'year' => $year, 'month' => $month],
            ['documents_processed' => 0, 'api_calls' => 0, 'cost' => 0, 'provider' => $provider]
        );

        $usage->increment('documents_processed');
        $usage->increment('api_calls', $apiCalls);
        $usage->increment('cost', 0.001 * $apiCalls); // Costo estimado por llamada
    }
}
