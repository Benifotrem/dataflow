# PROTOCOLO DE DEPLOYMENT - HOSTINGER (Subdominios de guaraniappstore.com)

## INFORMACIÓN CRÍTICA DE LA ESTRUCTURA

### Arquitectura Hostinger
- **Proveedor:** Hostinger (hPanel, NO cPanel)
- **Dominio principal:** guaraniappstore.com
- **Estructura:** Cada subdominio tiene su PROPIO hosting dedicado
- **Panel:** hPanel (limitaciones: no permite cambiar Document Root)

### Ruta REAL de archivos
```
/home/u489458217/domains/[SUBDOMINIO]/
├── DO_NOT_UPLOAD_HERE (archivo de aviso)
├── public_html/        ← AQUÍ es donde sirve el servidor web
└── (archivos de Laravel aquí en la raíz del subdominio)
```

**⚠️ IMPORTANTE:**
- `public_html` NO es la raíz del hosting
- Los archivos se sirven desde `/home/u489458217/domains/[SUBDOMINIO]/public_html/`
- NO usar `/home/u489458217/public_html/` (esa carpeta es para otros propósitos)

---

## PROMPT PARA CLAUDE

```
# DEPLOYMENT EN HOSTINGER - SUBDOMINIO DE GUARANIAPPSTORE.COM

## Contexto
Voy a desplegar un proyecto Laravel en Hostinger bajo un subdominio de guaraniappstore.com.

## Información del Servidor
- **Usuario SSH:** u489458217
- **Dominio principal:** guaraniappstore.com
- **Subdominio:** [NOMBRE].guaraniappstore.com
- **Panel:** hPanel (Hostinger)
- **PHP:** 8.3 (configurado en hPanel)
- **Estructura:** Hosting dedicado para este subdominio

## Ruta de Deployment
El proyecto debe desplegarse en:
```
/home/u489458217/domains/[SUBDOMINIO].guaraniappstore.com/
```

## Estructura Obligatoria
- Archivos de Laravel (vendor, app, config, etc.) → Raíz del subdominio
- Archivos públicos (index.php, assets, etc.) → `public_html/`
- El servidor web sirve desde `public_html/`

## Requisitos Previos Completados
- [x] Subdominio creado en hPanel
- [x] Base de datos MySQL creada (anota credenciales)
- [x] PHP 8.3 activado en hPanel
- [x] Repositorio Git con el proyecto Laravel

## Protocolo de Deployment
Sigue EXACTAMENTE estos pasos en este orden:

### PASO 1: Clonar repositorio
```bash
cd /home/u489458217/domains/[SUBDOMINIO].guaraniappstore.com/
git clone [REPO_URL] temp
cd temp
git checkout [BRANCH]
mv * ..
mv .* .. 2>/dev/null || true
cd ..
rm -rf temp
```

### PASO 2: Configurar dependencias
```bash
# IMPORTANTE: Si hay error de versión PHP en composer.lock
rm composer.lock
composer install --no-dev --optimize-autoloader
```

### PASO 3: Crear carpeta public_html
```bash
mkdir -p public_html
cp -r public/* public_html/
cp public/.htaccess public_html/ 2>/dev/null || true
```

### PASO 4: Configurar .env
```bash
cp .env.example .env
nano .env

# Configurar:
# - APP_URL=https://[SUBDOMINIO].guaraniappstore.com
# - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# - MAIL_* (Brevo si es necesario)
# - Otras API keys
```

### PASO 5: Generar clave y optimizar
```bash
php artisan key:generate --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### PASO 6: Ejecutar migraciones
```bash
php artisan migrate --force
```

### PASO 7: Configurar permisos
```bash
chmod -R 775 storage bootstrap/cache
chown -R u489458217:o1007755236 storage bootstrap/cache
```

### PASO 8: Verificar
- Acceder a: https://[SUBDOMINIO].guaraniappstore.com
- Debe mostrar la aplicación

## PROBLEMAS COMUNES Y SOLUCIONES

### Error: composer.lock incompatible con PHP 8.2/8.3
```bash
rm composer.lock
composer install --no-dev --optimize-autoloader
```

### Error: 404 Not Found
- Verificar que `public_html/` existe y tiene archivos
- Verificar que `.htaccess` está en `public_html/`

### Error: 500 Internal Server Error
```bash
# Ver logs
tail -50 storage/logs/laravel.log

# Limpiar cache
php artisan optimize:clear

# Verificar .env
cat .env | grep APP_KEY
```

### Error: Vistas Blade corruptas
**NO copiar archivos .blade.php con `cp`**. Usar `cat` o `rsync`:
```bash
# Método correcto para copiar vistas:
rsync -av resources/ /destino/resources/
```

### Error: "No application encryption key"
```bash
php artisan key:generate --force
php artisan config:clear
```

## CONFIGURACIONES POST-DEPLOYMENT

### Cron Job (Scheduler de Laravel)
En hPanel → Cron Jobs:
```
* * * * * cd /home/u489458217/domains/[SUBDOMINIO].guaraniappstore.com && php artisan schedule:run >> /dev/null 2>&1
```

### Configurar Brevo (Email)
1. Login: https://app.brevo.com
2. Settings → Senders & IP → Add Sender
3. Email: `[nombre]@guaraniappstore.com` (se aprueba automáticamente)
4. Copiar SMTP credentials al `.env`

### Desactivar Debug en Producción
```bash
nano .env
# Cambiar: APP_DEBUG=false
php artisan config:clear
```

## CHECKLIST FINAL

- [ ] Sitio accesible con SSL
- [ ] Base de datos conectada (sin errores)
- [ ] Vistas renderizando correctamente
- [ ] APP_DEBUG=false en producción
- [ ] Cron job configurado
- [ ] Email configurado (si aplica)
- [ ] Permisos correctos en storage/
- [ ] .env con credenciales correctas

## NOTAS IMPORTANTES

1. **NUNCA** usar `/home/u489458217/public_html/` para proyectos de subdominios
2. **SIEMPRE** clonar en `/home/u489458217/domains/[SUBDOMINIO].guaraniappstore.com/`
3. **SIEMPRE** copiar archivos de `public/` a `public_html/`
4. **NO** intentar cambiar Document Root en hPanel (no es posible)
5. **Verificar** que PHP 8.3 esté activo ANTES de instalar composer
6. **Si hay archivos Blade**, usar `rsync` en lugar de `cp` para evitar corrupción

## CONTACTOS DE SOPORTE

- **Hosting:** Hostinger hPanel
- **Email verificado:** guaraniappstore.com (usar para todos los subdominios)
- **SMTP:** smtp-relay.brevo.com (puerto 587)

---

**Última actualización:** 2025-11-20
**Proyecto de referencia:** dataflow.guaraniappstore.com
**Estado:** ✅ Deployments exitosos con este protocolo
```

---

## COMANDO RÁPIDO PARA NUEVO DEPLOYMENT

Para copiar este protocolo a un nuevo proyecto:

```bash
# En tu máquina local, dentro del nuevo proyecto Laravel:
curl -o HOSTINGER_DEPLOYMENT_PROTOCOL.md https://raw.githubusercontent.com/Benifotrem/contaplus/main/HOSTINGER_DEPLOYMENT_PROTOCOL.md

# O simplemente copia este archivo al nuevo repositorio
```

---

## EJEMPLO DE USO

Cuando vayas a desplegar un nuevo proyecto, simplemente:

1. Crea el subdominio en hPanel
2. Crea la base de datos MySQL
3. Dale este archivo a Claude junto con:
   - Nombre del subdominio
   - Credenciales de la base de datos
   - URL del repositorio Git

Claude seguirá el protocolo exacto sin improvisaciones.
