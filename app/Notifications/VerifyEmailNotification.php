<?php

namespace App\Notifications;

use App\Services\BrevoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        // Generar URL de verificación
        $verificationUrl = $this->verificationUrl($notifiable);

        // Enviar email via Brevo
        $brevoService = new BrevoService();
        if ($brevoService->isConfigured()) {
            $brevoService->sendEmailVerification(
                $notifiable->email,
                $notifiable->name,
                $verificationUrl
            );
        }

        return [
            'message' => 'Email de verificación enviado',
            'url' => $verificationUrl,
        ];
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
