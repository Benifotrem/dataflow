<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Dataflow</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: bold;">
                                ¡Bienvenido a Dataflow! 🎉
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 24px;">
                                Hola <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 24px;">
                                ¡Nos alegra mucho tenerte con nosotros! Tu cuenta ha sido creada exitosamente y ya puedes comenzar a usar Dataflow.
                            </p>

                            <div style="background-color: #F3F4F6; border-left: 4px solid #8B5CF6; padding: 20px; margin: 30px 0; border-radius: 4px;">
                                <p style="margin: 0 0 10px; color: #1F2937; font-size: 14px; font-weight: 600;">
                                    📧 Tu cuenta:
                                </p>
                                <p style="margin: 0; color: #6B7280; font-size: 14px;">
                                    {{ $user->email }}
                                </p>
                            </div>

                            <h2 style="margin: 30px 0 15px; color: #1F2937; font-size: 20px; font-weight: 600;">
                                ¿Qué puedes hacer con Dataflow?
                            </h2>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="color: #8B5CF6; font-size: 20px; margin-right: 10px;">📄</span>
                                        <span style="color: #374151; font-size: 15px;">Procesamiento automático de facturas con IA</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="color: #8B5CF6; font-size: 20px; margin-right: 10px;">📊</span>
                                        <span style="color: #374151; font-size: 15px;">Dashboard con estadísticas en tiempo real</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="color: #8B5CF6; font-size: 20px; margin-right: 10px;">🤖</span>
                                        <span style="color: #374151; font-size: 15px;">Bot de Telegram para enviar facturas</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <span style="color: #8B5CF6; font-size: 20px; margin-right: 10px;">✅</span>
                                        <span style="color: #374151; font-size: 15px;">Detección automática de duplicados</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; padding: 14px 32px; background-color: #8B5CF6; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 600;">
                                            Ir a mi Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 0; color: #6B7280; font-size: 14px; line-height: 20px;">
                                Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos respondiendo a este email.
                            </p>

                            <p style="margin: 20px 0 0; color: #374151; font-size: 16px;">
                                Saludos,<br>
                                <strong>El equipo de Dataflow</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #F9FAFB; border-top: 1px solid #E5E7EB; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 10px; color: #6B7280; font-size: 12px; text-align: center;">
                                © {{ date('Y') }} Dataflow by Guarani App Store. Todos los derechos reservados.
                            </p>
                            <p style="margin: 0; color: #9CA3AF; font-size: 11px; text-align: center;">
                                Este es un email automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
