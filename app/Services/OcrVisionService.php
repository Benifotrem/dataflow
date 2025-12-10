<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SystemSetting;

/**
 * Servicio especializado de OCR con OpenAI Vision API
 * para extracción de datos de facturas paraguayas según RG-90
 */
class OcrVisionService
{
    /**
     * Timeout para las peticiones a OpenAI (en segundos)
     */
    protected int $timeout = 60;

    /**
     * API Key de OpenAI
     */
    protected ?string $apiKey;

    /**
     * Modelo de OpenAI a usar
     */
    protected string $model;

    public function __construct()
    {
        $this->apiKey = SystemSetting::get('openai_api_key');
        $this->model = SystemSetting::get('ai_model', 'gpt-4o-mini');
    }

    /**
     * Extraer datos de factura paraguaya según RG-90
     *
     * @param string $base64Image Imagen en base64
     * @param string $mimeType Tipo MIME (image/jpeg, image/png, etc.)
     * @param string $promptContext Contexto adicional opcional
     * @return array Array con datos extraídos o errores
     */
    public function extractInvoiceData(string $base64Image, string $mimeType = 'image/jpeg', string $promptContext = ''): array
    {
        if (!$this->apiKey) {
            throw new \Exception('API Key de OpenAI no configurada');
        }

        try {
            // Construir el prompt específico para facturas paraguayas
            $prompt = $this->buildParaguayanInvoicePrompt($promptContext);

            Log::info('Iniciando extracción OCR con OpenAI Vision', [
                'model' => $this->model,
                'image_size' => strlen($base64Image),
            ]);

            // Llamar a OpenAI Vision API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout($this->timeout)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un experto en contabilidad paraguaya y extracción de datos fiscales. Extraes información de facturas con alta precisión y siempre devuelves JSON válido.',
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt,
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$base64Image}",
                                        'detail' => 'high', // Alta resolución para mejor precisión
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 1500,
                    'temperature' => 0.1, // Baja temperatura para respuestas más deterministas
                ]);

            if (!$response->successful()) {
                throw new \Exception('Error en OpenAI API: ' . $response->status() . ' - ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Respuesta de OpenAI inválida: no se encontró contenido');
            }

            $content = $data['choices'][0]['message']['content'];

            Log::info('Respuesta de OpenAI recibida', [
                'content_length' => strlen($content),
            ]);

            // Parsear JSON de la respuesta
            $extractedData = $this->parseOpenAIResponse($content);

            // Validar que tenga los campos requeridos
            $validationResult = $this->validateExtractedData($extractedData);

            return [
                'success' => $validationResult['valid'],
                'data' => $extractedData,
                'validation' => $validationResult,
                'raw_response' => $content,
            ];

        } catch (\Exception $e) {
            Log::error('Error en extracción OCR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Construir prompt específico para facturas paraguayas (RG-90)
     */
    protected function buildParaguayanInvoicePrompt(string $context = ''): string
    {
        $basePrompt = <<<'PROMPT'
Analiza la imagen de la factura paraguaya y extrae la siguiente información fiscal según la RG-90 de la SET (Subsecretaría de Estado de Tributación).

IMPORTANTE: Devuelve ÚNICAMENTE un objeto JSON válido con los siguientes campos. Si un campo no es legible o no existe en la imagen, usa `null`.

Campos requeridos:
{
  "ruc_emisor": "RUC del emisor (formato: 12345678-9, solo números sin guiones)",
  "razon_social_emisor": "Razón social o nombre del emisor",
  "timbrado": "Número de timbrado (8 dígitos)",
  "fecha_emision": "Fecha de emisión (formato: YYYY-MM-DD)",
  "numero_factura": "Número de factura completo (ej: 001-001-0012345)",
  "serie": "Serie de la factura si existe",
  "condicion_venta": "Condición de venta: CONTADO o CREDITO",
  "tipo_factura": "Tipo: FACTURA, BOLETA, AUTOFACTURA, etc.",

  "subtotal": "Subtotal/Base imponible (número decimal, sin símbolos)",
  "iva_5": "IVA 5% (número decimal, null si no aplica)",
  "iva_10": "IVA 10% (número decimal, null si no aplica)",
  "total_iva": "Total IVA (suma de todos los IVAs)",
  "monto_total": "Monto total de la factura (número decimal)",

  "moneda": "Código de moneda (PYG, USD, etc.)",
  "items": [
    {
      "descripcion": "Descripción del producto/servicio",
      "cantidad": "Cantidad (número)",
      "precio_unitario": "Precio unitario (número decimal)",
      "subtotal": "Subtotal del ítem (número decimal)"
    }
  ],

  "observaciones": "Observaciones o notas adicionales si existen",
  "calidad_imagen": "ALTA, MEDIA o BAJA - evalúa la legibilidad de la imagen"
}

REGLAS IMPORTANTES:
1. Devuelve SOLO el JSON, sin texto adicional antes o después
2. Todos los números deben ser sin separadores de miles, con punto decimal (ej: 1500000.50)
3. El RUC debe ser solo números, sin guiones
4. La fecha debe ser YYYY-MM-DD
5. Si la imagen es ilegible o no es una factura, devuelve: {"error": "descripción del problema"}

PROMPT;

        if ($context) {
            $basePrompt .= "\n\nCONTEXTO ADICIONAL:\n" . $context;
        }

        return $basePrompt;
    }

    /**
     * Parsear respuesta de OpenAI (extraer JSON)
     */
    protected function parseOpenAIResponse(string $content): ?array
    {
        // Limpiar la respuesta (a veces OpenAI incluye markdown ```json ... ```)
        $content = trim($content);

        // Remover bloques de código markdown si existen
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $content, $matches)) {
            $content = $matches[1];
        }

        // Intentar decodificar JSON
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Error al parsear JSON de OpenAI', [
                'json_error' => json_last_error_msg(),
                'content' => substr($content, 0, 500),
            ]);

            throw new \Exception('La respuesta de OpenAI no es JSON válido: ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Validar datos extraídos
     */
    protected function validateExtractedData(?array $data): array
    {
        if (!$data) {
            return [
                'valid' => false,
                'errors' => ['No se pudieron extraer datos de la imagen'],
            ];
        }

        // Si hay un error explícito en la respuesta
        if (isset($data['error'])) {
            return [
                'valid' => false,
                'errors' => [$data['error']],
            ];
        }

        $errors = [];
        $warnings = [];

        // Campos críticos para factura paraguaya
        $criticalFields = [
            'ruc_emisor' => 'RUC del emisor',
            'timbrado' => 'Timbrado',
            'fecha_emision' => 'Fecha de emisión',
            'monto_total' => 'Monto total',
        ];

        foreach ($criticalFields as $field => $label) {
            if (empty($data[$field]) || $data[$field] === null) {
                $errors[] = "Campo obligatorio faltante: {$label}";
            }
        }

        // Validar formato de RUC
        if (isset($data['ruc_emisor'])) {
            $ruc = preg_replace('/[^0-9]/', '', $data['ruc_emisor']);
            if (strlen($ruc) < 6 || strlen($ruc) > 10) {
                $errors[] = 'Formato de RUC inválido';
            }
        }

        // Validar formato de Timbrado
        if (isset($data['timbrado'])) {
            $timbrado = preg_replace('/[^0-9]/', '', $data['timbrado']);
            if (strlen($timbrado) !== 8) {
                $errors[] = 'Formato de Timbrado inválido (debe tener 8 dígitos)';
            }
        }

        // Validar fecha
        if (isset($data['fecha_emision'])) {
            try {
                \Carbon\Carbon::parse($data['fecha_emision']);
            } catch (\Exception $e) {
                $errors[] = 'Formato de fecha inválido';
            }
        }

        // Validar monto
        if (isset($data['monto_total'])) {
            if (!is_numeric($data['monto_total']) || $data['monto_total'] <= 0) {
                $errors[] = 'Monto total inválido';
            }
        }

        // Advertencias (no críticas)
        if (!isset($data['razon_social_emisor']) || empty($data['razon_social_emisor'])) {
            $warnings[] = 'Razón social del emisor no detectada';
        }

        if (!isset($data['numero_factura']) || empty($data['numero_factura'])) {
            $warnings[] = 'Número de factura no detectado';
        }

        // Evaluar calidad de imagen
        if (isset($data['calidad_imagen']) && $data['calidad_imagen'] === 'BAJA') {
            $warnings[] = 'Calidad de imagen baja - los datos pueden ser imprecisos';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'completeness' => $this->calculateCompleteness($data),
        ];
    }

    /**
     * Calcular porcentaje de completitud de los datos
     */
    protected function calculateCompleteness(array $data): int
    {
        $totalFields = 13; // Campos principales esperados
        $filledFields = 0;

        $fields = [
            'ruc_emisor',
            'razon_social_emisor',
            'timbrado',
            'fecha_emision',
            'numero_factura',
            'condicion_venta',
            'tipo_factura',
            'subtotal',
            'total_iva',
            'monto_total',
            'moneda',
            'items',
            'calidad_imagen',
        ];

        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $filledFields++;
            }
        }

        return (int) round(($filledFields / $totalFields) * 100);
    }

    /**
     * Extraer solo campos básicos (modo rápido)
     */
    public function extractBasicData(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $quickPrompt = <<<'PROMPT'
Extrae SOLO los datos fiscales básicos de esta factura:

{
  "ruc_emisor": "RUC sin guiones",
  "timbrado": "Timbrado (8 dígitos)",
  "fecha_emision": "YYYY-MM-DD",
  "monto_total": "Monto total (número decimal)"
}

Devuelve SOLO el JSON. Si no encuentras un campo, usa null.
PROMPT;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini', // Modelo más rápido
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $quickPrompt],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$base64Image}",
                                        'detail' => 'low', // Baja resolución para velocidad
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 300,
                ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? null;
                return $this->parseOpenAIResponse($content);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error en extracción básica', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
