<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\OcrInvoiceProcessingJob;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PagoParService;
use App\Services\TelegramService;
use App\Services\TelegramConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected TelegramService $telegramService;
    protected TelegramConversationService $conversationService;

    public function __construct(
        TelegramService $telegramService,
        TelegramConversationService $conversationService
    ) {
        $this->telegramService = $telegramService;
        $this->conversationService = $conversationService;
    }

    /**
     * Manejar webhook de Telegram
     * SIEMPRE retorna 200 OK para evitar reenvíos de Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();

            Log::info('📥 Webhook de Telegram recibido', [
                'update_id' => $update['update_id'] ?? null,
                'has_message' => isset($update['message']),
                'has_callback' => isset($update['callback_query']),
            ]);

            // Procesar mensaje
            if (isset($update['message'])) {
                try {
                    $this->handleMessage($update['message']);
                } catch (\Exception $e) {
                    Log::error('❌ Error procesando mensaje', [
                        'error' => $e->getMessage(),
                        'message' => $update['message'],
                    ]);

                    // Intentar notificar al usuario del error
                    try {
                        if (isset($update['message']['chat']['id'])) {
                            $this->telegramService->sendMessage(
                                $update['message']['chat']['id'],
                                "❌ Hubo un error al procesar tu mensaje.\n\n" .
                                "El sistema sigue funcionando. Intenta de nuevo o usa /help para más opciones."
                            );
                        }
                    } catch (\Exception $notifyError) {
                        Log::error('No se pudo notificar error al usuario', [
                            'error' => $notifyError->getMessage(),
                        ]);
                    }
                }
            }

            // Procesar callback query
            if (isset($update['callback_query'])) {
                try {
                    $this->handleCallbackQuery($update['callback_query']);
                } catch (\Exception $e) {
                    Log::error('❌ Error procesando callback', [
                        'error' => $e->getMessage(),
                        'callback' => $update['callback_query'],
                    ]);
                }
            }

            // SIEMPRE retornar 200 OK para que Telegram no reenvíe
            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error crítico en webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // SIEMPRE retornar 200 OK para que Telegram no reenvíe
            return response()->json(['status' => 'ok'], 200);
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
            // MODO PRUEBA TEMPORAL: Asignar usuario admin automáticamente
            $user = User::where('is_admin', true)->first();
            if (!$user) {
                $user = User::first(); // Fallback a cualquier usuario
            }

            if (!$user) {
                $this->telegramService->sendMessage(
                    $chatId,
                    "❌ Error: No hay usuarios en el sistema. Contacta al administrador."
                );
                return;
            }

            // Notificar modo prueba
            $this->telegramService->sendMessage(
                $chatId,
                "⚠️ <b>MODO PRUEBA</b>\n\n" .
                "Procesando como: <b>{$user->name}</b>\n" .
                "Email: {$user->email}\n\n" .
                "Para vincular tu cuenta, usa /link después."
            );
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

            // Procesar conversación con IA
            $this->handleConversation($message['text'], $chatId, $user);
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

            case '/app':
                $this->commandApp($chatId);
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
        $message = "🤖 <b>¡Bienvenido al Asistente Fiscal de Dataflow!</b>\n\n" .
            "Soy tu asistente experto en contabilidad paraguaya y gestión de facturas.\n\n" .
            "<b>💬 Puedes conversar conmigo:</b>\n" .
            "• Pregúntame sobre fiscalidad paraguaya (RG-90, SET, IVA)\n" .
            "• Consulta sobre comprobantes y requisitos fiscales\n" .
            "• Pide ayuda con normativas contables\n\n" .
            "<b>📄 Envía tus facturas directamente:</b>\n" .
            "• Solo envía el PDF o foto de la factura\n" .
            "• La proceso automáticamente con IA\n" .
            "• Te notifico cuando esté lista\n\n" .
            "<b>📱 Usa /app para:</b>\n" .
            "• Fotografiar documentos con compresión automática\n" .
            "• Subir múltiples facturas de forma rápida\n\n" .
            "Escríbeme directamente o usa /help para ver más opciones.";

        $this->telegramService->sendMessage($chatId, $message);
    }

    /**
     * Comando /help
     */
    protected function commandHelp(int $chatId)
    {
        $message = "📚 <b>¿Cómo puedo ayudarte?</b>\n\n" .
            "<b>💬 Conversación:</b>\n" .
            "Escríbeme directamente para:\n" .
            "• Consultas sobre fiscalidad paraguaya\n" .
            "• Interpretación de normativas SET\n" .
            "• Validación de comprobantes\n" .
            "• Requisitos de facturación\n\n" .
            "<b>📄 Procesar Facturas:</b>\n" .
            "• Envía el PDF o foto directamente\n" .
            "• Procesamiento automático con IA\n" .
            "• Notificación cuando esté lista\n\n" .
            "<b>📱 Comandos:</b>\n" .
            "/start - Información inicial\n" .
            "/app - 📷 Abrir cámara para escanear documentos\n" .
            "/link - Vincular tu cuenta\n" .
            "/status - Estado de tu cuenta\n" .
            "/pagar - Pago de suscripción\n" .
            "/suscripcion - Ver suscripción\n\n" .
            "💡 <b>Tip:</b> No necesitas comandos para hablar conmigo o enviar facturas, solo escribe o envía el documento.";

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
     * Manejar conversación con el usuario
     */
    protected function handleConversation(string $text, int $chatId, User $user)
    {
        try {
            // Mostrar acción de "escribiendo"
            $this->telegramService->sendChatAction($chatId, 'typing');

            // Procesar mensaje y obtener respuesta
            $response = $this->conversationService->processMessage($user, $text, $chatId);

            // Enviar respuesta
            $this->telegramService->sendMessage($chatId, $response);

        } catch (\Exception $e) {
            Log::error('Error en conversación Telegram', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);

            $this->telegramService->sendMessage(
                $chatId,
                "❌ Lo siento, hubo un error al procesar tu mensaje. Por favor intenta de nuevo.\n\n" .
                "💡 <b>Tip:</b> También puedes enviarme una factura (PDF o imagen) para procesarla automáticamente."
            );
        }
    }

    /**
     * Manejar documentos recibidos
     * Aislado para que errores NO afecten la conversacionalidad
     */
    protected function handleDocument(array $message, User $user)
    {
        $chatId = $message['chat']['id'];

        try {
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
                    "❌ No se pudo obtener el archivo.\n\n" .
                    "💡 <b>Soluciones:</b>\n" .
                    "• Intenta enviar el archivo de nuevo\n" .
                    "• Verifica que sea PDF o imagen (JPG/PNG)\n" .
                    "• Si el problema persiste, puedes preguntarme sobre fiscalidad"
                );
                return;
            }

            // Validar tipo de archivo
            if (!$this->telegramService->isAllowedFileType($mimeType)) {
                $this->telegramService->sendMessage(
                    $chatId,
                    "❌ <b>Tipo de archivo no permitido</b>\n\n" .
                    "📄 Formatos aceptados:\n" .
                    "• PDF (.pdf)\n" .
                    "• Imágenes JPG (.jpg, .jpeg)\n" .
                    "• Imágenes PNG (.png)\n\n" .
                    "Tipo recibido: {$mimeType}\n\n" .
                    "💡 <b>Tip:</b> Si tienes dudas sobre documentos, pregúntame directamente."
                );
                return;
            }

            // Enviar confirmación de recepción
            $this->telegramService->sendMessage(
                $chatId,
                "✅ <b>Documento recibido</b>\n\n" .
                "📄 Archivo: {$fileName}\n" .
                "⏳ Procesando con IA...\n\n" .
                "Te notificaré cuando termine. Mientras tanto, puedes seguir conversando conmigo."
            );

            // Encolar el trabajo de procesamiento con OpenAI Vision + DNIT
            try {
                OcrInvoiceProcessingJob::dispatch(
                    $user,
                    $fileId,          // fileId
                    $fileName,        // fileName
                    $mimeType,        // mimeType
                    $chatId,          // chatId
                    null,             // promptContext
                    null              // fileContent (se descargará de Telegram)
                );

                Log::info('✅ Documento de Telegram encolado para procesamiento', [
                    'user_id' => $user->id,
                    'file_id' => $fileId,
                    'file_name' => $fileName,
                    'mime_type' => $mimeType,
                ]);

            } catch (\Exception $dispatchError) {
                Log::error('❌ Error al encolar job de procesamiento', [
                    'user_id' => $user->id,
                    'file_id' => $fileId,
                    'error' => $dispatchError->getMessage(),
                ]);

                $this->telegramService->sendMessage(
                    $chatId,
                    "❌ <b>Error al procesar documento</b>\n\n" .
                    "El sistema no pudo encolar tu documento para procesamiento.\n\n" .
                    "💡 <b>Qué puedes hacer:</b>\n" .
                    "1. Intenta enviar el documento de nuevo en unos minutos\n" .
                    "2. Usa /app para subir documentos desde la miniapp\n" .
                    "3. Sube el documento manualmente en https://dataflow.guaraniappstore.com\n\n" .
                    "Mientras tanto, puedo ayudarte con consultas sobre fiscalidad. Pregúntame lo que necesites."
                );
            }

        } catch (\Exception $e) {
            Log::error('❌ Error general en handleDocument', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notificar al usuario con mensaje útil
            try {
                $this->telegramService->sendMessage(
                    $chatId,
                    "❌ <b>Error inesperado al procesar documento</b>\n\n" .
                    "Error: " . substr($e->getMessage(), 0, 200) . "\n\n" .
                    "💡 <b>Opciones:</b>\n" .
                    "• Intenta de nuevo en unos minutos\n" .
                    "• Usa la plataforma web: https://dataflow.guaraniappstore.com\n" .
                    "• Pregúntame sobre fiscalidad mientras tanto\n\n" .
                    "El bot sigue funcionando normalmente para consultas."
                );
            } catch (\Exception $notifyError) {
                Log::error('No se pudo notificar error de documento al usuario', [
                    'error' => $notifyError->getMessage(),
                ]);
            }
        }
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
     * Comando /app - Abrir Mini App
     */
    protected function commandApp(int $chatId)
    {
        $this->telegramService->sendMessage($chatId, 
            "📷 <b>Escáner de Documentos</b>\n\n" .
            "Presiona el botón para abrir el escáner móvil.\n\n" .
            "✨ <b>Funcionalidades:</b>\n" .
            "• 📸 Fotografiar facturas con tu cámara\n" .
            "• 🗜️ Compresión automática sin pérdida de calidad\n" .
            "• 📤 Subida rápida de múltiples documentos\n" .
            "• ⚡ Procesamiento inmediato con IA\n\n" .
            "💡 <b>Tip:</b> También puedes enviar documentos directamente al chat.",
            [
                'reply_markup' => json_encode([
                    'inline_keyboard' => [[
                        [
                            'text' => '📷 Abrir Escáner de Documentos',
                            'web_app' => ['url' => 'https://dataflow.guaraniappstore.com/miniapp']
                        ]
                    ]]
                ])
            ]
        );
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
