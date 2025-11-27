<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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

        // Get language based on tenant's country (default Spanish)
        $countryCode = $notifiable->tenant->country_code ?? 'ES';
        $locale = $this->getLocaleFromCountry($countryCode);

        $message = (new MailMessage)
            ->subject('Restablecer Contraseña - ' . config('app.name'))
            ->greeting('¡Hola!')
            ->line('Has recibido este correo porque solicitaste restablecer la contraseña de tu cuenta.')
            ->action('Restablecer Contraseña', $url)
            ->line('**⏰ Este enlace expirará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.**')
            ->line('---')
            ->line('🔒 **IMPORTANTE - Seguridad:**')
            ->line('• **NO compartas este enlace con nadie.** Es personal e intransferible.')
            ->line('• Compartir este enlace permitiría a terceros acceder a tu cuenta.')
            ->line('• Si no solicitaste este cambio, ignora este mensaje y tu contraseña permanecerá sin cambios.');

        // Add Telegram information if user hasn't linked Telegram yet
        if (!$notifiable->telegram_id) {
            $message->line('---')
                ->line('💬 **¿Sabías que puedes gestionar tu contabilidad desde Telegram?**')
                ->line('Vincula tu cuenta de Telegram para disfrutar de:')
                ->line('✅ **Enviar facturas desde tu móvil** - Solo envía la foto del documento')
                ->line('✅ **Procesamiento automático con IA** - Extracción de datos al instante')
                ->line('✅ **Notificaciones instantáneas** - Mantente informado en tiempo real')
                ->line('✅ **Gestión sobre la marcha** - Sin necesidad de entrar a la web')
                ->line('')
                ->line('👉 Inicia sesión y busca la opción **"Vincular Telegram"** en tu perfil.');
        }

        $message->salutation('Saludos cordiales,
Equipo de ' . config('app.name'));

        return $message;
    }

    /**
     * Get locale from country code.
     */
    protected function getLocaleFromCountry(string $countryCode): string
    {
        // Map country codes to locales
        $mapping = [
            'ES' => 'es', // España
            'PY' => 'es', // Paraguay
            'AR' => 'es', // Argentina
            'MX' => 'es', // México
            'BR' => 'pt', // Brasil
            // Add more as needed
        ];

        return $mapping[$countryCode] ?? 'es'; // Default to Spanish
    }
}
