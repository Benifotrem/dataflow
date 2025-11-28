# Configuración de Cron en Hostinger hPanel

## ¿Qué hace el cron?

El cron ejecutará el comando de informes mensuales **automáticamente el día 1 de cada mes a las 00:00 (medianoche)**.

## Pasos para configurar en hPanel:

### 1. Acceder a hPanel
- Inicia sesión en hPanel de Hostinger
- Busca la sección **"Avanzado"** o **"Advanced"**
- Encuentra **"Cron Jobs"** o **"Tareas Cron"**

### 2. Crear nueva tarea cron
Click en **"Crear nuevo Cron Job"** o **"Create new Cron Job"**

### 3. Configuración del Cron

#### Opción A: Si hay selector de frecuencia
- **Tipo:** Mensual (Monthly)
- **Día:** 1
- **Hora:** 00:00

#### Opción B: Si pide expresión cron manual
```
0 0 1 * *
```

**Explicación de la expresión:**
- `0` = Minuto 0
- `0` = Hora 0 (medianoche)
- `1` = Día 1 del mes
- `*` = Todos los meses
- `*` = Todos los días de la semana

### 4. Comando a ejecutar

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && /usr/bin/php artisan reports:send-monthly
```

**Notas importantes:**
- Usa la ruta completa al PHP de Hostinger: `/usr/bin/php`
- El `cd` cambia al directorio correcto antes de ejecutar
- Si Hostinger usa otra versión de PHP (ejemplo: `php82`), ajusta el comando

### 5. Configuración de notificaciones (opcional)
- **Email de notificación:** Deja tu email si quieres recibir confirmación cada vez que se ejecute
- O déjalo vacío para no recibir emails

### 6. Guardar
Click en **"Guardar"** o **"Create"**

---

## Verificación

### Probar manualmente antes de esperar al mes siguiente:

Conéctate por SSH y ejecuta:
```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
php artisan reports:send-monthly
```

Deberías ver:
```
Iniciando envío de informes mensuales...
Generando reportes para: [mes anterior]
Procesando X tenants...
✓ Informe enviado a: email@ejemplo.com
=== Resumen ===
Informes enviados: X
```

### Ver historial de ejecuciones:
En hPanel, la sección de Cron Jobs mostrará:
- Última ejecución
- Estado (exitoso/fallido)
- Próxima ejecución programada

---

## Alternativas de horario

Si prefieres otro horario, ajusta la expresión:

```bash
# Día 1 a las 8:00 AM
0 8 1 * *

# Día 1 a las 18:00 (6 PM)
0 18 1 * *

# Último día del mes a medianoche
0 0 L * *
```

---

## ¿Qué pasa con las notificaciones de email?

Con `QUEUE_CONNECTION=sync` en el `.env`:

✅ **Emails de verificación:** Se envían inmediatamente al registrarse
✅ **Emails de recuperación de contraseña:** Se envían inmediatamente
✅ **Notificaciones de documentos procesados:** Se envían inmediatamente después de procesar
✅ **Informes mensuales:** Se envían vía cron el día 1 de cada mes

**NO necesitas queue:work corriendo** porque `sync` procesa todo inmediatamente.

---

## Resumen de cambios realizados:

1. ✅ Cambiado `QUEUE_CONNECTION=database` a `QUEUE_CONNECTION=sync` en `.env`
2. ⏳ **PENDIENTE:** Configurar cron en hPanel (sigue los pasos arriba)
3. ⏳ **PENDIENTE:** Subir `.env` actualizado a producción

---

## Comandos para producción:

Cuando subas los cambios a producción, ejecuta:

```bash
php artisan config:cache
php artisan queue:clear
```

Esto limpiará cualquier trabajo pendiente en la cola antigua y cargará la nueva configuración.
