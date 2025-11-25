<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.brevo.com/v3';

    public function __construct()
    {
        $this->apiKey = Setting::get('brevo_api_key') ?? config('services.brevo.api_key');
    }

    /**
     * Enviar email de verificación de cuenta
     */
    public function sendEmailVerification(string $email, string $name, string $verificationUrl): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/smtp/email", [
                'sender' => [
                    'name' => config('app.name', 'Dataflow'),
                    'email' => config('mail.from.address', 'no-reply@dataflow.com'),
                ],
                'to' => [
                    [
                        'email' => $email,
                        'name' => $name,
                    ],
                ],
                'subject' => 'Verifica tu cuenta en Dataflow',
                'htmlContent' => $this->getEmailVerificationTemplate($name, $verificationUrl),
            ]);

            if ($response->successful()) {
                Log::info("Email de verificación enviado a: {$email}");
                return true;
            }

            Log::error("Error al enviar email de verificación: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Error en BrevoService::sendEmailVerification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    public function sendPasswordReset(string $email, string $name, string $resetUrl): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/smtp/email", [
                'sender' => [
                    'name' => config('app.name', 'Dataflow'),
                    'email' => config('mail.from.address', 'no-reply@dataflow.com'),
                ],
                'to' => [
                    [
                        'email' => $email,
                        'name' => $name,
                    ],
                ],
                'subject' => 'Recupera tu contraseña - Dataflow',
                'htmlContent' => $this->getPasswordResetTemplate($name, $resetUrl),
            ]);

            if ($response->successful()) {
                Log::info("Email de recuperación de contraseña enviado a: {$email}");
                return true;
            }

            Log::error("Error al enviar email de recuperación: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Error en BrevoService::sendPasswordReset: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de límite de documentos
     */
    public function sendDocumentLimitNotification(string $email, string $name, int $used, int $limit, float $percentage): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/smtp/email", [
                'sender' => [
                    'name' => config('app.name', 'Dataflow'),
                    'email' => config('mail.from.address', 'no-reply@dataflow.com'),
                ],
                'to' => [
                    [
                        'email' => $email,
                        'name' => $name,
                    ],
                ],
                'subject' => '⚠️ Acercándote al límite de documentos - Dataflow',
                'htmlContent' => $this->getDocumentLimitTemplate($name, $used, $limit, $percentage),
            ]);

            if ($response->successful()) {
                Log::info("Notificación de límite de documentos enviada a: {$email}");
                return true;
            }

            Log::error("Error al enviar notificación de límite: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Error en BrevoService::sendDocumentLimitNotification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email de bienvenida
     */
    public function sendWelcomeEmail(string $email, string $name): bool
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/smtp/email", [
                'sender' => [
                    'name' => config('app.name', 'Dataflow'),
                    'email' => config('mail.from.address', 'no-reply@dataflow.com'),
                ],
                'to' => [
                    [
                        'email' => $email,
                        'name' => $name,
                    ],
                ],
                'subject' => '¡Bienvenido a Dataflow!',
                'htmlContent' => $this->getWelcomeTemplate($name),
            ]);

            if ($response->successful()) {
                Log::info("Email de bienvenida enviado a: {$email}");
                return true;
            }

            Log::error("Error al enviar email de bienvenida: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Error en BrevoService::sendWelcomeEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Template de verificación de email
     */
    protected function getEmailVerificationTemplate(string $name, string $verificationUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verifica tu cuenta</title>
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px;">Dataflow</h1>
            </div>
            <div style="background: white; padding: 40px 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-top: 0;">¡Hola {$name}!</h2>
                <p style="font-size: 16px; color: #555;">Gracias por registrarte en Dataflow. Para completar tu registro y activar tu cuenta, por favor verifica tu dirección de email haciendo clic en el botón de abajo:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{$verificationUrl}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Verificar Mi Email</a>
                </div>
                <p style="font-size: 14px; color: #777;">Si no puedes hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:</p>
                <p style="font-size: 14px; color: #667eea; word-break: break-all;">{$verificationUrl}</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #999;">Si no creaste esta cuenta, puedes ignorar este email de forma segura.</p>
                <p style="font-size: 13px; color: #999; margin-top: 20px;">Saludos,<br>El equipo de Dataflow</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Template de recuperación de contraseña
     */
    protected function getPasswordResetTemplate(string $name, string $resetUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recupera tu contraseña</title>
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px;">Dataflow</h1>
            </div>
            <div style="background: white; padding: 40px 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-top: 0;">¡Hola {$name}!</h2>
                <p style="font-size: 16px; color: #555;">Recibimos una solicitud para restablecer la contraseña de tu cuenta en Dataflow. Haz clic en el botón de abajo para crear una nueva contraseña:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{$resetUrl}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Restablecer Contraseña</a>
                </div>
                <p style="font-size: 14px; color: #777;">Este enlace expirará en 60 minutos por razones de seguridad.</p>
                <p style="font-size: 14px; color: #777;">Si no puedes hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:</p>
                <p style="font-size: 14px; color: #667eea; word-break: break-all;">{$resetUrl}</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #999;">Si no solicitaste restablecer tu contraseña, puedes ignorar este email de forma segura. Tu contraseña no cambiará.</p>
                <p style="font-size: 13px; color: #999; margin-top: 20px;">Saludos,<br>El equipo de Dataflow</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Template de notificación de límite de documentos
     */
    protected function getDocumentLimitTemplate(string $name, int $used, int $limit, float $percentage): string
    {
        $remaining = $limit - $used;
        $isNearLimit = $percentage >= 95;
        $color = $isNearLimit ? '#ef4444' : '#f59e0b';
        $icon = $isNearLimit ? '🚨' : '⚠️';

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Límite de documentos</title>
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: linear-gradient(135deg, {$color} 0%, {$color} 100%); padding: 40px 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px;">{$icon} Alerta de Límite</h1>
            </div>
            <div style="background: white; padding: 40px 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-top: 0;">¡Hola {$name}!</h2>
                <p style="font-size: 16px; color: #555;">Te escribimos para informarte que estás acercándote al límite de documentos de tu plan.</p>
                <div style="background: #f9fafb; border-left: 4px solid {$color}; padding: 20px; margin: 20px 0; border-radius: 4px;">
                    <p style="margin: 0; font-size: 16px; color: #333;"><strong>Uso actual:</strong> {$used} de {$limit} documentos ({$percentage}%)</p>
                    <p style="margin: 10px 0 0 0; font-size: 16px; color: {$color};"><strong>Documentos restantes:</strong> {$remaining}</p>
                </div>
                <p style="font-size: 16px; color: #555;">Para continuar procesando documentos sin interrupciones, te recomendamos actualizar tu plan.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://dataflow.guaraniappstore.com/pricing" style="display: inline-block; background: {$color}; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Ver Planes Disponibles</a>
                </div>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #999;">¿Tienes preguntas? Contáctanos en soporte@dataflow.com</p>
                <p style="font-size: 13px; color: #999; margin-top: 20px;">Saludos,<br>El equipo de Dataflow</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Template de bienvenida
     */
    protected function getWelcomeTemplate(string $name): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bienvenido a Dataflow</title>
        </head>
        <body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center; border-radius: 10px 10px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px;">¡Bienvenido a Dataflow!</h1>
            </div>
            <div style="background: white; padding: 40px 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-top: 0;">¡Hola {$name}!</h2>
                <p style="font-size: 16px; color: #555;">¡Estamos encantados de tenerte con nosotros! Tu cuenta ha sido creada exitosamente y ya puedes comenzar a aprovechar todas las funcionalidades de Dataflow.</p>
                <div style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 20px; margin: 20px 0; border-radius: 4px;">
                    <h3 style="margin-top: 0; color: #0369a1;">🎉 ¿Qué puedes hacer ahora?</h3>
                    <ul style="color: #555; margin: 10px 0; padding-left: 20px;">
                        <li style="margin: 8px 0;">Subir y procesar facturas, recibos y extractos bancarios</li>
                        <li style="margin: 8px 0;">Gestionar transacciones financieras</li>
                        <li style="margin: 8px 0;">Configurar entidades fiscales</li>
                        <li style="margin: 8px 0;">Generar reportes automáticos con IA</li>
                    </ul>
                </div>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://dataflow.guaraniappstore.com/dashboard" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Ir a Mi Dashboard</a>
                </div>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 13px; color: #999;">Si tienes alguna pregunta, no dudes en contactarnos. Estamos aquí para ayudarte.</p>
                <p style="font-size: 13px; color: #999; margin-top: 20px;">Saludos,<br>El equipo de Dataflow</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Verificar si Brevo está configurado
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
