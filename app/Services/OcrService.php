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

            // Llamada a OpenAI
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente experto en contabilidad. Extrae información de documentos contables (facturas, recibos, extractos) y devuelve los datos en formato JSON estructurado.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Extrae del siguiente documento: importe total, importe de IVA/VAT, fecha del documento, emisor, receptor, concepto. Devuelve solo JSON.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$document->mime_type};base64,{$base64}"
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 1000,
            ]);

            $data = $response->json();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Error en respuesta de IA');
            }

            $extractedData = json_decode($data['choices'][0]['message']['content'], true);

            // Actualizar documento
            $document->update([
                'ocr_status' => 'completed',
                'ocr_data' => $extractedData,
                'amount' => $extractedData['total'] ?? null,
                'document_date' => $extractedData['date'] ?? null,
                'issuer' => $extractedData['issuer'] ?? null,
                'recipient' => $extractedData['recipient'] ?? null,
            ]);

            // Registrar uso de IA
            $this->recordAiUsage($document->tenant_id, $document->id, $provider);

            return $document;

        } catch (\Exception $e) {
            $document->update([
                'ocr_status' => 'failed',
                'ocr_data' => ['error' => $e->getMessage()]
            ]);

            throw $e;
        }
    }

    protected function recordAiUsage($tenantId, $documentId, $provider)
    {
        $year = date('Y');
        $month = date('n');

        $usage = AiUsage::firstOrCreate(
            ['tenant_id' => $tenantId, 'year' => $year, 'month' => $month],
            ['documents_processed' => 0, 'api_calls' => 0, 'cost' => 0, 'provider' => $provider]
        );

        $usage->increment('documents_processed');
        $usage->increment('api_calls');
        $usage->increment('cost', 0.001); // Costo estimado por documento
    }
}
