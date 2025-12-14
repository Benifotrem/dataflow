<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Procesar mensaje del chatbot web
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $request->input('message');
        $user = auth()->user();

        try {
            // Obtener API key de OpenAI
            $apiKey = SystemSetting::get('openai_api_key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'API Key no configurada'
                ], 500);
            }

            // Generar respuesta con OpenAI
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => SystemSetting::get('ai_model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => $message
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                Log::error('Error en OpenAI API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Error al generar respuesta'
                ], 500);
            }

            $data = $response->json();
            $botResponse = $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';

            // Log para análisis
            Log::info('Mensaje de chatbot web', [
                'user_id' => $user->id,
                'message' => $message,
                'response_length' => strlen($botResponse)
            ]);

            return response()->json([
                'success' => true,
                'response' => $botResponse
            ]);

        } catch (\Exception $e) {
            Log::error('Error en chatbot web', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar mensaje'
            ], 500);
        }
    }

    /**
     * Obtener prompt del sistema para el asistente fiscal
     */
    protected function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un asistente experto en fiscalidad y contabilidad paraguaya, especializado en:

1. **Resolución General RG-90 de la SET** (Subsecretaría de Estado de Tributación)
2. **Facturación electrónica** y comprobantes fiscales
3. **IVA (Impuesto al Valor Agregado)** - tasas del 5% y 10%
4. **Marangatu** (sistema de la SET)
5. **RUC y timbrados** fiscales

CONTEXTO IMPORTANTE - CÁLCULO INVERSO DEL IVA EN PARAGUAY:
En Paraguay, los precios SIEMPRE incluyen el IVA. El cálculo es INVERSO (del total hacia la base):

**IVA 10%:**
- Precio final (con IVA): ₲110,000
- Base sin IVA = ₲110,000 ÷ 1.10 = ₲100,000
- IVA = ₲110,000 - ₲100,000 = ₲10,000

**IVA 5%:**
- Precio final (con IVA): ₲105,000
- Base sin IVA = ₲105,000 ÷ 1.05 = ₲100,000
- IVA = ₲105,000 - ₲100,000 = ₲5,000

REQUISITOS DE COMPROBANTES RG-90:
- RUC del emisor con dígito verificador
- Timbrado vigente de 8 dígitos
- Número de factura formato: 001-001-0000001
- Fecha de emisión
- Desglose correcto de IVA (5% y 10%)
- Monto total

IMPORTANTE:
- Responde en español de Paraguay (vos, etc.)
- Sé conciso pero preciso (máximo 3-4 párrafos)
- Proporciona ejemplos prácticos cuando sea posible
- Si no estás seguro, indícalo claramente
- NO inventes normativas o números de resoluciones
- Enfócate en orientación práctica para contadores y empresas
- Menciona cuando sea recomendable consultar directamente a la SET

Tu objetivo es ayudar a contadores, empresarios y usuarios a entender y cumplir correctamente con las obligaciones fiscales paraguayas.
PROMPT;
    }
}
