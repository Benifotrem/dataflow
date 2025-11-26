<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public $token;

    /**
     * Create a notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $message = (new MailMessage)
            ->subject('Restablecer Contraseña - ' . config('app.name'))
            ->greeting('¡Hola!')
            ->line('Recibiste este correo porque solicitaste restablecer tu contraseña.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace expirará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
            ->line('Si no solicitaste restablecer tu contraseña, ignora este mensaje.');

        // Add Telegram information if user hasn't linked Telegram yet
        if (!$notifiable->telegram_id) {
            $message->line('---')
                ->line('💡 **¿Sabías que puedes usar Telegram con tu cuenta?**')
                ->line('Vincula tu cuenta de Telegram para:')
                ->line('✅ Enviar facturas directamente desde tu móvil')
                ->line('✅ Recibir notificaciones instantáneas')
                ->line('✅ Procesar documentos sobre la marcha')
                ->line('Inicia sesión y busca la opción "Vincular Telegram" en tu perfil.');
        }

        return $message;
    }
}
