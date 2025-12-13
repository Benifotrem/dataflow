# Configuración del Bot de Telegram para Dataflow

Este documento explica cómo configurar el bot de Telegram para recibir y procesar facturas automáticamente.

## Requisitos previos

1. Bot de Telegram creado con @BotFather
2. Token del bot
3. Servidor con acceso HTTPS (webhook requiere SSL)

## Configuración del Bot

### 1. Crear el bot en Telegram

1. Abre Telegram y busca `@BotFather`
2. Envía el comando `/newbot`
3. Sigue las instrucciones para elegir nombre y username
4. Guarda el **token** que te proporciona BotFather

### 2. Configurar variables de entorno

Edita el archivo `.env` y agrega:

```bash
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_BOT_USERNAME=@TuBot
TELEGRAM_WEBHOOK_URL="${APP_URL}/api/telegram/webhook"
```

**Reemplaza:**
- `123456789:ABCdefGHIjklMNOpqrsTUVwxyz` con el token real de tu bot
- `@TuBot` con el username de tu bot
- Asegúrate que `APP_URL` apunte a tu dominio con HTTPS

### 3. Configurar el webhook

Ejecuta el siguiente comando Artisan para registrar el webhook en Telegram:

```bash
php artisan telegram:manage setup
```

Este comando:
- Configura la URL del webhook en los servidores de Telegram
- Verifica que el bot pueda recibir actualizaciones

### 4. Verificar configuración

Para verificar que el webhook está configurado correctamente:

```bash
php artisan telegram:manage info
```

Deberías ver información como:
- URL del webhook configurada
- Cantidad de actualizaciones pendientes
- Últimos errores (si los hay)

### 5. Obtener información del bot

Para ver detalles de tu bot:

```bash
php artisan telegram:manage me
```

## Uso del Bot

### Para Usuarios

1. **Vincular cuenta:**
   - Busca el bot en Telegram
   - Envía `/start`
   - Envía `/link`
   - Contacta al administrador con tu Telegram ID para completar la vinculación

2. **Enviar facturas:**
   - Una vez vinculado, simplemente envía:
     - Fotos de facturas (JPG, PNG)
     - Archivos PDF de facturas
   - El bot procesará automáticamente con IA

3. **Comandos disponibles:**
   - `/start` - Iniciar el bot
   - `/help` - Ver ayuda
   - `/link` - Vincular cuenta
   - `/unlink` - Desvincular cuenta
   - `/status` - Ver estado de cuenta
   - `/pagar` - Generar link de pago
   - `/suscripcion` - Ver estado de suscripción

### Para Administradores

**Vincular manualmente un usuario:**

```bash
# El usuario debe enviar /link en Telegram para obtener su ID
# Luego ejecuta (reemplaza email y telegram_id):
php artisan telegram:manage link --email=usuario@example.com
```

**Desvincular webhook (para mantenimiento):**

```bash
php artisan telegram:manage delete
```

## Troubleshooting

### El bot no responde

1. Verifica que el webhook esté configurado:
   ```bash
   php artisan telegram:manage info
   ```

2. Revisa los logs de Laravel:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Verifica que la URL sea accesible desde internet:
   ```bash
   curl https://tudominio.com/api/telegram/webhook
   ```

### Error de SSL

El webhook de Telegram **requiere HTTPS**. Asegúrate de:
- Tener un certificado SSL válido
- `APP_URL` en `.env` use `https://`
- El dominio sea accesible públicamente

### Actualizaciones no llegan

Si las actualizaciones no llegan al webhook:

1. Elimina el webhook actual:
   ```bash
   php artisan telegram:manage delete
   ```

2. Espera 1-2 minutos

3. Vuelve a configurar:
   ```bash
   php artisan telegram:manage setup
   ```

### Ver actualizaciones pendientes

```bash
php artisan telegram:manage info
```

Busca el campo `pending_update_count`. Si es mayor a 0, hay mensajes en cola.

## Arquitectura Técnica

### Flujo de procesamiento

1. Usuario envía foto/PDF al bot
2. Telegram envía webhook a `/api/telegram/webhook`
3. `TelegramController@webhook` procesa el mensaje
4. Si es un documento, encola `OcrInvoiceProcessingJob`
5. El job descarga el archivo de Telegram
6. Si es PDF, convierte a imagen con `PdfConverterService`
7. Procesa con OpenAI Vision (`OcrVisionService`)
8. Valida con DNIT (`DnitConnector`)
9. Guarda en base de datos
10. Notifica al usuario por Telegram

### Archivos clave

- `app/Http/Controllers/Api/TelegramController.php` - Controlador principal
- `app/Services/TelegramService.php` - Servicio de comunicación con API
- `app/Jobs/OcrInvoiceProcessingJob.php` - Job de procesamiento
- `app/Console/Commands/TelegramBotManage.php` - Comandos Artisan
- `routes/api.php` - Ruta del webhook
- `config/services.php` - Configuración del bot

## Seguridad

1. **No compartas el token del bot** - Es como una contraseña
2. **Usa HTTPS** - Requerido por Telegram
3. **Valida usuarios** - El bot verifica que estén vinculados
4. **Logs** - Todos los mensajes se registran para auditoría

## Producción

### Hostinger/Shared Hosting

Si usas shared hosting:

1. Asegúrate que la URL del webhook sea accesible
2. Verifica que `allow_url_fopen` esté habilitado (para descargar archivos)
3. Configura cron para `queue:work`:
   ```bash
   * * * * * php /home/usuario/public_html/artisan queue:work --stop-when-empty
   ```

### VPS/Dedicated Server

Usa supervisor para mantener workers activos:

```ini
[program:dataflow-worker]
command=php /ruta/a/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/dataflow-worker.log
```

## Soporte

Para problemas o preguntas:
- Revisa los logs: `storage/logs/laravel.log`
- Documentación de Telegram Bot API: https://core.telegram.org/bots/api
- Issues del proyecto: [GitHub Issues]

---

**Última actualización:** 2025-12-12
**Versión:** 1.0
