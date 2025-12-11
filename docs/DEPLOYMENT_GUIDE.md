# 🚀 Guía de Despliegue - Nuevas Funcionalidades

## ✅ Lo que acabamos de implementar:

1. ✅ **Conversión automática de PDF a imagen**
2. ✅ **Asistente conversacional con IA para asesoría RG-90**
3. ✅ **Sistema de guardado de conversaciones**
4. ✅ **Exportación y envío de conversaciones por email**
5. ✅ **Validación estricta de archivos** (solo PDF, JPG, PNG)

---

## 🚀 DESPLIEGUE RÁPIDO (Método Recomendado)

```bash
ssh u489458217@tu-servidor.hostinger.com
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

# 1. Actualizar código
git pull origin claude/aranduka-core-architecture-013R2N35J7x7K8PwQETakzRW

# 2. Ejecutar migración
php artisan migrate --force

# 3. Resolver dependencias y limpiar caché
./fix-composer.sh

# 4. Probar conversión de PDF
php test-pdf-conversion.php

# 5. Reiniciar webhook
./restart-webhook.sh

# 6. Probar en Telegram
# Envía un PDF al bot y pregunta: "¿Qué es RG-90?"
```

**¡Listo!** Si todos los scripts pasan, tu bot está funcionando.

---

## 📦 Paso 1: Actualizar Código en Producción (Manual)

Si prefieres hacerlo paso a paso:

```bash
ssh u489458217@tu-servidor.hostinger.com
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

# Actualizar código
git pull origin claude/aranduka-core-architecture-013R2N35J7x7K8PwQETakzRW

# Instalar dependencias
rm composer.lock
composer install --no-dev --optimize-autoloader
```

---

## 🔧 Paso 2: Instalar Imagick (CRÍTICO para PDFs)

### Opción A: Hostinger (contactar soporte)

```text
Asunto: Instalación de PHP Imagick Extension

Hola,

Necesito que instalen las siguientes extensiones en mi cuenta:
- PHP Imagick extension
- Ghostscript

Dominio: dataflow.guaraniappstore.com
Usuario: u489458217

Gracias
```

### Opción B: Acceso SSH/Root

```bash
sudo apt-get update
sudo apt-get install -y php8.2-imagick ghostscript
sudo systemctl restart php8.2-fpm
```

### Verificar instalación:
```bash
php -m | grep imagick
# Debe mostrar: imagick
```

---

## 🗄️ Paso 3: Ejecutar Migración

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
php artisan migrate --force
```

Esto crea la tabla `telegram_conversations` para guardar las conversaciones.

---

## 📧 Paso 4: Configurar Email (si no está configurado)

Edita `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password-de-gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Dataflow Bot"
```

**Nota:** Para Gmail, necesitas una [App Password](https://support.google.com/accounts/answer/185833).

---

## 🧹 Paso 5: Limpiar Caché

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 🧪 Paso 6: Probar Funcionalidades

### A) Probar conversión de PDF

1. Abre tu bot en Telegram
2. Envía un PDF de factura
3. Verifica que se procese correctamente

**Si falla con error de Imagick:** Necesitas instalarlo (Paso 2)

### B) Probar conversación

**Tú:** "¿Qué es el timbrado en Paraguay?"

**Bot:** (Debe responder explicando qué es el timbrado)

### C) Exportar conversaciones por email

```bash
php artisan telegram:export-conversations --email=tu-email@gmail.com
```

Revisa tu bandeja de entrada.

---

## ⚙️ Paso 7: Programar Envío Automático de Conversaciones

### Agregar a Crontab

```bash
crontab -e
```

Agrega esta línea (enviar reporte diario a las 23:00):

```cron
0 23 * * * cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && php artisan telegram:export-conversations --email=contador@estudio.com --since=today >> /dev/null 2>&1
```

O para reporte semanal (lunes a las 9am):

```cron
0 9 * * 1 cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && php artisan telegram:export-conversations --email=contador@estudio.com --since="7 days ago" >> /dev/null 2>&1
```

---

## 🎯 Resumen de Cambios

### Antes:
- ❌ PDFs eran rechazados
- ❌ Bot solo procesaba facturas (no conversaba)
- ❌ No se guardaban conversaciones
- ✅ Solo aceptaba imágenes JPG/PNG

### Ahora:
- ✅ **PDFs se convierten automáticamente a imagen**
- ✅ **Bot conversa inteligentemente sobre RG-90**
- ✅ **Todas las conversaciones se guardan**
- ✅ **Puedes exportar conversaciones por email**
- ✅ **Sigue aceptando JPG/PNG**
- ✅ **Rechaza cualquier otro formato**

---

## 📊 Ejemplos de Uso

### 1. Usuario envía PDF
```
Usuario: [envía factura.pdf]
Bot: ✅ Documento recibido
     📄 Archivo: factura.pdf
     ⏳ Procesando con IA...

     [Convierte PDF → imagen → OCR → validación]

Bot: ✅ Factura procesada exitosamente!
     🆔 ID: #674
     • RUC: 2494738-5
     • Total: ₲ 90.000
     ...
```

### 2. Usuario hace pregunta
```
Usuario: ¿Qué documentos necesito para cumplir con RG-90?
Bot: Para cumplir con la RG-90, necesitas registrar en el sistema Marangatu:
     1. Facturas de venta con timbrado vigente
     2. Notas de crédito/débito
     3. Comprobantes de compra
     ...
```

### 3. Exportar conversaciones
```bash
$ php artisan telegram:export-conversations --email=contador@estudio.com

📧 Exportando conversaciones de Telegram...
📊 Se encontraron 3 conversaciones.
  ✓ Exportada conversación de Juan Pérez (Chat ID: 123456)
  ✓ Exportada conversación de María López (Chat ID: 789012)
  ✓ Exportada conversación de Pedro Gómez (Chat ID: 345678)
✅ Email enviado exitosamente a: contador@estudio.com
```

---

## 🆘 Solución de Problemas

### ❌ Error: "Imagick extension no está instalada"
**Solución:** Completa el Paso 2 (instalar Imagick)

### ❌ Error al convertir PDF
**Posibles causas:**
- PDF protegido con contraseña → Pide al usuario que envíe foto
- PDF corrupto → Pide archivo nuevo
- Falta Ghostscript → Instala Ghostscript

### ❌ Email no se envía
1. Verifica configuración SMTP en `.env`
2. Revisa logs: `tail -f storage/logs/laravel.log`
3. Verifica que la queue esté corriendo: `php artisan queue:work`

### ❌ Bot no responde conversaciones
1. Verifica que la migración se ejecutó
2. Revisa logs: `tail -f storage/logs/laravel.log`
3. Verifica API key de OpenAI en la base de datos

---

## 📞 Siguiente Paso

Una vez completados todos los pasos:

```bash
# Prueba enviando al bot:
1. Un PDF de factura
2. Una pregunta: "¿Qué es RG-90?"
3. Exporta: php artisan telegram:export-conversations --email=tu-email@test.com
```

¡Listo! 🎉
