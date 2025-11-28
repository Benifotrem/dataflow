# Guía Completa de Despliegue - Sistema de Notificaciones

## 📋 Resumen de Cambios

Esta actualización incluye:

✅ **Sistema completo de notificaciones por email usando Brevo:**
- Verificación de email para nuevos usuarios
- Recuperación de contraseña
- Notificaciones cuando un documento es procesado
- Informes mensuales automáticos

✅ **Configuración de Telegram en perfil de usuario**
✅ **Límites de entidades fiscales según plan de suscripción**
✅ **Información de plan en perfil de usuario**
✅ **Correcciones de navegación y botones**

---

## 🚀 Paso 1: Conectarse a Producción

Abre tu terminal local y conéctate a Hostinger:

```bash
ssh -p 65002 u489458217@147.93.37.28
```

Una vez conectado, ve al directorio de tu aplicación:

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
```

---

## 📥 Paso 2: Descargar Cambios del Repositorio

### Opción A: Usando el script automatizado (RECOMENDADO)

```bash
# Descargar el script
git fetch origin
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

# Dar permisos de ejecución
chmod +x DEPLOY_NOTIFICACIONES.sh

# Ejecutar
bash DEPLOY_NOTIFICACIONES.sh
```

### Opción B: Paso a paso manual

```bash
# 1. Obtener últimos cambios
git fetch origin
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

# 2. Instalar dependencias
composer install --no-dev --optimize-autoloader

# 3. Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 4. Generar cachés optimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Limpiar cola antigua
php artisan queue:clear
```

---

## ⚙️ Paso 3: Configurar Variables de Entorno (.env)

Edita el archivo `.env` en producción:

```bash
nano .env
```

### Variables CRÍTICAS a verificar/cambiar:

```env
# 1. CAMBIAR de database a sync
QUEUE_CONNECTION=sync

# 2. Verificar configuración de Brevo
BREVO_API_KEY=tu_api_key_aqui

# 3. Verificar email remitente
MAIL_FROM_ADDRESS=dataflow@guaraniappstore.com
MAIL_FROM_NAME=Dataflow

# 4. Verificar configuración SMTP de Brevo
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_email_brevo
MAIL_PASSWORD=tu_smtp_key_brevo
MAIL_ENCRYPTION=tls
```

### ⚠️ IMPORTANTE: Cómo obtener las credenciales de Brevo

1. Ve a https://app.brevo.com/
2. Inicia sesión con tu cuenta
3. **Para API Key:**
   - Ve a Settings → API Keys
   - Copia tu API key v3
   - Pégala en `BREVO_API_KEY`

4. **Para SMTP:**
   - Ve a Settings → SMTP & API
   - Copia tu login SMTP en `MAIL_USERNAME`
   - Copia tu clave SMTP en `MAIL_PASSWORD`

Guarda los cambios: `Ctrl+O`, `Enter`, `Ctrl+X`

### Aplicar cambios:

```bash
php artisan config:cache
```

---

## ✅ Paso 4: Verificar Despliegue

Ejecuta el script de pruebas:

```bash
chmod +x TEST_NOTIFICACIONES.sh
bash TEST_NOTIFICACIONES.sh
```

Deberías ver todas las verificaciones en verde ✓

---

## 📧 Paso 5: Probar Notificaciones

### A) Probar Verificación de Email (Registro)

1. Abre una ventana de incógnito en tu navegador
2. Ve a: https://dataflow.guaraniappstore.com/register
3. Registra un nuevo usuario con tu email real
4. **Deberías recibir un email de verificación**
5. Click en el link del email
6. Deberías ser redirigido y ver tu cuenta verificada

### B) Probar Recuperación de Contraseña

1. Ve a: https://dataflow.guaraniappstore.com/login
2. Click en "¿Olvidaste tu contraseña?"
3. Ingresa tu email
4. **Deberías recibir un email con link de recuperación**
5. Click en el link
6. Cambia tu contraseña

### C) Probar Notificación de Documento

1. Sube un documento vía Telegram al bot
2. Espera a que se procese (1-2 minutos)
3. **Deberías recibir un email notificándote**
4. El email incluye link directo al documento

### D) Probar Informe Mensual (Opcional)

Para probar con un tenant específico:

```bash
php artisan reports:send-monthly --tenant=1
```

Esto enviará un informe del mes anterior al propietario del tenant ID 1.

---

## ⏰ Paso 6: Configurar CRON para Informes Mensuales

Los informes mensuales se envían **automáticamente el día 1 de cada mes** usando un cron job.

### Configurar en Hostinger hPanel:

1. **Acceder a hPanel:**
   - Inicia sesión en hPanel de Hostinger
   - Ve a: **Avanzado** → **Cron Jobs**

2. **Crear nuevo Cron Job:**
   - Click en **"Create new Cron Job"**

3. **Configuración:**

   **Frecuencia (Schedule):**
   ```
   0 0 1 * *
   ```

   O si hay selector visual:
   - Tipo: Mensual (Monthly)
   - Día: 1
   - Hora: 00:00 (medianoche)

   **Comando (Command):**
   ```bash
   cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && /usr/bin/php artisan reports:send-monthly
   ```

   **Email notifications:** Deja tu email si quieres recibir confirmación cada vez que se ejecuta

4. **Guardar:**
   - Click en **"Create"** o **"Save"**

5. **Verificar:**
   - En la lista de cron jobs deberías ver:
     - **Próxima ejecución:** 1 del próximo mes a las 00:00
     - **Estado:** Activo

### Notas sobre el CRON:

- Se ejecutará automáticamente el día 1 de cada mes
- Enviará informes del mes anterior a todos los tenants activos
- Incluye: documentos procesados, transacciones, ingresos, gastos, balance
- Los emails se envían a los propietarios de cada tenant

---

## 🔍 Paso 7: Verificar en Logs

Monitorear si hay errores:

```bash
# Ver últimas líneas del log de Laravel
tail -f storage/logs/laravel.log

# Para salir: Ctrl+C
```

Buscar mensajes como:
- ✅ "Email de verificación enviado a: email@ejemplo.com"
- ✅ "Email de reset de contraseña enviado a: email@ejemplo.com"
- ✅ "Email de notificación de documento enviado"
- ❌ Si ves errores de Brevo, verifica tu API Key

---

## 🎯 Resumen de URLs Importantes

| Funcionalidad | URL |
|--------------|-----|
| Registro | https://dataflow.guaraniappstore.com/register |
| Login | https://dataflow.guaraniappstore.com/login |
| Recuperar contraseña | https://dataflow.guaraniappstore.com/password/reset |
| Verificar email | Se envía por email después del registro |
| Mi Perfil | https://dataflow.guaraniappstore.com/profile |
| Configuración Telegram | En Mi Perfil → Sección Telegram |

---

## 📊 Estado del Sistema después del Despliegue

### ✅ Funcionando automáticamente:

1. **Registro nuevo usuario** → Email de verificación (inmediato)
2. **Recuperar contraseña** → Email con link (inmediato)
3. **Documento procesado** → Email de notificación (inmediato)
4. **Día 1 de cada mes** → Informes mensuales (automático vía cron)

### 🔧 Configuraciones aplicadas:

- `QUEUE_CONNECTION=sync` - Envío inmediato sin worker
- Rutas de verificación de email activas
- User implements MustVerifyEmail
- BrevoService configurado con 6 métodos
- Comando `reports:send-monthly` registrado
- Telegram link/unlink en perfil
- Límites de entidades por plan activos

---

## 🆘 Troubleshooting

### ❌ No llegan los emails

**Posibles causas:**

1. **Brevo API Key incorrecta**
   ```bash
   # Verificar en tinker:
   php artisan tinker
   > $brevo = new App\Services\BrevoService();
   > $brevo->isConfigured()
   # Debe retornar: true
   ```

2. **Email remitente no verificado en Brevo**
   - Ve a Brevo → Senders
   - Verifica que `dataflow@guaraniappstore.com` esté verificado
   - Si no, agrega y verifica el dominio

3. **Emails en spam**
   - Revisa la carpeta de spam
   - Marca como "No spam"

4. **Error en logs**
   ```bash
   tail -50 storage/logs/laravel.log
   ```
   Busca errores relacionados con "Brevo" o "email"

### ❌ Error 500 al registrarse

```bash
# Ver error exacto:
tail -50 storage/logs/laravel.log

# Limpiar cachés:
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### ❌ No se crea el cron job

- Verifica que tu plan de Hostinger incluya cron jobs
- Algunos planes básicos no los incluyen
- Alternativa: Usar un servicio externo como cron-job.org

### ❌ Comando reports:send-monthly no encontrado

```bash
# Verificar que el comando esté registrado:
php artisan list | grep reports

# Deberías ver:
# reports:send-monthly    Enviar informes mensuales...

# Si no aparece, regenerar autoload:
composer dump-autoload
php artisan config:cache
```

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa los logs: `tail -50 storage/logs/laravel.log`
2. Verifica la configuración de Brevo en su panel
3. Asegúrate de que `QUEUE_CONNECTION=sync`
4. Verifica que el caché esté actualizado: `php artisan config:cache`

---

## ✨ Siguiente Paso

Una vez completado todo:

1. ✅ Despliegue realizado
2. ✅ Configuración de .env actualizada
3. ✅ Brevo configurado
4. ✅ Cron configurado
5. ✅ Pruebas realizadas

**El sistema está 100% operativo y las notificaciones funcionarán automáticamente.**

---

**Última actualización:** 2025-11-28
**Versión:** 1.0
**Branch:** claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU
