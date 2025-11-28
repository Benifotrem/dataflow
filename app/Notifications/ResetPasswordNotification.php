<?php

namespace App\Notifications;

use App\Services\BrevoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database']; // No usamos 'mail' porque enviaremos via Brevo directamente
    }

    /**
     * Send the notification.
     */
    public function toDatabase($notifiable): array
    {
        // Generar URL de reset
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Enviar email via Brevo
        $brevoService = new BrevoService();
        if ($brevoService->isConfigured()) {
            $brevoService->sendPasswordReset(
                $notifiable->email,
                $notifiable->name,
                $resetUrl
            );
        }

        return [
            'message' => 'Email de recuperación de contraseña enviado',
            'url' => $resetUrl,
        ];
    }
}
