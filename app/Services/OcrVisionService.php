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
                    'max_tokens' => 2000, // Más tokens para respuesta completa
                    'temperature' => 0.0, // Temperatura 0 para máxima precisión y consistencia
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
Eres un OCR especializado en facturas paraguayas. Tu trabajo es COPIAR números exactamente como aparecen.

🚫 PROHIBIDO: Hacer cálculos, sumas, multiplicaciones o porcentajes
✅ PERMITIDO: Solo COPIAR los números que ves escritos

IMPORTANTE: En facturas paraguayas los números usan PUNTO como separador de miles:
- Si ves "90.000" significa 90000 (noventa mil)
- Si ves "81.819" significa 81819 (ochenta y un mil ochocientos diecinueve)
- Si ves "8.181" significa 8181 (ocho mil ciento ochenta y uno)

📋 ESTRUCTURA DE FACTURA RG-90 - Extrae estos campos:

DATOS DEL EMISOR (parte superior):
{
  "ruc_emisor": "RUC completo del emisor (incluye dígito verificador, solo números sin guiones ni espacios)",
  "razon_social_emisor": "Nombre completo o razón social del emisor",
  "direccion_emisor": "Dirección del emisor si está visible",
  "telefono_emisor": "Teléfono si está visible",

DATOS DEL COMPROBANTE:
  "timbrado": "Número de timbrado (8 dígitos)",
  "numero_factura": "Número completo de factura (ej: 001-001-0012345)",
  "fecha_emision": "Fecha de emisión (formato: YYYY-MM-DD)",
  "condicion_venta": "CONTADO o CREDITO",
  "tipo_factura": "FACTURA, CONTADO, CREDITO, etc.",

DATOS DEL RECEPTOR (si existen):
  "ruc_receptor": "RUC del cliente si está visible",
  "razon_social_receptor": "Nombre del cliente si está visible",

MONTOS (la parte más importante - lee con mucha atención):

En la factura hay varias FILAS con números. Cada fila tiene una ETIQUETA y un NÚMERO.
Tu trabajo es copiar el número que está al lado de cada etiqueta específica.

Busca estas etiquetas y copia EXACTAMENTE el número que ves a su lado:

  "subtotal_gravado_10":
     OPCIÓN 1: Si encuentras etiqueta "Gravado 10%" o "Sub Total 10%" → Copia ese número
     OPCIÓN 2: Si NO existe esa etiqueta pero hay una tabla de items con columna "IVA 10%" con un monto:
               → Calcula: (monto de columna IVA 10%) / 1.1
               Ejemplo: Si columna IVA 10% dice 90.000 → subtotal_gravado_10 = 90000 / 1.1 = 81818

  "subtotal_gravado_5":
     OPCIÓN 1: Si encuentras "Gravado 5%" → Copia ese número
     OPCIÓN 2: Si NO existe pero hay columna "IVA 5%" → Calcula: (monto columna IVA 5%) / 1.05

  "subtotal_exentas": Busca etiqueta "Exentas" en la columna de valores → Copia ese número

  "iva_10": Busca "Liquidación del IVA" o "IVA 10%" (la fila del resumen, NO la columna) → Copia ese número
  "iva_5": Busca "IVA 5%" en el resumen final → Copia ese número
  "total_iva": Busca "Total IVA" o "Liquidación del IVA (Total)" → Copia ese número
  "monto_total": Busca "TOTAL" o "Total a Pagar" (última fila) → Copia ese número

IMPORTANTE: Distingue entre:
- COLUMNAS de la tabla de items (Exentas, IVA 5%, IVA 10%) → Estos son montos con IVA incluido
- FILAS del resumen final (Liquidación del IVA) → Estos son los impuestos calculados

EJEMPLO 1 - Factura CON campo "Gravado 10%" explícito:
┌──────────────┬──────────┐
│ Gravado 10%  │  81.819  │ ← Copia 81819 para subtotal_gravado_10
│ IVA 10%      │   8.181  │ ← Copia 8181 para iva_10
│ TOTAL        │  90.000  │ ← Copia 90000 para monto_total
└──────────────┴──────────┘

EJEMPLO 2 - Factura SIN campo "Gravado 10%" (tiene tabla con columnas):
Tabla de items:
┌──────────┬────────┬─────────┬─────────┐
│ Cantidad │ Precio │ Exentas │ IVA 10% │
│    1     │ 90.000 │    0    │  90.000 │ ← Monto en columna IVA 10%
└──────────┴────────┴─────────┴─────────┘

Resumen:
│ Liquidación del IVA (10%) │  8.182  │ ← Copia 8182 para iva_10
│ Total a Pagar             │ 90.000  │ ← Copia 90000 para monto_total

Para este caso:
- subtotal_gravado_10 = 90000 / 1.1 = 81818 (calculas porque no hay etiqueta "Gravado 10%")
- iva_10 = 8182 (copias de "Liquidación del IVA")
- monto_total = 90000 (copias de "Total a Pagar")

VERIFICA tu respuesta:
- ¿subtotal_gravado_10 + iva_10 ≈ monto_total? Debe cumplirse (ej: 81818 + 8182 = 90000)
- ¿iva_10 es aproximadamente 1/10 de subtotal_gravado_10? Debe serlo
- Si calculaste el gravado: ¿es menor que el total? Debe serlo

ITEMS/PRODUCTOS (si son legibles):
  "items": [
    {
      "cantidad": "Cantidad del ítem",
      "descripcion": "Descripción completa del producto/servicio",
      "precio_unitario": "Precio unitario",
      "exentas": "Monto exento si aplica",
      "gravado_5": "Monto gravado al 5% si aplica",
      "gravado_10": "Monto gravado al 10% si aplica"
    }
  ],

OTROS DATOS:
  "moneda": "PYG",
  "observaciones": "Si hay observaciones",
  "calidad_imagen": "ALTA, MEDIA o BAJA"
}

REGLAS FINALES:
1. Quita los puntos de los números (90.000 → 90000)
2. Quita símbolos monetarios (₲, Gs.)
3. COPIA cada número de su etiqueta correspondiente
4. NO calcules porcentajes ni hagas operaciones matemáticas

Devuelve SOLO el objeto JSON con todos los campos. Sin texto adicional.

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
