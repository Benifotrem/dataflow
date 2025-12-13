<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\OcrInvoiceProcessingJob;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PagoParService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Manejar webhook de Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();

            Log::info('Webhook de Telegram recibido', ['update' => $update]);

            // Procesar mensaje
            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            }

            // Procesar callback query
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Error en webhook de Telegram', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Manejar mensajes de Telegram
     */
    protected function handleMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $telegramId = $message['from']['id'];
        $username = $message['from']['username'] ?? null;

        // Manejar comandos
        if (isset($message['text']) && str_starts_with($message['text'], '/')) {
            $this->handleCommand($message['text'], $chatId, $telegramId, $username);
            return;
        }

        // Verificar si el usuario está vinculado
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ <b>Cuenta no vinculada</b>\n\n" .
                "Para usar este bot, primero debes vincular tu cuenta de Dataflow.\n\n" .
                "Usa el comando /link para obtener instrucciones."
            );
            return;
        }

        // Manejar documentos (PDF o imágenes)
        if (isset($message['document']) || isset($message['photo'])) {
            $this->handleDocument($message, $user);
            return;
        }

        // Mensaje de texto sin comando
        if (isset($message['text'])) {
            // Verificar si es un código de vinculación
            if ($this->isLinkCode($message['text'])) {
                $this->processLinkCode($message['text'], $chatId, $telegramId, $username);
                return;
            }

            $this->telegramService->sendMessage(
                $chatId,
                "ℹ️ Envíame un documento (PDF o imagen) de una factura para procesarlo.\n\n" .
                "Comandos disponibles:\n" .
                "/start - Iniciar el bot\n" .
                "/help - Ver ayuda\n" .
                "/link - Vincular cuenta\n" .
                "/status - Ver estado de tu cuenta"
            );
        }
    }

    /**
     * Manejar comandos del bot
     */
    protected function handleCommand(string $command, int $chatId, int $telegramId, ?string $username)
    {
        $command = strtolower(trim(explode(' ', $command)[0]));

        switch ($command) {
            case '/start':
                $this->commandStart($chatId);
                break;

            case '/help':
                $this->commandHelp($chatId);
                break;

            case '/link':
                $this->commandLink($chatId, $telegramId);
                break;

            case '/unlink':
                $this->commandUnlink($chatId, $telegramId);
                break;

            case '/status':
                $this->commandStatus($chatId, $telegramId);
                break;

            case '/pagar':
                $this->commandPagar($chatId, $telegramId);
                break;

            case '/suscripcion':
                $this->commandSuscripcion($chatId, $telegramId);
                break;

            default:
                $this->telegramService->sendMessage(
                    $chatId,
                    "❌ Comando no reconocido. Usa /help para ver los comandos disponibles."
                );
        }
    }

    /**
     * Comando /start
     */
    protected function commandStart(int $chatId)
    {
        $message = "🤖 <b>¡Bienvenido al Bot de Dataflow!</b>\n\n" .
            "Soy tu asistente para la gestión automática de facturas.\n\n" .
            "<b>¿Qué puedo hacer?</b>\n" .
            "✅ Recibir facturas en PDF o imagen\n" .
            "✅ Extraer datos automáticamente con IA\n" .
            "✅ Organizar facturas por emisor, año y mes\n" .
            "✅ Notificarte cuando el procesamiento termine\n\n" .
            "<b>Para empezar:</b>\n" .
            "1. Vincula tu cuenta con /link\n" .
            "2. Envía tus facturas\n" .
            "3. ¡Listo! Recibirás una notificación cuando estén procesadas\n\n" .
            "Usa /help para ver todos los comandos disponibles.";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Comando /help
     */
    protected function commandHelp(int $chatId)
    {
        $message = "📚 <b>Comandos disponibles:</b>\n\n" .
            "<b>📱 General:</b>\n" .
            "/start - Iniciar el bot\n" .
            "/help - Ver esta ayuda\n" .
            "/link - Vincular tu cuenta de Dataflow\n" .
            "/unlink - Desvincular tu cuenta\n" .
            "/status - Ver el estado de tu cuenta\n\n" .
            "<b>💳 Pagos:</b>\n" .
            "/pagar - Generar enlace de pago de suscripción\n" .
            "/suscripcion - Ver estado de tu suscripción\n\n" .
            "<b>📄 Para enviar facturas:</b>\n" .
            "Simplemente envía el archivo PDF o una foto de la factura.\n\n" .
            "<b>⚠️ Importante:</b>\n" .
            "Solo se procesan facturas y recibos de proveedores.\n" .
            "Los extractos bancarios deben cargarse desde la plataforma web.";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Comando /link
     */
    protected function commandLink(int $chatId, int $telegramId)
    {
        // Verificar si ya está vinculado
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if ($user) {
            $this->telegramService->sendMessage(
                $chatId,
                "✅ Tu cuenta ya está vinculada.\n\n" .
                "Usuario: <b>{$user->name}</b>\n" .
                "Email: <b>{$user->email}</b>\n\n" .
                "Usa /unlink si deseas desvincular tu cuenta."
            );
            return;
        }

        $message = "🔗 <b>Vincular cuenta de Dataflow</b>\n\n" .
            "<b>Opción 1: Código de vinculación</b>\n" .
            "1. Inicia sesión en Dataflow: https://dataflow.guaraniappstore.com\n" .
            "2. Ve a tu perfil y genera un código de vinculación\n" .
            "3. Envíame el código aquí\n\n" .
            "<b>Opción 2: Vinculación manual</b>\n" .
            "Contacta al administrador del sistema con tu Telegram ID:\n" .
            "<code>{$telegramId}</code>\n\n" .
            "Una vez vinculada tu cuenta, podrás enviar facturas directamente por Telegram.";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Comando /unlink
     */
    protected function commandUnlink(int $chatId, int $telegramId)
    {
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ No tienes ninguna cuenta vinculada."
            );
            return;
        }

        $user->unlinkTelegram();

        $this->telegramService->sendMessage(
            $chatId,
            "✅ Tu cuenta ha sido desvinculada exitosamente.\n\n" .
            "Usa /link cuando quieras vincular tu cuenta nuevamente."
        );
    }

    /**
     * Comando /status
     */
    protected function commandStatus(int $chatId, int $telegramId)
    {
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ No tienes ninguna cuenta vinculada.\n\n" .
                "Usa /link para vincular tu cuenta."
            );
            return;
        }

        // Obtener estadísticas
        $documentsCount = $user->documents()->count();
        $documentsThisMonth = $user->documents()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $message = "📊 <b>Estado de tu cuenta</b>\n\n" .
            "👤 <b>Usuario:</b> {$user->name}\n" .
            "📧 <b>Email:</b> {$user->email}\n" .
            "🏢 <b>Empresa:</b> {$user->tenant->name}\n\n" .
            "📄 <b>Documentos procesados:</b>\n" .
            "Total: {$documentsCount}\n" .
            "Este mes: {$documentsThisMonth}\n\n" .
            "🔗 <b>Vinculado desde:</b> " . $user->telegram_linked_at->format('d/m/Y H:i');

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Manejar documentos recibidos
     */
    protected function handleDocument(array $message, User $user)
    {
        $chatId = $message['chat']['id'];

        // Enviar acción de "subiendo documento"
        $this->telegramService->sendChatAction($chatId, 'upload_document');

        // Obtener información del archivo
        $fileId = null;
        $fileName = null;
        $mimeType = null;

        if (isset($message['document'])) {
            $fileId = $message['document']['file_id'];
            $fileName = $message['document']['file_name'] ?? 'documento.pdf';
            $mimeType = $message['document']['mime_type'] ?? 'application/pdf';
        } elseif (isset($message['photo'])) {
            // Telegram envía múltiples tamaños, tomamos el más grande
            $photos = $message['photo'];
            $largestPhoto = end($photos);
            $fileId = $largestPhoto['file_id'];
            $fileName = 'imagen_' . time() . '.jpg';
            $mimeType = 'image/jpeg';
        }

        if (!$fileId) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ No se pudo obtener el archivo. Por favor, intenta nuevamente."
            );
            return;
        }

        // Validar tipo de archivo
        if (!$this->telegramService->isAllowedFileType($mimeType)) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ <b>Tipo de archivo no permitido</b>\n\n" .
                "Solo se aceptan archivos PDF o imágenes (JPG, PNG)."
            );
            return;
        }

        // Enviar confirmación de recepción
        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>Documento recibido</b>\n\n" .
            "📄 Archivo: {$fileName}\n" .
            "⏳ Procesando con IA...\n\n" .
            "Te notificaré cuando el procesamiento termine."
        );

        // Encolar el trabajo de procesamiento
        OcrInvoiceProcessingJob::dispatch($user, $fileId, $fileName, $mimeType, $chatId);

        Log::info('Documento de Telegram encolado para procesamiento', [
            'user_id' => $user->id,
            'file_id' => $fileId,
            'file_name' => $fileName,
            'mime_type' => $mimeType,
        ]);
    }

    /**
     * Verificar si es un código de vinculación
     */
    protected function isLinkCode(string $text): bool
    {
        return preg_match('/^[A-Z0-9]{8}$/', trim($text));
    }

    /**
     * Procesar código de vinculación
     */
    protected function processLinkCode(string $code, int $chatId, int $telegramId, ?string $username)
    {
        $userId = $this->telegramService->verifyLinkCode($code);

        if (!$userId) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ <b>Código inválido o expirado</b>\n\n" .
                "El código de vinculación no es válido o ha expirado (15 minutos).\n\n" .
                "Genera un nuevo código desde tu perfil en Dataflow."
            );
            return;
        }

        // Verificar si el usuario existe
        $user = User::find($userId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Usuario no encontrado. Por favor, contacta al soporte."
            );
            return;
        }

        // Vincular cuenta
        $user->linkTelegram($telegramId, $username, $chatId);

        $this->telegramService->sendMessage(
            $chatId,
            "✅ <b>¡Cuenta vinculada exitosamente!</b>\n\n" .
            "👤 Usuario: {$user->name}\n" .
            "📧 Email: {$user->email}\n\n" .
            "Ahora puedes enviar facturas directamente por Telegram.\n" .
            "Simplemente envía el PDF o foto de la factura."
        );

        Log::info('Cuenta de Telegram vinculada', [
            'user_id' => $user->id,
            'telegram_id' => $telegramId,
            'telegram_username' => $username,
        ]);
    }

    /**
     * Comando /pagar
     */
    protected function commandPagar(int $chatId, int $telegramId)
    {
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ No tienes ninguna cuenta vinculada.\n\n" .
                "Usa /link para vincular tu cuenta."
            );
            return;
        }

        // Obtener suscripción activa del tenant
        $subscription = $user->tenant->activeSubscription();

        if (!$subscription) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ No tienes ninguna suscripción activa.\n\n" .
                "Contacta al administrador para activar tu suscripción."
            );
            return;
        }

        // Verificar si necesita pago
        if (!$subscription->needsPayment()) {
            $this->telegramService->sendMessage(
                $chatId,
                "✅ Tu suscripción está al día.\n\n" .
                "📅 Válida hasta: {$subscription->expires_at->format('d/m/Y')}\n" .
                "💰 Plan: <b>{$subscription->plan}</b>"
            );
            return;
        }

        // Generar enlace de pago
        $this->telegramService->sendChatAction($chatId, 'typing');

        $pagoParService = app(PagoParService::class);
        $paymentResult = $pagoParService->generatePaymentLink($subscription);

        if (!$paymentResult || !$paymentResult['success']) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ Error al generar el enlace de pago.\n\n" .
                "Por favor, intenta nuevamente en unos momentos o contacta al soporte."
            );
            return;
        }

        $currency = $user->tenant->currency_code ?? 'USD';
        $formattedAmount = $pagoParService->formatAmount($subscription->price, $currency);

        $message = "💳 <b>Pago de Suscripción</b>\n\n" .
            "🏢 Empresa: {$user->tenant->name}\n" .
            "📦 Plan: <b>{$subscription->plan}</b>\n" .
            "💰 Monto: <b>{$formattedAmount}</b>\n\n" .
            "🔗 <b>Enlace de pago:</b>\n" .
            "{$paymentResult['payment_url']}\n\n" .
            "⏰ Este enlace es válido por 24 horas.\n" .
            "✅ Recibirás una notificación cuando se confirme el pago.";

        $this->telegramService->sendMessage($chatId, $message);

        Log::info('Enlace de pago generado y enviado por Telegram', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'transaction_id' => $paymentResult['transaction_id'],
        ]);
    }

    /**
     * Comando /suscripcion
     */
    protected function commandSuscripcion(int $chatId, int $telegramId)
    {
        $user = $this->telegramService->findUserByTelegramId($telegramId);

        if (!$user) {
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ No tienes ninguna cuenta vinculada.\n\n" .
                "Usa /link para vincular tu cuenta."
            );
            return;
        }

        $subscription = $user->tenant->activeSubscription();

        if (!$subscription) {
            $this->telegramService->sendMessage(
                $chatId,
                "❌ No tienes ninguna suscripción activa.\n\n" .
                "Contacta al administrador para activar tu suscripción."
            );
            return;
        }

        $currency = $user->tenant->currency_code ?? 'USD';
        $pagoParService = app(PagoParService::class);
        $formattedAmount = $pagoParService->formatAmount($subscription->price, $currency);

        $statusIcon = $subscription->isActive() ? '✅' : '⚠️';
        $statusText = $subscription->isActive() ? 'Activa' : ucfirst($subscription->status);

        $paymentStatusIcon = match($subscription->payment_status) {
            'completed' => '✅',
            'pending' => '⏳',
            'processing' => '🔄',
            'failed' => '❌',
            default => '❓',
        };
        $paymentStatusText = match($subscription->payment_status) {
            'completed' => 'Pagado',
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'failed' => 'Fallido',
            default => 'Desconocido',
        };

        $message = "📋 <b>Estado de tu Suscripción</b>\n\n" .
            "🏢 <b>Empresa:</b> {$user->tenant->name}\n" .
            "📦 <b>Plan:</b> {$subscription->plan}\n" .
            "💰 <b>Precio:</b> {$formattedAmount}\n" .
            "{$statusIcon} <b>Estado:</b> {$statusText}\n" .
            "{$paymentStatusIcon} <b>Pago:</b> {$paymentStatusText}\n\n";

        if ($subscription->starts_at) {
            $message .= "📅 <b>Inicio:</b> {$subscription->starts_at->format('d/m/Y')}\n";
        }

        if ($subscription->expires_at) {
            $daysUntilExpiry = now()->diffInDays($subscription->expires_at, false);
            $message .= "📅 <b>Vencimiento:</b> {$subscription->expires_at->format('d/m/Y')}";

            if ($daysUntilExpiry > 0) {
                $message .= " ({$daysUntilExpiry} días restantes)\n";
            } elseif ($daysUntilExpiry == 0) {
                $message .= " (vence hoy)\n";
            } else {
                $message .= " (vencida)\n";
            }
        }

        $message .= "\n📄 <b>Límite de documentos:</b> {$subscription->document_limit}/mes\n";

        // Obtener uso actual
        $documentsThisMonth = $user->tenant->getCurrentMonthDocumentCount();
        $percentage = ($documentsThisMonth / $subscription->document_limit) * 100;

        $message .= "📊 <b>Uso actual:</b> {$documentsThisMonth}/{$subscription->document_limit} ({$percentage}%)\n";

        if ($subscription->needsPayment()) {
            $message .= "\n⚠️ <b>Tu suscripción requiere pago</b>\n";
            $message .= "Usa /pagar para generar un enlace de pago.";
        }

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Manejar callback queries (botones inline)
     */
    protected function handleCallbackQuery(array $callbackQuery)
    {
        $callbackQueryId = $callbackQuery['id'];
        $data = $callbackQuery['data'];
        $chatId = $callbackQuery['message']['chat']['id'];

        // Responder al callback
        $this->telegramService->answerCallbackQuery($callbackQueryId, 'Procesando...');

        // Aquí puedes manejar diferentes acciones según el callback data
        // Por ejemplo: confirmar eliminación, ver detalles, etc.

        Log::info('Callback query recibido', [
            'callback_query_id' => $callbackQueryId,
            'data' => $data,
            'chat_id' => $chatId,
        ]);
    }
}
