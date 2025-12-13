# 📱 Guía de Configuración del Bot de Telegram - Aranduka

## 🎯 Objetivo

Configurar el bot de Telegram para que los clientes puedan enviar fotos de facturas y recibir validación automática con OCR y verificación fiscal.

---

## 📋 Requisitos Previos

- ✅ Cuenta de Telegram
- ✅ Acceso al servidor de producción
- ✅ OpenAI API Key (para OCR)
- ✅ Sistema Aranduka desplegado

---

## 🤖 Paso 1: Crear el Bot de Telegram

### 1.1 Hablar con BotFather

1. Abre Telegram y busca: **@BotFather**
2. Inicia conversación: `/start`
3. Crea nuevo bot: `/newbot`
4. Sigue las instrucciones:
   - **Nombre del bot**: `Aranduka Fiscal Bot` (o el que prefieras)
   - **Username del bot**: `aranduka_fiscal_bot` (debe terminar en "bot")

### 1.2 Obtener el Token

BotFather te dará un **token** como este:
```
123456789:ABCdefGHIjklMNOpqrsTUVwxyz
```

⚠️ **IMPORTANTE**: Guarda este token de forma segura. Es como una contraseña.

### 1.3 Configurar el Bot (Opcional)

```
/setdescription - Descripción del bot
/setabouttext - Texto "Acerca de"
/setuserpic - Foto del bot
```

Descripción sugerida:
```
🇵🇾 Bot de validación fiscal automática para Paraguay.
Envía fotos de facturas y recibe validación instantánea con OCR + DNIT.
```

---

## 🔐 Paso 2: Configurar en el Servidor

### 2.1 Conectar por SSH

```bash
ssh u489458217@tu-servidor.hostinger.com
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
```

### 2.2 Editar archivo .env

```bash
nano .env
```

Busca la línea `TELEGRAM_BOT_TOKEN=` y agrégale tu token:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_WEBHOOK_URL=https://dataflow.guaraniappstore.com/api/telegram/webhook
```

Guarda con `Ctrl + O`, luego `Enter`, y sal con `Ctrl + X`.

### 2.3 Limpiar caché

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🌐 Paso 3: Configurar Webhook

### 3.1 Registrar Webhook en Telegram

Ejecuta este comando en el servidor:

```bash
php artisan telegram:set-webhook
```

Deberías ver:
```
✅ Webhook configurado exitosamente
URL: https://dataflow.guaraniappstore.com/api/telegram/webhook
```

### 3.2 Verificar Webhook (Opcional)

```bash
curl "https://api.telegram.org/bot<TU_TOKEN>/getWebhookInfo"
```

Reemplaza `<TU_TOKEN>` con tu token real.

---

## 🧪 Paso 4: Probar el Bot

### 4.1 Buscar tu Bot

1. Abre Telegram
2. Busca: `@aranduka_fiscal_bot` (o el username que elegiste)
3. Clic en "Start" o envía `/start`

### 4.2 Comandos Disponibles

```
/start - Iniciar el bot y ver instrucciones
/help - Ver ayuda y comandos disponibles
/status - Ver estado del sistema
```

### 4.3 Enviar Factura de Prueba

1. Toma una foto de una factura paraguaya (RG-90)
2. Envíala al bot como **foto** o **documento**
3. Espera la respuesta (15-30 segundos)

**Respuesta esperada:**
```
✅ Factura procesada exitosamente

📄 DATOS EXTRAÍDOS:
RUC Emisor: 80012345-6
Razón Social: EMPRESA EJEMPLO SA
Timbrado: 12345678
Factura N°: 001-001-0001234
Fecha: 10/12/2025
Monto Total: ₲ 1.500.000

🔍 VALIDACIÓN FISCAL:
✅ RUC: Válido
✅ Timbrado: Vigente
✅ Estado: APROBADO
```

---

## 🔧 Paso 5: Configuración Avanzada (Opcional)

### 5.1 Restringir Acceso a Usuarios Específicos

Edita `config/telegram.php`:

```php
'allowed_users' => [
    123456789, // ID de usuario de Telegram
    987654321,
],
```

Para obtener tu ID de usuario, envía cualquier mensaje al bot y revisa los logs:

```bash
tail -f storage/logs/laravel.log | grep telegram_user_id
```

### 5.2 Configurar Notificaciones

En `.env`:

```env
# Notificaciones de administrador
TELEGRAM_ADMIN_CHAT_ID=123456789
TELEGRAM_NOTIFY_ADMIN=true
```

### 5.3 Ajustar Timeouts

En `.env`:

```env
# Timeouts para procesamiento
QUEUE_TIMEOUT=600
OCR_TIMEOUT=30
DNIT_TIMEOUT=30
```

---

## 📊 Paso 6: Monitorear el Bot

### 6.1 Ver Logs en Tiempo Real

```bash
tail -f storage/logs/laravel.log
```

### 6.2 Ver Cola de Jobs

```bash
php artisan queue:work --tries=3 --timeout=600
```

O configurar como servicio permanente (recomendado para producción).

### 6.3 Ver Estadísticas

```bash
php artisan tinker
```

```php
// Ver total de facturas procesadas
\DB::table('documents')->where('source', 'telegram')->count();

// Ver facturas del día
\DB::table('documents')
    ->where('source', 'telegram')
    ->whereDate('created_at', today())
    ->count();

// Ver tasa de éxito
\DB::table('documents')
    ->where('source', 'telegram')
    ->selectRaw('
        status,
        COUNT(*) as total,
        ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as porcentaje
    ')
    ->groupBy('status')
    ->get();
```

---

## ⚠️ Troubleshooting

### Problema: Bot no responde

**Solución 1**: Verificar webhook
```bash
php artisan telegram:set-webhook
```

**Solución 2**: Verificar logs
```bash
tail -50 storage/logs/laravel.log
```

**Solución 3**: Verificar token
```bash
grep TELEGRAM_BOT_TOKEN .env
```

### Problema: "Error processing image"

**Causas comunes**:
- Imagen muy grande (>20MB)
- Formato no soportado
- OpenAI API Key inválida

**Solución**:
```bash
# Verificar API Key
php artisan tinker --execute="
\$key = config('services.openai.key');
echo 'OpenAI Key: ' . substr(\$key, 0, 10) . '...' . PHP_EOL;
"
```

### Problema: "Validation failed"

**Causa**: Base de datos RUC vacía o DNIT no responde

**Solución**:
```bash
# Verificar datos RUC
php artisan tinker --execute="
echo 'RUCs en BD: ' . \DB::table('ruc_contribuyentes')->count() . PHP_EOL;
"
```

---

## 🔒 Seguridad

### ✅ Buenas Prácticas

1. **Nunca compartas tu token** en público o repositorios
2. **Usa HTTPS** siempre (ya configurado)
3. **Valida usuarios** si es para uso interno
4. **Monitorea logs** regularmente
5. **Backups** de la base de datos

### 🚨 Si el Token se Compromete

1. Habla con @BotFather
2. Usa `/revoke` para obtener nuevo token
3. Actualiza `.env` con el nuevo token
4. Ejecuta `php artisan telegram:set-webhook`

---

## 📞 Comandos Útiles

```bash
# Ver info del webhook
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo" | jq

# Eliminar webhook (para desarrollo local)
curl "https://api.telegram.org/bot<TOKEN>/deleteWebhook"

# Obtener actualizaciones manualmente (sin webhook)
curl "https://api.telegram.org/bot<TOKEN>/getUpdates"

# Ver info del bot
curl "https://api.telegram.org/bot<TOKEN>/getMe"
```

---

## 🎉 ¡Listo!

Tu bot de Telegram está configurado y listo para:
- ✅ Recibir fotos de facturas
- ✅ Extraer datos con OCR (OpenAI Vision)
- ✅ Validar con DNIT Paraguay
- ✅ Responder automáticamente

**Siguiente paso**: Comparte el enlace del bot con tus clientes:
```
https://t.me/aranduka_fiscal_bot
```

---

## 📚 Recursos Adicionales

- [Documentación Telegram Bot API](https://core.telegram.org/bots/api)
- [BotFather Commands](https://core.telegram.org/bots#6-botfather)
- [Telegram Bot PHP SDK](https://telegram-bot-sdk.readme.io/)

---

**Desarrollado para Aranduka-Core Platform** 🇵🇾
Sistema de gestión fiscal inteligente para Paraguay
