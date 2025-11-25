# 🚀 GUÍA DE DEPLOYMENT - DATAFLOW

## Configuración para: dataflow.guaraniappstore.com

---

## 📋 PRE-REQUISITOS

- ✅ Hosting compartido con soporte PHP 8.2+
- ✅ MySQL 8.0+
- ✅ Acceso SSH o cPanel
- ✅ Dominio: guaraniappstore.com (ya configurado)
- ✅ Subdominio: dataflow.guaraniappstore.com
- ✅ Certificado SSL activo

---

## 🔧 PASO 1: PREPARAR EL HOSTING

### En cPanel (o equivalente):

1. **Crear subdominio:**
   - Nombre: `dataflow`
   - Dominio raíz: `guaraniappstore.com`
   - Document Root: `/public_html/dataflow/public`

2. **Crear base de datos MySQL:**
   - Nombre: `guarani_dataflow`
   - Usuario: `guarani_dataflow`
   - Password: [genera uno seguro]
   - Host: `localhost`

3. **Configurar SSL:**
   - El SSL de guaraniappstore.com debe cubrir *.guaraniappstore.com
   - O instalar SSL específico para el subdominio

---

## 📦 PASO 2: SUBIR ARCHIVOS

### Opción A: Via Git (RECOMENDADO)

```bash
# Conectar por SSH al hosting
ssh tu_usuario@guaraniappstore.com

# Ir a directorio web
cd public_html

# Clonar repositorio
git clone https://github.com/Benifotrem/dataflow.git
cd dataflow

# Checkout al branch correcto
git checkout claude/dataflow-saas-platform-01Gogn2DJtLmkTPq15MUxMWf

# Instalar dependencias
composer install --optimize-autoloader --no-dev
```

### Opción B: Via FTP/SFTP

1. Comprimir el proyecto localmente (excluyendo node_modules y vendor)
2. Subir archivo .zip al servidor
3. Descomprimir en `/public_html/dataflow/`
4. Conectar por SSH y ejecutar: `composer install --optimize-autoloader --no-dev`

---

## ⚙️ PASO 3: CONFIGURAR .ENV

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar .env
nano .env
```

### Configuración .env para producción:

```env
APP_NAME=Dataflow
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://dataflow.guaraniappstore.com

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES

# Base de datos (ajustar según cPanel)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=guarani_dataflow
DB_USERNAME=guarani_dataflow
DB_PASSWORD=tu_password_mysql_aqui

# Email con Brevo
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_email_brevo@ejemplo.com
MAIL_PASSWORD=tu_password_smtp_brevo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="dataflow@guaraniappstore.com"
MAIL_FROM_NAME="Dataflow"

# API de OpenAI
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
AI_PROVIDER=openai
AI_MODEL=gpt-4o-mini

# Configuración Dataflow
DOCUMENT_LIMIT_BASE=500
DATA_RETENTION_DAYS=60
ADDON_PRICE_PER_500_DOCS=9.99

# Cache y Queue
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

---

## 🔑 PASO 4: GENERAR APP KEY Y CONFIGURAR

```bash
# Generar key de aplicación
php artisan key:generate

# Crear enlace simbólico para storage
php artisan storage:link

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 📁 PASO 5: CONFIGURAR PERMISOS

```bash
# Dar permisos de escritura
chmod -R 775 storage bootstrap/cache
chown -R tu_usuario:tu_usuario storage bootstrap/cache

# Si usas www-data
chown -R www-data:www-data storage bootstrap/cache
```

---

## ⏰ PASO 6: CONFIGURAR CRON (TAREAS AUTOMÁTICAS)

### En cPanel → Cron Jobs:

**Configuración:**
- Comando: `/usr/bin/php /home/tu_usuario/public_html/dataflow/artisan schedule:run >> /dev/null 2>&1`
- Frecuencia: `* * * * *` (cada minuto)

O manualmente en crontab:
```bash
crontab -e
```

Agregar línea:
```
* * * * * cd /home/tu_usuario/public_html/dataflow && php artisan schedule:run >> /dev/null 2>&1
```

**Tareas que se ejecutarán automáticamente:**
- 🔄 Eliminar extractos bancarios expirados (diario 2 AM)
- 🔄 Procesar documentos pendientes OCR (cada hora)
- 🔄 Verificar límites de documentos (diario 9 AM)

---

## 🌐 PASO 7: CONFIGURAR DOCUMENT ROOT

### En cPanel → Domains → Manage:

1. Seleccionar `dataflow.guaraniappstore.com`
2. Document Root: `/home/tu_usuario/public_html/dataflow/public`
3. Guardar cambios

### O crear .htaccess en raíz del subdominio:

Si no puedes cambiar el Document Root, crea `.htaccess` en `/public_html/dataflow/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 🔒 PASO 8: SEGURIDAD ADICIONAL

### Proteger archivos sensibles (.htaccess en raíz):

```apache
# Proteger archivos .env
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Proteger archivos de configuración
<FilesMatch "\.(env|json|config\.js|md|gitignore|gitattributes|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 📧 PASO 9: CONFIGURAR BREVO

1. **Login en Brevo**: https://app.brevo.com
2. **Settings → Senders & IP**
3. **Add Sender**:
   - Email: `dataflow@guaraniappstore.com`
   - Name: `Dataflow`
4. Como `guaraniappstore.com` ya está verificado, se aprueba automáticamente ✅
5. **Settings → SMTP & API**:
   - Copiar Login (tu email de cuenta Brevo)
   - Generar Master Password
   - Usar estos datos en .env

---

## ✅ PASO 10: VERIFICAR DEPLOYMENT

### Test de rutas:
```bash
php artisan route:list
```

### Test de base de datos:
```bash
php artisan migrate:status
```

### Test de comandos:
```bash
php artisan list | grep dataflow
```

### Acceder a la aplicación:
- https://dataflow.guaraniappstore.com
- https://dataflow.guaraniappstore.com/pricing
- https://dataflow.guaraniappstore.com/faq

---

## 🐛 TROUBLESHOOTING

### Error 500:
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Permisos:
```bash
chmod -R 775 storage bootstrap/cache
```

### Base de datos no conecta:
- Verificar credenciales en .env
- Verificar que el usuario MySQL tenga permisos
- Verificar que DB_HOST sea 'localhost' o '127.0.0.1'

### Composer no funciona:
```bash
# Usar composer.phar si el global no funciona
php composer.phar install --optimize-autoloader --no-dev
```

---

## 📊 MONITOREO POST-DEPLOYMENT

### Verificar tareas programadas:
```bash
# Ver últimos logs de scheduler
tail -f storage/logs/laravel.log | grep schedule
```

### Verificar uso de disco:
```bash
du -sh storage/
```

### Verificar base de datos:
```sql
SHOW TABLES;
SELECT COUNT(*) FROM tenants;
SELECT COUNT(*) FROM documents;
```

---

## 🔄 ACTUALIZACIONES FUTURAS

Para actualizar la aplicación:

```bash
cd /home/tu_usuario/public_html/dataflow

# Modo mantenimiento
php artisan down

# Actualizar código
git pull origin claude/dataflow-saas-platform-01Gogn2DJtLmkTPq15MUxMWf

# Actualizar dependencias
composer install --optimize-autoloader --no-dev

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Salir de mantenimiento
php artisan up
```

---

## ✅ CHECKLIST FINAL

- [ ] Subdominio configurado
- [ ] SSL activo
- [ ] Base de datos creada
- [ ] Archivos subidos
- [ ] .env configurado
- [ ] APP_KEY generado
- [ ] Migraciones ejecutadas
- [ ] Storage link creado
- [ ] Permisos configurados
- [ ] Cron configurado
- [ ] Brevo configurado y verificado
- [ ] OpenAI API Key configurado
- [ ] Cache optimizado
- [ ] Sitio accesible

---

## 🎉 ¡DEPLOYMENT COMPLETADO!

Tu aplicación estará disponible en:
**https://dataflow.guaraniappstore.com**

Para soporte: Revisar `storage/logs/laravel.log`

---

Desarrollado por: Claude (Anthropic)
Fecha: 18 de Noviembre de 2025
