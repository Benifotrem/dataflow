# 📦 Sistema de Backup Mensual - Guía de Deployment

## 📋 Resumen

Este sistema envía automáticamente cada día **20 del mes a las 9:00 AM (hora Asunción/America)** un backup en Excel con todos los documentos del mes anterior a cada tenant por email.

---

## 🏗️ Arquitectura del Sistema

### Componentes Creados:

1. **MonthlyBackupExport** (`app/Exports/MonthlyBackupExport.php`)
   - Genera Excel con todos los documentos del mes anterior
   - Incluye 20 columnas con información completa
   - Añade resumen con totales al final

2. **MonthlyBackupMail** (`app/Mail/MonthlyBackupMail.php`)
   - Mailable que envía el email transaccional vía Brevo
   - Adjunta el archivo Excel generado
   - Usa template HTML personalizado

3. **Email Template** (`resources/views/emails/monthly-backup.blade.php`)
   - Diseño profesional con instrucciones paso a paso
   - Placeholders para 5 capturas de pantalla (opcionales)
   - Instrucciones para guardar en Gmail/Google Drive

4. **MonthlyBackupCommand** (`app/Console/Commands/MonthlyBackupCommand.php`)
   - Comando artisan: `php artisan backup:monthly`
   - Procesa todos los tenants con email configurado
   - Genera y envía backups automáticamente
   - Logging completo de éxito/errores

---

## 🚀 Deployment en Producción

### Paso 1: Desplegar el Fix del Error 500

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU
php artisan view:clear
php artisan cache:clear
```

### Paso 2: Verificar el Sistema de Backup

Una vez desplegado, los siguientes archivos estarán disponibles:

```
app/
├── Console/Commands/MonthlyBackupCommand.php
├── Exports/MonthlyBackupExport.php
└── Mail/MonthlyBackupMail.php

resources/views/emails/
└── monthly-backup.blade.php

public/images/email/
├── .gitkeep
└── README.md (instrucciones para capturas)
```

### Paso 3: Probar el Comando Manualmente

Antes de configurar el cron, prueba que funcione:

```bash
# Probar con un tenant específico
php artisan backup:monthly --tenant-id=1 --force

# Probar con todos los tenants (pedirá confirmación)
php artisan backup:monthly

# Probar con todos los tenants (sin confirmación)
php artisan backup:monthly --force
```

**Verifica:**
- ✅ Se genera el Excel correctamente
- ✅ Se envía el email al tenant
- ✅ El email tiene el Excel adjunto
- ✅ El diseño del email se ve correctamente
- ✅ Las instrucciones son claras

---

## ⏰ Configuración del Cron Job

### Opción A: Crontab Manual (Recomendada)

```bash
# Editar crontab
crontab -e

# Agregar esta línea (ejecuta el día 20 a las 9:00 AM hora Asunción)
0 9 20 * * cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && php artisan backup:monthly --force >> /home/u489458217/backup_cron.log 2>&1
```

**Explicación del cron:**
- `0 9 20 * *` = Minuto 0, hora 9, día 20, todos los meses, todos los días de semana
- `cd /path/to/project` = Cambiar al directorio del proyecto
- `php artisan backup:monthly --force` = Ejecutar el comando sin confirmación
- `>> /home/u489458217/backup_cron.log 2>&1` = Guardar logs en archivo

### Opción B: Panel de Control cPanel/Hostinger

1. Accede al panel de control de Hostinger
2. Ve a "Cron Jobs" o "Tareas Programadas"
3. Crea una nueva tarea con:
   - **Minuto:** 0
   - **Hora:** 9
   - **Día del mes:** 20
   - **Mes:** * (todos)
   - **Día de la semana:** * (todos)
   - **Comando:**
     ```bash
     cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && /usr/bin/php artisan backup:monthly --force
     ```

### Verificar Zona Horaria del Servidor

```bash
# Ver zona horaria actual
date
timedatectl  # Si está disponible

# El cron se ejecuta en la zona horaria del servidor
# Asegúrate de que el servidor esté en America/Asuncion o ajusta la hora del cron
```

**Si el servidor NO está en America/Asuncion:**
- Calcula la diferencia horaria
- Ajusta la hora del cron en consecuencia
- Ejemplo: Si el servidor está en UTC-0 y Asunción es UTC-3, el cron debería ser a las 12:00 (no 9:00)

---

## 🧪 Testing y Debugging

### Probar el Comando

```bash
# Test con un tenant específico
php artisan backup:monthly --tenant-id=1 --force

# Ver el output completo
php artisan backup:monthly --force -vvv
```

### Ver Logs

```bash
# Laravel logs
tail -50 storage/logs/laravel.log

# Cron logs
tail -50 /home/u489458217/backup_cron.log
```

### Debugging Común

**❌ Error: "Class 'MonthlyBackupExport' not found"**
```bash
composer dump-autoload
php artisan clear-compiled
```

**❌ Error: "Unable to write file"**
```bash
chmod -R 775 storage/app/temp
chown -R www-data:www-data storage/app/temp  # O el usuario del servidor
```

**❌ Email no se envía**
- Verifica que Brevo API key esté configurada: `SystemSetting::get('brevo_api_key')`
- Verifica que el tenant tenga email: `Tenant::find(1)->email`
- Revisa los logs de Laravel para errores de SMTP/Brevo

---

## 📊 Monitoreo

### Ver Última Ejecución del Cron

```bash
# Ver logs del cron
tail -100 /home/u489458217/backup_cron.log

# Filtrar solo éxitos
grep "✅" /home/u489458217/backup_cron.log

# Filtrar solo errores
grep "❌" /home/u489458217/backup_cron.log
```

### Ver Emails Enviados en Laravel Logs

```bash
grep "Monthly backup sent" storage/logs/laravel.log
```

---

## 🖼️ Capturas de Pantalla (Opcional)

El email incluye placeholders para 5 capturas de pantalla que ilustran los pasos para guardar el backup.

**Para agregar las capturas:**
1. Lee las instrucciones en `public/images/email/README.md`
2. Captura las pantallas según las especificaciones
3. Nómbralas exactamente como se indica:
   - `backup-step1.png`
   - `backup-step2.png`
   - `backup-step3.png`
   - `backup-step4.png`
   - `backup-step5.png`
4. Súbelas a `public/images/email/`

**Si no quieres usar capturas:**
El email funciona perfectamente sin ellas. Las instrucciones textuales son suficientemente claras.

---

## 🔧 Personalización

### Cambiar la Hora de Envío

Edita el cron job para cambiar la hora:
```bash
# Cambiar a las 8:00 AM
0 8 20 * * cd /path && php artisan backup:monthly --force

# Cambiar a las 10:30 AM
30 10 20 * * cd /path && php artisan backup:monthly --force
```

### Cambiar el Día del Mes

```bash
# Enviar el día 1 de cada mes
0 9 1 * * cd /path && php artisan backup:monthly --force

# Enviar el último día del mes (requiere script más complejo)
```

### Personalizar el Email

Edita `resources/views/emails/monthly-backup.blade.php`:
- Cambiar colores
- Modificar textos
- Agregar/quitar secciones
- Cambiar el logo

---

## 📝 Checklist de Deployment

- [ ] Git pull del código en producción
- [ ] Limpiar caches de Laravel
- [ ] Probar comando manualmente con un tenant
- [ ] Verificar que se genera el Excel
- [ ] Verificar que se envía el email
- [ ] Verificar que el email tiene el adjunto
- [ ] Configurar cron job
- [ ] Verificar zona horaria del servidor
- [ ] Configurar logging del cron
- [ ] (Opcional) Agregar capturas de pantalla
- [ ] Documentar para el equipo

---

## 🆘 Soporte

**Si algo no funciona:**
1. Revisa los logs: `storage/logs/laravel.log`
2. Revisa los logs del cron: `/home/u489458217/backup_cron.log`
3. Ejecuta el comando manualmente con `-vvv` para debugging
4. Verifica que Brevo API key esté configurada
5. Verifica que los tenants tengan email

**Para reportar problemas:**
Incluye en tu reporte:
- Logs completos de error
- Comando ejecutado
- Output completo del comando
- Versión de PHP y Laravel

---

## 📚 Comandos Útiles de Referencia

```bash
# Probar backup para tenant específico
php artisan backup:monthly --tenant-id=1 --force

# Probar backup para todos
php artisan backup:monthly --force

# Ver ayuda del comando
php artisan backup:monthly --help

# Limpiar cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Ver cron logs
tail -f /home/u489458217/backup_cron.log

# Verificar Brevo API key
php artisan tinker
>>> App\Models\SystemSetting::get('brevo_api_key')

# Listar tenants con email
php artisan tinker
>>> App\Models\Tenant::whereNotNull('email')->get(['id', 'name', 'email'])
```

---

**¡Sistema listo para deployment!** 🚀
