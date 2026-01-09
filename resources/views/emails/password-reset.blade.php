<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Dataflow</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background-color: #8B5CF6; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                🔐 Recuperación de Contraseña
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 24px;">
                                Hola <strong>{{ $userName }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 24px;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en Dataflow.
                            </p>

                            <p style="margin: 0 0 30px; color: #374151; font-size: 16px; line-height: 24px;">
                                Haz clic en el botón de abajo para crear una nueva contraseña:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 16px 40px; background-color: #8B5CF6; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 600;">
                                            Restablecer Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alert Box -->
                            <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 20px; margin: 30px 0; border-radius: 4px;">
                                <p style="margin: 0 0 10px; color: #92400E; font-size: 14px; font-weight: 600;">
                                    ⚠️ Importante:
                                </p>
                                <p style="margin: 0; color: #92400E; font-size: 14px; line-height: 20px;">
                                    Este enlace expirará en <strong>60 minutos</strong> por razones de seguridad.
                                </p>
                            </div>

                            <p style="margin: 30px 0 0; color: #6B7280; font-size: 14px; line-height: 20px;">
                                Si no solicitaste este cambio de contraseña, puedes ignorar este email de forma segura. Tu contraseña actual no cambiará.
                            </p>

                            <div style="margin: 30px 0; padding: 20px; background-color: #F9FAFB; border-radius: 6px; border: 1px solid #E5E7EB;">
                                <p style="margin: 0 0 10px; color: #6B7280; font-size: 12px; font-weight: 600;">
                                    Si el botón no funciona, copia y pega este enlace en tu navegador:
                                </p>
                                <p style="margin: 0; color: #8B5CF6; font-size: 11px; word-break: break-all;">
                                    {{ $resetUrl }}
                                </p>
                            </div>

                            <p style="margin: 30px 0 0; color: #374151; font-size: 16px;">
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
                                Por tu seguridad, nunca compartas este email con nadie.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
