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
Analiza esta FACTURA PARAGUAYA (formato RG-90 de la SET) y extrae TODOS los datos fiscales que encuentres.

⚠️ REGLAS CRÍTICAS:
1. LEE DIRECTAMENTE las casillas y campos del documento - NO CALCULES NADA
2. NO hagas sumas, restas ni operaciones matemáticas
3. Si un campo tiene un valor escrito, cópialo exactamente como aparece
4. Si una casilla está vacía o ilegible, usa `null`
5. Extrae TODOS los datos visibles para que el usuario pueda revisarlos

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
  "ruc_receptor": "RUC del cliente/receptor si está visible",
  "razon_social_receptor": "Nombre del cliente/receptor si está visible",

⚠️⚠️⚠️ ADVERTENCIA CRÍTICA SOBRE MONTOS ⚠️⚠️⚠️

🚫 NO CALCULES - SOLO LEE LO QUE ESTÁ ESCRITO

Cada casilla de la factura tiene un número DIFERENTE escrito. Debes leer cada una INDEPENDIENTEMENTE.

❌ ERROR FRECUENTE: Ver que el TOTAL es 90.000 y pensar:
   "El IVA debe ser 10% de 90.000, entonces es 9.000"
   ¡ESTO ESTÁ MAL! Debes LEER qué dice la casilla de IVA, no calcularlo.

✅ CORRECTO: Buscar la casilla que dice "IVA 10%" y leer el número que tiene escrito.
   Puede decir "8.181" (que es diferente de 9.000)

MONTOS - PROCESO DE LECTURA PASO A PASO:
Para CADA casilla individualmente:
1. Ubica visualmente la casilla específica (ej: busca donde dice "Gravado 10%")
2. Lee el número que está ESCRITO en esa casilla (no el de otra casilla)
3. Copia TODOS los dígitos, quitando solo puntos y símbolos
4. NO relaciones este número con otros - cada casilla es independiente

Ejemplo:
- Si la casilla "Gravado 10%" dice "81.819" → extraes 81819 (NO 90000, NO 82000)
- Si la casilla "IVA 10%" dice "8.181" → extraes 8181 (NO 9000, NO 8000)
- Si la casilla "TOTAL" dice "90.000" → extraes 90000

⚠️ UBICACIÓN DE CASILLAS EN LA FACTURA:
En facturas paraguayas, las casillas de montos están en DIFERENTES lugares:
- "Gravado 10%" está en una fila ANTES del total (número más pequeño, ej: 81.819)
- "IVA 10%" está en otra fila (número pequeño, ej: 8.181)
- "TOTAL" está al FINAL, en letra grande o negrita (número más grande, ej: 90.000)

❌ NO CONFUNDAS:
- "Gravado 10%" NO es lo mismo que "TOTAL"
- Si el Gravado 10% = TOTAL, estás leyendo la casilla equivocada

Extrae estos campos (cada uno de su casilla correspondiente):
  "subtotal_gravado_5": "Casilla 'Gravado 5%' o 'Sub Total 5%' (ANTES del total, número diferente al total)",
  "subtotal_gravado_10": "Casilla 'Gravado 10%' o 'Sub Total 10%' (ANTES del total, será MENOR que el total, ej: si total=90000 entonces gravado≈81819)",
  "subtotal_exentas": "Casilla 'Exentas' (solo si tiene productos exentos)",
  "iva_5": "Casilla 'IVA 5%' (número pequeño, diferente al total)",
  "iva_10": "Casilla 'IVA 10%' (número pequeño, diferente al total, ej: 8181 si total=90000)",
  "total_iva": "Casilla 'Total IVA' (suma de IVAs, similar al iva_10 si solo hay 10%)",
  "monto_total": "Casilla 'TOTAL' o 'Total a Pagar' (el número MÁS GRANDE, al final, en negrita)",

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
  "moneda": "PYG (guaraníes) por defecto, o USD si dice dólares",
  "observaciones": "Cualquier observación, nota o comentario visible en la factura",
  "calidad_imagen": "ALTA, MEDIA o BAJA - evalúa qué tan legible está la imagen"
}

📝 FORMATO DE NÚMEROS PARAGUAYOS - LEE CON ATENCIÓN:

⚠️ CRÍTICO: En facturas paraguayas, el PUNTO (.) es separador de miles, NO decimal.

EJEMPLOS REALES de cómo leer números:
┌─────────────────────┬──────────────────────────┬────────────────┐
│ Lo que VES escrito  │ Qué significa            │ Cómo escribirlo│
├─────────────────────┼──────────────────────────┼────────────────┤
│ "90.000"            │ Noventa mil guaraníes    │ 90000          │
│ "1.500.000"         │ Un millón quinientos mil │ 1500000        │
│ "81.819"            │ Ochenta y un mil...      │ 81819          │
│ "8.181"             │ Ocho mil ciento ochenta  │ 8181           │
│ "180"               │ Ciento ochenta           │ 180            │
│ "₲ 90.000"          │ Noventa mil guaraníes    │ 90000          │
│ "Gs. 1.234.567"     │ Un millón...             │ 1234567        │
└─────────────────────┴──────────────────────────┴────────────────┘

🚨 ERROR COMÚN que debes EVITAR:
❌ NO confundas "90.000" con "90" - SON DIFERENTES
   "90.000" = noventa MIL (90000)
   "90" = noventa (90)

REGLAS PARA LEER NÚMEROS:
1. Lee el número COMPLETO, incluyendo TODOS los dígitos
2. Quita TODOS los puntos que separan miles
3. Quita símboos monetarios (₲, Gs., PYG)
4. Si tiene coma decimal (raro en Paraguay), reemplázala por punto
5. El resultado debe ser un número entero SIN separadores

VALIDACIÓN:
- Si extraes menos de 1000 de una casilla de "TOTAL", probablemente estés leyendo MAL
- Las facturas paraguayas suelen tener montos de miles o millones de guaraníes
- Si ves 3 dígitos después del punto (ej: 90.000), ese punto separa miles

🚫 NO HAGAS:
- NO sumes IVAs para obtener totales
- NO calcules el subtotal sumando items
- NO multipliques cantidad × precio
- SOLO lee lo que está escrito en cada casilla

✅ EJEMPLO COMPLETO DE EXTRACCIÓN CORRECTA:

Imagina que en la factura ves estas casillas:
┌──────────────────┬──────────┐
│ Gravado 10%      │ 81.819   │ ← Esta casilla dice 81.819
│ IVA 10%          │ 8.181    │ ← Esta casilla dice 8.181
│ TOTAL            │ 90.000   │ ← Esta casilla dice 90.000
└──────────────────┴──────────┘

✅ EXTRACCIÓN CORRECTA:
{
  "subtotal_gravado_10": 81819,   ← Leí 81.819 de la casilla "Gravado 10%"
  "iva_10": 8181,                  ← Leí 8.181 de la casilla "IVA 10%"
  "monto_total": 90000             ← Leí 90.000 de la casilla "TOTAL"
}

❌ ERRORES COMUNES (NO hagas esto):
{
  "subtotal_gravado_10": 90000,   ← ERROR: Copió el TOTAL en lugar de leer la casilla Gravado 10%
  "iva_10": 9000,                  ← ERROR: Calculó 10% de 90.000 en lugar de leer la casilla IVA 10%
  "monto_total": 90000             ← Correcto
}

O:
{
  "subtotal_gravado_10": 81,      ← ERROR: Solo leyó parte del número (falta 819)
  "iva_10": 8,                     ← ERROR: Solo leyó parte del número (falta 181)
  "monto_total": 90                ← ERROR: Solo leyó parte del número (falta 000)
}

🔍 AUTO-VERIFICACIÓN antes de responder:
1. ¿Leí directamente cada casilla SIN calcular nada?
2. ¿Los números son DIFERENTES entre sí?
3. ¿El subtotal_gravado_10 es MENOR que monto_total? (si son iguales, ERROR)
4. ¿El IVA 10% es diferente a 9000 o 9% del total? (si es 9000 o 10% exacto, ERROR)
5. ¿Leí TODOS los dígitos de cada número? (ej: 81819, no 81)
6. ¿Los números tienen dígitos "raros" como 81819, 8181? (señal de lectura correcta)
7. ¿Quité puntos separadores y símbolos (₲, Gs.)?

⚠️ SEÑALES DE ERROR - Revisa si:
- subtotal_gravado_10 = monto_total (están iguales) ← ❌ ERROR GRAVE
- iva_10 es exactamente 10% del monto_total ← ❌ Calculaste
- subtotal_gravado_10 + iva_10 = monto_total exactamente ← ⚠️ Probablemente correcto
- Todos los números son redondos (90000, 9000) ← ⚠️ Probablemente calculaste

✅ SEÑALES DE ÉXITO:
- subtotal_gravado_10 ≠ monto_total (son diferentes)
- Números tienen dígitos "raros": 81819, 8181 (no redondos)
- subtotal_gravado_10 + iva_10 ≈ monto_total (suma aproximada)

✅ DEVUELVE:
SOLO el objeto JSON completo con TODOS los campos extraídos. Sin texto antes o después.
Si la imagen no es una factura o es completamente ilegible, devuelve: {"error": "descripción del problema"}

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
