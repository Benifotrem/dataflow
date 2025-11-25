# 🚀 GUÍA DE DEPLOYMENT - DATAFLOW (VERSIÓN COMPLETA)

## Branch a desplegar: `claude/finish-website-017QFURjJdFkpfiAEC9cJAVL`

Esta guía te llevará paso a paso para implementar la versión completa de Dataflow en producción.

---

## 📋 INFORMACIÓN DEL SERVIDOR

- **Servidor:** Hostinger (hPanel)
- **Usuario SSH:** u489458217
- **IP/Host:** 185.201.11.61
- **Puerto SSH:** 65002
- **Dominio:** dataflow.guaraniappstore.com
- **Ruta del proyecto:** `/home/u489458217/domains/dataflow.guaraniappstore.com/`

---

## 🎯 PASO 1: CONECTAR AL SERVIDOR

```bash
ssh u489458217@185.201.11.61 -p 65002
```

**Password:** (tu password de Hostinger)

---

## 📦 PASO 2: ACTUALIZAR EL CÓDIGO

```bash
# Ir al directorio del proyecto
cd /home/u489458217/domains/dataflow.guaraniappstore.com

# Modo mantenimiento (opcional, si ya hay usuarios)
php artisan down --message="Actualizando a nueva versión" --retry=60

# Guardar cambios locales si existen
git stash

# Actualizar repositorio
git fetch origin

# Cambiar al branch nuevo con TODOS los cambios
git checkout claude/finish-website-017QFURjJdFkpfiAEC9cJAVL

# Pull de los últimos cambios
git pull origin claude/finish-website-017QFURjJdFkpfiAEC9cJAVL

# Restaurar cambios locales si los había
git stash pop
```

---

## 🔧 PASO 3: ACTUALIZAR DEPENDENCIAS

```bash
# Limpiar caché de composer
rm -rf vendor/
rm composer.lock 2>/dev/null || true

# Instalar dependencias optimizadas para producción
composer install --no-dev --optimize-autoloader

# Verificar que todo se instaló correctamente
composer dump-autoload
```

---

## 🎨 PASO 4: COMPILAR ASSETS FRONTEND

```bash
# Instalar dependencias de Node.js
npm install

# Compilar assets para producción
npm run build

# Copiar assets compilados a public_html
cp -r public/build public_html/ 2>/dev/null || true
```

---

## 🗄️ PASO 5: EJECUTAR MIGRACIONES

```bash
# Ver estado de migraciones
php artisan migrate:status

# Ejecutar nuevas migraciones (si las hay)
php artisan migrate --force

# IMPORTANTE: Si es la primera vez, esto creará todas las tablas:
# - tenants
# - users
# - entities
# - documents
# - transactions
# - bank_statements
# - fiscal_deadlines
# - ai_usage
# - addons
# - subscriptions
# - system_settings
```

---

## ⚙️ PASO 6: CONFIGURAR .ENV (Si no está configurado)

```bash
# Verificar configuración actual
cat .env | grep APP_KEY

# Si APP_KEY está vacío, generar uno nuevo
php artisan key:generate --force

# Verificar variables críticas
nano .env
```

**Verificar estas variables:**

```env
APP_NAME=Dataflow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dataflow.guaraniappstore.com

# Base de datos
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u489458217_dataflow
DB_USERNAME=u489458217_dataflow
DB_PASSWORD=tu_password_aqui

# Email (Brevo)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@ejemplo.com
MAIL_PASSWORD=tu_smtp_password
MAIL_FROM_ADDRESS="dataflow@guaraniappstore.com"
MAIL_FROM_NAME="Dataflow"

# API de OpenAI (para OCR de documentos)
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
AI_PROVIDER=openai
AI_MODEL=gpt-4o-mini

# Configuración Dataflow
DOCUMENT_LIMIT_BASE=500
DATA_RETENTION_DAYS=60
ADDON_PRICE_PER_500_DOCS=9.99

# Sesiones y Cache
SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=database
```

---

## 🔄 PASO 7: ACTUALIZAR PUBLIC_HTML

```bash
# Copiar archivos públicos actualizados
rsync -av public/ public_html/ --exclude=storage

# Verificar que index.php existe
ls -la public_html/index.php

# Verificar .htaccess
ls -la public_html/.htaccess
```

---

## 🧹 PASO 8: LIMPIAR Y OPTIMIZAR CACHÉ

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Crear cachés optimizadas para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimizar autoload de composer
composer dump-autoload --optimize
```

---

## 🔐 PASO 9: VERIFICAR PERMISOS

```bash
# Dar permisos de escritura a storage y cache
chmod -R 775 storage bootstrap/cache

# Cambiar propietario (usa tu usuario de Hostinger)
chown -R u489458217:u489458217 storage bootstrap/cache

# Verificar permisos
ls -la storage/
```

---

## ⏰ PASO 10: CONFIGURAR CRON JOB (Si no está configurado)

**En hPanel → Advanced → Cron Jobs:**

**Comando:**
```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com && php artisan schedule:run >> /dev/null 2>&1
```

**Frecuencia:** `* * * * *` (cada minuto)

**Esto ejecutará automáticamente:**
- Eliminación de extractos bancarios expirados (diario 2 AM)
- Procesamiento de documentos OCR pendientes (cada hora)
- Verificación de límites de documentos (diario 9 AM)

---

## ✅ PASO 11: CREAR USUARIO ADMINISTRADOR (PRIMERA VEZ)

```bash
# Conectar a MySQL
mysql -u u489458217_dataflow -p u489458217_dataflow

# Ejecutar en MySQL:
```

```sql
-- Crear tenant de prueba
INSERT INTO tenants (name, type, country, currency, status, created_at, updated_at)
VALUES ('Administración', 'b2b', 'ES', 'EUR', 'active', NOW(), NOW());

-- Crear usuario admin (usa el ID del tenant creado arriba)
INSERT INTO users (tenant_id, name, email, password, role, created_at, updated_at)
VALUES (1, 'Admin', 'admin@dataflow.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- Password por defecto: "password" (cambiar después del login)

exit;
```

---

## 🎉 PASO 12: SALIR DE MODO MANTENIMIENTO Y VERIFICAR

```bash
# Salir de modo mantenimiento
php artisan up

# Verificar que el sitio está funcionando
curl -I https://dataflow.guaraniappstore.com

# Ver logs en tiempo real (Ctrl+C para salir)
tail -f storage/logs/laravel.log
```

---

## 🌐 PASO 13: VERIFICAR EN EL NAVEGADOR

Abre tu navegador y verifica estas URLs:

### Landing Pages:
- ✅ https://dataflow.guaraniappstore.com (Homepage)
- ✅ https://dataflow.guaraniappstore.com/pricing (Página de precios)
- ✅ https://dataflow.guaraniappstore.com/faq (FAQ)
- ✅ https://dataflow.guaraniappstore.com/terms (Términos)
- ✅ https://dataflow.guaraniappstore.com/privacy (Privacidad)

### Autenticación:
- ✅ https://dataflow.guaraniappstore.com/login (Login)
- ✅ https://dataflow.guaraniappstore.com/register (Registro)

### Dashboard (después de login):
- ✅ /dashboard (Dashboard principal)
- ✅ /entities (Entidades fiscales)
- ✅ /documents (Documentos)
- ✅ /transactions (Transacciones)
- ✅ /bank-statements (Extractos bancarios)
- ✅ /admin/dashboard (Panel admin - solo para admin)

---

## 🔍 VERIFICACIÓN POST-DEPLOYMENT

### Test de Base de Datos
```bash
php artisan migrate:status
php artisan db:show
```

### Test de Rutas
```bash
php artisan route:list | grep -E "(entities|documents|transactions|bank-statements)"
```

### Test de Configuración
```bash
php artisan config:show app
php artisan config:show database
```

### Ver Comandos Disponibles
```bash
php artisan list | grep dataflow
```

Deberías ver:
- `dataflow:check-limits` - Verificar límites de documentos
- `dataflow:delete-expired-statements` - Eliminar extractos expirados
- `dataflow:process-documents` - Procesar documentos pendientes

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Error 500 - Internal Server Error

```bash
# Ver logs detallados
tail -100 storage/logs/laravel.log

# Limpiar todo el caché
php artisan optimize:clear

# Regenerar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permisos
chmod -R 775 storage bootstrap/cache
```

### Error 404 - Not Found

```bash
# Verificar que public_html tiene archivos
ls -la public_html/

# Verificar .htaccess
cat public_html/.htaccess

# Regenerar caché de rutas
php artisan route:clear
php artisan route:cache
```

### Assets no cargan (CSS/JS)

```bash
# Verificar que build existe
ls -la public_html/build/

# Si no existe, compilar y copiar
npm run build
cp -r public/build public_html/

# Limpiar caché del navegador
```

### Base de datos no conecta

```bash
# Verificar credenciales
cat .env | grep DB_

# Test de conexión
php artisan db:show

# Si falla, verificar en hPanel que la base de datos existe
```

### Vistas Blade no renderizan

```bash
# Limpiar caché de vistas
php artisan view:clear

# Verificar que las vistas existen
ls -la resources/views/dashboard/entities/
ls -la resources/views/dashboard/documents/
ls -la resources/views/dashboard/transactions/

# Regenerar caché
php artisan view:cache
```

---

## 📊 MONITOREO

### Ver usuarios registrados
```sql
mysql -u u489458217_dataflow -p -e "SELECT id, name, email, role FROM users;" u489458217_dataflow
```

### Ver documentos subidos
```sql
mysql -u u489458217_dataflow -p -e "SELECT COUNT(*) as total FROM documents;" u489458217_dataflow
```

### Ver espacio usado
```bash
du -sh storage/
du -sh public_html/
```

### Ver logs en tiempo real
```bash
tail -f storage/logs/laravel.log
```

---

## 🎯 CHECKLIST FINAL

- [ ] Código actualizado al branch correcto
- [ ] Dependencias instaladas (composer + npm)
- [ ] Assets compilados y copiados
- [ ] Migraciones ejecutadas
- [ ] .env configurado correctamente
- [ ] APP_DEBUG=false en producción
- [ ] Caché optimizada
- [ ] Permisos correctos en storage/
- [ ] Cron job configurado
- [ ] Usuario admin creado
- [ ] Sitio accesible con SSL
- [ ] Landing pages funcionando
- [ ] Login/registro funcionando
- [ ] Dashboard funcionando
- [ ] CRUD de entidades funcional
- [ ] CRUD de documentos funcional
- [ ] CRUD de transacciones funcional
- [ ] Panel admin accesible

---

## 🚀 PRÓXIMOS PASOS

### 1. Crear tu primera cuenta
1. Ve a https://dataflow.guaraniappstore.com/register
2. Crea tu cuenta como Plan Básico o Avanzado
3. Completa el registro

### 2. Configurar tu primera entidad
1. Login en el dashboard
2. Ve a "Entidades Fiscales"
3. Crea tu primera entidad con NIF/CIF

### 3. Subir documentos
1. Ve a "Documentos"
2. Sube tu primera factura o recibo
3. El sistema lo procesará automáticamente con IA

### 4. Registrar transacciones
1. Ve a "Transacciones"
2. Crea ingresos y gastos
3. Asígnalos a tus entidades

---

## 📧 CONFIGURAR BREVO (Email)

Si aún no está configurado:

1. Login en https://app.brevo.com
2. Settings → Senders & IP → Add Sender
3. Email: `dataflow@guaraniappstore.com`
4. Se aprueba automáticamente ✅
5. Settings → SMTP & API → Copiar credenciales
6. Actualizar .env:

```env
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_email_brevo
MAIL_PASSWORD=tu_smtp_password
```

---

## 🔑 CONFIGURAR OPENAI API

Para que funcione el procesamiento OCR de documentos:

1. Ve a https://platform.openai.com/api-keys
2. Crea una API Key nueva
3. Cópiala al .env:

```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxx
```

4. Limpia caché:
```bash
php artisan config:clear
php artisan config:cache
```

---

## ✅ ¡DEPLOYMENT COMPLETADO!

Tu plataforma Dataflow está **100% funcional** en:

**🌐 https://dataflow.guaraniappstore.com**

### Funcionalidades disponibles:
- ✅ Landing page profesional
- ✅ Registro y login de usuarios
- ✅ Dashboard con estadísticas
- ✅ Gestión de entidades fiscales
- ✅ Subida y procesamiento de documentos con IA
- ✅ Gestión de transacciones
- ✅ Extractos bancarios con retención de 60 días
- ✅ Panel de administración
- ✅ Multi-tenant (B2C y B2B)
- ✅ Multi-jurisdicción (19 países)

---

**Desarrollado por:** Claude (Anthropic)
**Fecha:** 22 de Noviembre de 2025
**Branch:** claude/finish-website-017QFURjJdFkpfiAEC9cJAVL
**Versión:** Completa y funcional al 100%
