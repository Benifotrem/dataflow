<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Crear notificación de duplicado detectado
     */
    public function notifyDuplicateDetected(
        int $tenantId,
        ?int $userId,
        string $invoiceNumber,
        string $issuer,
        float $amount,
        int $originalDocumentId,
        int $duplicateDocumentId
    ): ?Notification {
        $title = '⚠️ Documento duplicado eliminado';
        $message = "Se detectó que intentaste subir un documento duplicado:\n\n" .
                   "📄 Factura: {$invoiceNumber}\n" .
                   "🏢 Emisor: {$issuer}\n" .
                   "💰 Monto: " . $this->formatCurrency($amount) . "\n\n" .
                   "El duplicado fue eliminado automáticamente para evitar procesamiento doble.";

        $data = [
            'original_document_id' => $originalDocumentId,
            'duplicate_document_id' => $duplicateDocumentId,
            'invoice_number' => $invoiceNumber,
            'issuer' => $issuer,
            'amount' => $amount,
        ];

        return $this->createNotification(
            tenantId: $tenantId,
            userId: $userId,
            type: 'duplicate_detected',
            title: $title,
            message: $message,
            data: $data,
            sendToTelegram: true
        );
    }

    /**
     * Crear notificación de límite de documentos excedido
     */
    public function notifyDocumentLimitExceeded(
        int $tenantId,
        ?int $userId,
        int $currentUsage,
        int $limit
    ): ?Notification {
        $title = '🚨 Límite de documentos excedido';
        $message = "Has alcanzado el límite de {$limit} documentos IA este mes.\n\n" .
                   "Uso actual: {$currentUsage}/{$limit}\n\n" .
                   "Para continuar procesando documentos, necesitas agregar un addon de 500 documentos adicionales por Gs 9.99.";

        $data = [
            'current_usage' => $currentUsage,
            'limit' => $limit,
            'addon_price' => 9.99,
        ];

        return $this->createNotification(
            tenantId: $tenantId,
            userId: $userId,
            type: 'limit_exceeded',
            title: $title,
            message: $message,
            data: $data,
            sendToTelegram: true
        );
    }

    /**
     * Crear notificación de documento procesado exitosamente
     */
    public function notifyDocumentProcessed(
        int $tenantId,
        ?int $userId,
        int $documentId,
        string $invoiceNumber,
        float $amount
    ): ?Notification {
        $title = '✅ Documento procesado';
        $message = "Tu factura {$invoiceNumber} ha sido procesada exitosamente.\n\n" .
                   "💰 Monto: " . $this->formatCurrency($amount);

        $data = [
            'document_id' => $documentId,
            'invoice_number' => $invoiceNumber,
            'amount' => $amount,
        ];

        return $this->createNotification(
            tenantId: $tenantId,
            userId: $userId,
            type: 'document_processed',
            title: $title,
            message: $message,
            data: $data,
            sendToTelegram: false // No enviar a Telegram para documentos exitosos
        );
    }

    /**
     * Crear notificación genérica
     */
    protected function createNotification(
        int $tenantId,
        ?int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        bool $sendToTelegram = false
    ): ?Notification {
        try {
            $notification = Notification::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            Log::info('Notification created', [
                'notification_id' => $notification->id,
                'type' => $type,
                'tenant_id' => $tenantId,
            ]);

            // Enviar a Telegram si está configurado
            if ($sendToTelegram) {
                $this->sendToTelegram($notification);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Error creating notification', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
                'type' => $type,
            ]);

            return null;
        }
    }

    /**
     * Enviar notificación a Telegram
     */
    protected function sendToTelegram(Notification $notification): void
    {
        try {
            $tenant = Tenant::find($notification->tenant_id);

            if (!$tenant || !$tenant->telegram_chat_id) {
                Log::debug('Tenant does not have Telegram configured', [
                    'tenant_id' => $notification->tenant_id,
                ]);
                return;
            }

            // Formatear mensaje para Telegram
            $telegramMessage = $this->formatTelegramMessage($notification);

            // Enviar mensaje
            $this->telegramService->sendMessage(
                $tenant->telegram_chat_id,
                $telegramMessage
            );

            Log::info('Notification sent to Telegram', [
                'notification_id' => $notification->id,
                'chat_id' => $tenant->telegram_chat_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending notification to Telegram', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Formatear mensaje para Telegram
     */
    protected function formatTelegramMessage(Notification $notification): string
    {
        $icon = $this->getIconForType($notification->type);

        $message = "🔔 *Notificación de Dataflow*\n\n";
        $message .= "{$icon} *{$notification->title}*\n\n";
        $message .= $notification->message;

        // Agregar link si hay documento relacionado
        if (isset($notification->data['original_document_id'])) {
            $documentId = $notification->data['original_document_id'];
            $url = route('documents.show', $documentId);
            $message .= "\n\n[Ver documento original]({$url})";
        }

        $message .= "\n\n_" . $notification->created_at->diffForHumans() . "_";

        return $message;
    }

    /**
     * Obtener ícono según tipo de notificación
     */
    protected function getIconForType(string $type): string
    {
        return match($type) {
            'duplicate_detected' => '⚠️',
            'limit_exceeded' => '🚨',
            'document_processed' => '✅',
            'document_failed' => '❌',
            'warning' => '⚠️',
            'info' => 'ℹ️',
            default => '🔔',
        };
    }

    /**
     * Formatear moneda
     */
    protected function formatCurrency(float $amount): string
    {
        return 'Gs ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener notificaciones no leídas de un tenant
     */
    public function getUnreadTenantNotifications(int $tenantId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::forTenant($tenantId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }
}
