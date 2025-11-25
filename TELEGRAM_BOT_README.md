# 🤖 Bot de Telegram - Dataflow

Documentación completa del bot de Telegram para la gestión automática de facturas y pagos de suscripciones.

## 📋 Tabla de Contenidos

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Comandos del Bot](#comandos-del-bot)
- [Flujo de Uso](#flujo-de-uso)
- [Gestión de Pagos](#gestión-de-pagos)
- [Comandos Artisan](#comandos-artisan)
- [Webhooks](#webhooks)
- [Estructura de Archivos](#estructura-de-archivos)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Características

### Gestión de Facturas
- ✅ Recepción de facturas por Telegram (PDF o imágenes)
- ✅ Extracción automática de datos con IA (OpenAI Vision)
- ✅ Organización automática por emisor/año/mes
- ✅ Almacenamiento en: `storage/contadores/{user_id}/facturas/{emisor}/{año}/{mes}/`
- ✅ Notificaciones en tiempo real del estado de procesamiento
- ⚠️ **Restricción**: Solo facturas/recibos (extractos bancarios NO)

### Gestión de Pagos
- 💳 Generación de enlaces de pago con PagoPar
- 💳 Notificaciones de suscripciones vencidas
- 💳 Recordatorios automáticos de renovación
- 💳 Confirmación de pagos en tiempo real

### Vinculación de Cuentas
- 🔗 Vinculación segura con código temporal (15 minutos)
- 🔗 Un usuario = una cuenta de Telegram
- 🔗 Desvinculación en cualquier momento

---

## 📦 Requisitos

- PHP 8.2+
- Laravel 12
- Cuenta de Telegram Bot (BotFather)
- Cuenta de PagoPar (para pagos)
- OpenAI API Key (ya configurado)
- Servidor HTTPS (req por Telegram)

---

## 🔧 Instalación

### 1. Instalar Dependencias

```bash
composer install
```

Esto instalará:
- `irazasyed/telegram-bot-sdk: ^3.14` - SDK de Telegram

### 2. Ejecutar Migraciones

```bash
php artisan migrate
```

Esto creará:
- Campos de Telegram en `users` (telegram_id, telegram_username, telegram_chat_id, telegram_linked_at)
- Campos de pago en `subscriptions` (payment_link, payment_status, payment_transaction_id, etc.)

### 3. Crear Bot en Telegram

1. Habla con [@BotFather](https://t.me/BotFather) en Telegram
2. Envía `/newbot`
3. Sigue las instrucciones
4. Guarda el **token** que te proporciona
5. Configura el nombre de usuario del bot

### 4. Configurar Variables de Entorno

Edita tu archivo `.env`:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_BOT_USERNAME=DataflowBot
TELEGRAM_WEBHOOK_URL="${APP_URL}/api/telegram/webhook"

# PagoPar Payment Configuration
PAGOPAR_PUBLIC_KEY=tu_public_key
PAGOPAR_PRIVATE_KEY=tu_private_key
PAGOPAR_SANDBOX=true
PAGOPAR_WEBHOOK_URL="${APP_URL}/api/pagopar/webhook"
```

### 5. Configurar Webhook

```bash
# Opción 1: Usando comando Artisan (recomendado)
php artisan telegram:manage setup

# Opción 2: Usando script PHP
php telegram_setup.php
```

Verifica que el webhook esté configurado:

```bash
php artisan telegram:manage info
```

---

## ⚙️ Configuración

### Configuración de Telegram

El archivo `config/services.php` contiene:

```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
],
```

### Configuración de PagoPar

```php
'pagopar' => [
    'public_key' => env('PAGOPAR_PUBLIC_KEY'),
    'private_key' => env('PAGOPAR_PRIVATE_KEY'),
    'sandbox' => env('PAGOPAR_SANDBOX', true),
    'webhook_url' => env('PAGOPAR_WEBHOOK_URL'),
],
```

---

## 📱 Comandos del Bot

### Comandos Generales

| Comando | Descripción |
|---------|-------------|
| `/start` | Iniciar el bot y ver bienvenida |
| `/help` | Ver lista de comandos disponibles |
| `/link` | Obtener instrucciones para vincular cuenta |
| `/unlink` | Desvincular cuenta de Telegram |
| `/status` | Ver estado de tu cuenta y estadísticas |

### Comandos de Pagos

| Comando | Descripción |
|---------|-------------|
| `/pagar` | Generar enlace de pago para suscripción |
| `/suscripcion` | Ver estado detallado de suscripción |

### Envío de Facturas

No requiere comandos. Simplemente:
1. Envía el archivo PDF o foto de la factura
2. El bot confirmará la recepción
3. Procesamiento automático con IA
4. Notificación cuando termine

---

## 🚀 Flujo de Uso

### 1. Vincular Cuenta

**Opción A: Desde el Panel de Dataflow** (Pendiente implementar)
1. Usuario inicia sesión en https://dataflow.guaraniappstore.com
2. Va a Perfil → Telegram
3. Hace clic en "Generar código de vinculación"
4. Recibe código de 8 caracteres (ej: `A3F5K9L2`)
5. Abre Telegram, busca el bot
6. Envía el código
7. ¡Cuenta vinculada!

**Opción B: Desde Línea de Comandos** (Para admin)
```bash
php artisan telegram:manage link --email=usuario@ejemplo.com
```

### 2. Enviar Facturas

1. Abre el chat con el bot
2. Envía PDF o foto de la factura
3. Bot confirma recepción
4. Espera notificación de procesamiento completado
5. Revisa los datos extraídos
6. Archivo guardado en: `storage/contadores/{user_id}/facturas/{emisor}/{año}/{mes}/`

### 3. Gestionar Pagos

#### Ver Estado de Suscripción
```
/suscripcion
```

Muestra:
- Plan actual (basic/advanced)
- Precio
- Estado de la suscripción
- Estado del pago
- Fecha de vencimiento
- Uso de documentos

#### Generar Enlace de Pago
```
/pagar
```

El bot:
1. Verifica si necesitas pagar
2. Genera enlace con PagoPar
3. Envía enlace de pago
4. Espera confirmación automática via webhook

---

## 💳 Gestión de Pagos

### Flujo de Pago

```
Usuario → /pagar
      ↓
Bot genera enlace (PagoPar)
      ↓
Usuario realiza pago
      ↓
PagoPar envía webhook
      ↓
Sistema confirma pago
      ↓
Suscripción activada
      ↓
Notificación a usuario
```

### Estados de Pago

| Estado | Descripción |
|--------|-------------|
| `pending` | Enlace generado, esperando pago |
| `processing` | Pago en proceso de confirmación |
| `completed` | Pago confirmado, suscripción activa |
| `failed` | Pago rechazado |

### Webhooks de PagoPar

**Endpoint**: `https://dataflow.guaraniappstore.com/api/pagopar/webhook`

El sistema procesa automáticamente:
- Pagos aprobados → Activa suscripción
- Pagos rechazados → Notifica al usuario
- Pagos pendientes → Actualiza estado

---

## 🛠️ Comandos Artisan

### Gestión del Bot

```bash
# Ver información del bot
php artisan telegram:manage me

# Configurar webhook
php artisan telegram:manage setup

# Ver info del webhook
php artisan telegram:manage info

# Eliminar webhook
php artisan telegram:manage delete

# Generar código de vinculación
php artisan telegram:manage link --email=usuario@ejemplo.com
```

### Gestión de Suscripciones

```bash
# Verificar suscripciones vencidas
php artisan subscriptions:check-expired

# Con notificaciones automáticas
php artisan subscriptions:check-expired --notify

# Generar enlaces de pago automáticamente
php artisan subscriptions:check-expired --notify --auto-generate-links
```

### Programar Tareas (Cron)

Agrega al crontab o `app/Console/Kernel.php`:

```php
// Verificar suscripciones diariamente a las 9:00 AM
$schedule->command('subscriptions:check-expired --notify')
    ->dailyAt('09:00');
```

---

## 🔌 Webhooks

### Telegram Webhook

**URL**: `https://dataflow.guaraniappstore.com/api/telegram/webhook`

**Procesa**:
- Mensajes de texto (comandos y códigos)
- Documentos (PDF)
- Fotos (imágenes de facturas)
- Callback queries (botones inline)

**Controlador**: `App\Http\Controllers\Api\TelegramController`

### PagoPar Webhook

**URL**: `https://dataflow.guaraniappstore.com/api/pagopar/webhook`

**Procesa**:
- Confirmaciones de pago
- Rechazos de pago
- Actualizaciones de estado

**Controlador**: `App\Http\Controllers\Api\PagoParController`

---

## 📁 Estructura de Archivos

### Modelos
```
app/Models/
├── User.php                    # Campos de Telegram agregados
└── Subscription.php            # Campos de pago agregados
```

### Servicios
```
app/Services/
├── TelegramService.php         # Comunicación con API de Telegram
├── PagoParService.php          # Generación de enlaces de pago
└── OcrService.php              # Procesamiento OCR (ya existente)
```

### Controladores
```
app/Http/Controllers/Api/
├── TelegramController.php      # Webhook y comandos de Telegram
└── PagoParController.php       # Webhook de PagoPar
```

### Jobs
```
app/Jobs/
└── ProcessTelegramDocument.php # Procesamiento asíncrono de documentos
```

### Comandos
```
app/Console/Commands/
├── TelegramBotManage.php       # Gestión del bot
└── CheckExpiredSubscriptions.php # Verificar suscripciones
```

### Migraciones
```
database/migrations/
├── 2025_11_25_000001_add_telegram_fields_to_users_table.php
└── 2025_11_25_000002_add_payment_fields_to_subscriptions_table.php
```

### Rutas
```
routes/
└── api.php                     # Rutas de webhooks
```

---

## 🐛 Troubleshooting

### El bot no responde

1. Verifica que el webhook esté configurado:
   ```bash
   php artisan telegram:manage info
   ```

2. Revisa los logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Verifica las variables de entorno:
   ```bash
   php artisan config:cache
   ```

### Error al procesar documentos

1. Verifica que la cola esté ejecutándose:
   ```bash
   php artisan queue:work
   ```

2. Revisa el storage:
   ```bash
   php artisan storage:link
   chmod -R 775 storage/
   ```

3. Verifica OpenAI API Key:
   ```bash
   php artisan tinker
   >>> config('services.openai_api_key')
   ```

### Pagos no se confirman

1. Verifica el webhook de PagoPar:
   - URL debe ser HTTPS
   - Debe ser accesible desde internet
   - Verificar en panel de PagoPar

2. Revisa los logs de webhook:
   ```bash
   grep "PagoPar" storage/logs/laravel.log
   ```

### Usuario no puede vincular cuenta

1. Verifica que el código no haya expirado (15 min)
2. Genera nuevo código:
   ```bash
   php artisan telegram:manage link --email=usuario@ejemplo.com
   ```

---

## 📊 Monitoreo

### Logs Importantes

```bash
# Todos los logs
tail -f storage/logs/laravel.log

# Solo Telegram
tail -f storage/logs/laravel.log | grep "Telegram"

# Solo PagoPar
tail -f storage/logs/laravel.log | grep "PagoPar"

# Solo procesamiento de documentos
tail -f storage/logs/laravel.log | grep "ProcessTelegramDocument"
```

### Métricas

Revisa en la base de datos:
```sql
-- Usuarios con Telegram vinculado
SELECT COUNT(*) FROM users WHERE telegram_id IS NOT NULL;

-- Documentos procesados por Telegram
SELECT COUNT(*) FROM documents WHERE user_id IN
  (SELECT id FROM users WHERE telegram_id IS NOT NULL);

-- Pagos pendientes
SELECT COUNT(*) FROM subscriptions WHERE payment_status = 'pending';
```

---

## 🔒 Seguridad

### Mejores Prácticas

1. **Webhook Signature**: Implementar validación de firma de PagoPar
2. **Rate Limiting**: Limitar requests al webhook
3. **Validación de Usuario**: Siempre verificar que el usuario esté vinculado
4. **Logs**: Nunca loguear información sensible (tokens, API keys)
5. **HTTPS**: Obligatorio para webhooks

### Configuración Recomendada

```php
// En app/Http/Middleware/VerifyTelegramWebhook.php
// Validar que el request viene de Telegram
```

---

## 📝 Próximas Mejoras

- [ ] Panel web para generar códigos de vinculación
- [ ] Soporte para más métodos de pago
- [ ] Reportes mensuales automáticos por Telegram
- [ ] Comandos para consultar facturas específicas
- [ ] Integración con más pasarelas de pago
- [ ] Notificaciones programadas personalizables

---

## 📞 Soporte

Para soporte técnico:
- Email: dataflow@guaraniappstore.com
- Repositorio: https://github.com/Benifotrem/dataflow
- Documentación: https://dataflow.guaraniappstore.com/docs

---

## 📄 Licencia

Este proyecto es parte de Dataflow - Plataforma SaaS de Automatización Contable.

© 2025 Dataflow. Todos los derechos reservados.
