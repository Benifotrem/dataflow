<?php

namespace App\Services;

use App\Models\TelegramConversation;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SystemSetting;

/**
 * Servicio para manejar conversaciones del bot de Telegram
 */
class TelegramConversationService
{
    protected ?string $apiKey;
    protected string $model;
    protected int $timeout = 30;

    public function __construct()
    {
        $this->apiKey = SystemSetting::get('openai_api_key');
        $this->model = SystemSetting::get('ai_model', 'gpt-4o-mini');
    }

    /**
     * Procesar mensaje de texto del usuario
     */
    public function processMessage(User $user, string $message, int $chatId): string
    {
        try {
            // Guardar mensaje del usuario
            $this->saveMessage($user, $chatId, 'user', $message);

            // Obtener contexto de conversación reciente
            $context = $this->getConversationContext($user, $chatId);

            // Generar respuesta con OpenAI
            $response = $this->generateResponse($message, $context);

            // Guardar respuesta del bot
            $this->saveMessage($user, $chatId, 'assistant', $response);

            return $response;

        } catch (\Exception $e) {
            Log::error('Error en conversación Telegram', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);

            return "❌ Lo siento, hubo un error al procesar tu mensaje. Por favor intenta de nuevo.";
        }
    }

    /**
     * Generar respuesta usando OpenAI
     */
    protected function generateResponse(string $message, array $context): string
    {
        if (!$this->apiKey) {
            throw new \Exception('API Key de OpenAI no configurada');
        }

        $systemPrompt = $this->getSystemPrompt();

        // Construir mensajes para OpenAI
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Agregar contexto de conversación
        foreach ($context as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Agregar mensaje actual
        $messages[] = ['role' => 'user', 'content' => $message];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])
            ->timeout($this->timeout)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error en OpenAI API: ' . $response->status());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';
    }

    /**
     * Obtener contexto de conversación (últimos 10 mensajes)
     */
    protected function getConversationContext(User $user, int $chatId, int $limit = 10): array
    {
        $messages = TelegramConversation::where('user_id', $user->id)
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->message,
                ];
            })
            ->toArray();

        return $messages;
    }

    /**
     * Guardar mensaje en la base de datos
     */
    protected function saveMessage(User $user, int $chatId, string $role, string $message): void
    {
        TelegramConversation::create([
            'user_id' => $user->id,
            'chat_id' => $chatId,
            'role' => $role,
            'message' => $message,
        ]);
    }

    /**
     * Obtener prompt del sistema para el bot
     */
    protected function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un asistente experto en contabilidad paraguaya, especializado en la Resolución General RG-90 de la SET (Subsecretaría de Estado de Tributación de Paraguay).

Tu función es ayudar a contadores y empresas con:
1. Registro de comprobantes según RG-90
2. Interpretación de requisitos fiscales paraguayos
3. Validación de facturas y comprobantes
4. Explicación de normativas de la SET
5. Buenas prácticas contables en Paraguay

IMPORTANTE:
- Responde en español de Paraguay
- Sé conciso pero preciso
- Si no estás seguro de algo relacionado con normativas, indícalo claramente
- Proporciona ejemplos prácticos cuando sea posible
- Usa emojis moderadamente para hacer las respuestas más amigables

CONTEXTO RG-90:
La RG-90 establece los requisitos para el registro de comprobantes de ingresos, ventas, egresos y compras en el sistema Marangatu de la SET. Los comprobantes deben incluir:
- RUC del emisor con dígito verificador
- Timbrado vigente de 8 dígitos
- Número de factura con formato estándar (001-001-0000001)
- Fecha de emisión
- Desglose de IVA (5% y 10%)
- Monto total

Responde de manera profesional pero accesible.
PROMPT;
    }

    /**
     * Exportar conversación completa de un usuario
     */
    public function exportConversation(User $user, int $chatId): array
    {
        $messages = TelegramConversation::where('user_id', $user->id)
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'timestamp' => $msg->created_at->format('Y-m-d H:i:s'),
                    'role' => $msg->role === 'user' ? 'Usuario' : 'Asistente',
                    'message' => $msg->message,
                ];
            })
            ->toArray();

        return [
            'user' => $user->name,
            'email' => $user->email,
            'chat_id' => $chatId,
            'messages' => $messages,
            'total_messages' => count($messages),
        ];
    }
}
