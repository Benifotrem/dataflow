<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramService
{
    protected Api $telegram;
    protected string $botToken;
    protected string $botUsername;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->botUsername = config('services.telegram.bot_username');

        $this->telegram = new Api($this->botToken);
    }

    /**
     * Configurar el webhook de Telegram
     */
    public function setWebhook(string $url): array
    {
        try {
            $response = $this->telegram->setWebhook([
                'url' => $url,
                'allowed_updates' => ['message', 'callback_query'],
            ]);

            Log::info('Telegram webhook configurado', ['url' => $url, 'response' => $response]);

            return [
                'success' => true,
                'message' => 'Webhook configurado correctamente',
                'data' => $response,
            ];
        } catch (TelegramSDKException $e) {
            Log::error('Error al configurar webhook de Telegram', [
                'error' => $e->getMessage(),
                'url' => $url,
            ]);

            return [
                'success' => false,
                'message' => 'Error al configurar webhook: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener información del webhook actual
     */
    public function getWebhookInfo(): array
    {
        try {
            $response = $this->telegram->getWebhookInfo();

            return [
                'success' => true,
                'data' => $response,
            ];
        } catch (TelegramSDKException $e) {
            Log::error('Error al obtener información del webhook', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al obtener información del webhook: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Eliminar el webhook
     */
    public function deleteWebhook(): array
    {
        try {
            $response = $this->telegram->deleteWebhook();

            Log::info('Webhook de Telegram eliminado');

            return [
                'success' => true,
                'message' => 'Webhook eliminado correctamente',
                'data' => $response,
            ];
        } catch (TelegramSDKException $e) {
            Log::error('Error al eliminar webhook de Telegram', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al eliminar webhook: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Enviar mensaje de texto
     */
    public function sendMessage(int $chatId, string $text, array $options = []): ?int
    {
        try {
            $params = array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ], $options);

            $response = $this->telegram->sendMessage($params);

            Log::info('Mensaje de Telegram enviado', [
                'chat_id' => $chatId,
                'message_id' => $response->getMessageId(),
            ]);

            return $response->getMessageId();
        } catch (TelegramSDKException $e) {
            Log::error('Error al enviar mensaje de Telegram', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Enviar mensaje con teclado inline
     */
    public function sendMessageWithKeyboard(int $chatId, string $text, array $keyboard): ?int
    {
        try {
            $response = $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);

            return $response->getMessageId();
        } catch (TelegramSDKException $e) {
            Log::error('Error al enviar mensaje con teclado', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Descargar archivo de Telegram
     */
    public function downloadFile(string $fileId): ?array
    {
        try {
            $file = $this->telegram->getFile(['file_id' => $fileId]);
            $filePath = $file->getFilePath();

            $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
            $fileContent = Http::get($fileUrl)->body();

            if (empty($fileContent)) {
                Log::error('Archivo vacío descargado de Telegram', ['file_id' => $fileId]);
                return null;
            }

            Log::info('Archivo descargado de Telegram', [
                'file_id' => $fileId,
                'file_path' => $filePath,
                'size' => strlen($fileContent),
            ]);

            return [
                'content' => $fileContent,
                'path' => $filePath,
                'size' => $file->getFileSize(),
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
            ];
        } catch (TelegramSDKException $e) {
            Log::error('Error al descargar archivo de Telegram', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Obtener información del bot
     */
    public function getMe(): ?array
    {
        try {
            $response = $this->telegram->getMe();

            return [
                'id' => $response->getId(),
                'first_name' => $response->getFirstName(),
                'username' => $response->getUsername(),
            ];
        } catch (TelegramSDKException $e) {
            Log::error('Error al obtener información del bot', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Buscar usuario por telegram_id
     */
    public function findUserByTelegramId(int $telegramId): ?User
    {
        return User::where('telegram_id', $telegramId)->first();
    }

    /**
     * Generar código de vinculación único
     */
    public function generateLinkCode(User $user): string
    {
        $code = strtoupper(substr(md5($user->id . time() . $user->email), 0, 8));

        cache()->put("telegram_link_code:{$code}", $user->id, now()->addMinutes(15));

        return $code;
    }

    /**
     * Verificar código de vinculación
     */
    public function verifyLinkCode(string $code): ?int
    {
        $userId = cache()->get("telegram_link_code:{$code}");

        if ($userId) {
            cache()->forget("telegram_link_code:{$code}");
        }

        return $userId;
    }

    /**
     * Validar que el archivo es permitido (PDF o imagen)
     */
    public function isAllowedFileType(string $mimeType): bool
    {
        $allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];

        return in_array($mimeType, $allowedTypes);
    }

    /**
     * Obtener el mime type desde la extensión
     */
    public function getMimeTypeFromExtension(string $extension): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Formatear información de documento para el usuario
     */
    public function formatDocumentInfo(array $ocrData): string
    {
        $lines = [];
        $lines[] = "📄 <b>Factura procesada exitosamente</b>\n";

        if (!empty($ocrData['issuer'])) {
            $lines[] = "👤 <b>Emisor:</b> {$ocrData['issuer']}";
        }

        if (!empty($ocrData['amount'])) {
            $lines[] = "💰 <b>Monto:</b> {$ocrData['amount']}";
        }

        if (!empty($ocrData['date'])) {
            $lines[] = "📅 <b>Fecha:</b> {$ocrData['date']}";
        }

        if (!empty($ocrData['concept'])) {
            $lines[] = "📝 <b>Concepto:</b> {$ocrData['concept']}";
        }

        return implode("\n", $lines);
    }

    /**
     * Enviar acción de chat (typing, upload_document, etc.)
     */
    public function sendChatAction(int $chatId, string $action = 'typing'): void
    {
        try {
            $this->telegram->sendChatAction([
                'chat_id' => $chatId,
                'action' => $action,
            ]);
        } catch (TelegramSDKException $e) {
            // No es crítico si falla
            Log::warning('Error al enviar acción de chat', [
                'chat_id' => $chatId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Responder a un callback query
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = null): void
    {
        try {
            $params = ['callback_query_id' => $callbackQueryId];

            if ($text) {
                $params['text'] = $text;
            }

            $this->telegram->answerCallbackQuery($params);
        } catch (TelegramSDKException $e) {
            Log::error('Error al responder callback query', [
                'callback_query_id' => $callbackQueryId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
