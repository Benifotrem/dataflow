# 🗓️ Sistema de Calendario Fiscal - Documentación Completa

## 📋 Resumen

Sistema completo de gestión de eventos fiscales con:
- **Eventos pre-cargados por país** (Paraguay, España, Argentina)
- **Notificaciones automáticas por email** vía Brevo
- **Dashboard completo** para gestionar eventos
- **Personalización total** por parte del cliente
- **Notificaciones inteligentes** con días de anticipación configurables

---

## 🏗️ Arquitectura del Sistema

### Componentes Creados:

#### **1. Base de Datos**

**Migration:** `database/migrations/2025_11_26_210107_create_fiscal_events_table.php`

**Tabla: `fiscal_events`**
- `id`: ID del evento
- `tenant_id`: Tenant propietario
- `country_code`: Código ISO país (PY, ES, AR, etc.)
- `title`: Título del evento
- `description`: Descripción detallada
- `event_type`: Tipo de evento (enum)
- `event_date`: Fecha del vencimiento
- `notification_days_before`: Días de anticipación para notificar
- `is_recurring`: Si se repite cada año
- `is_active`: Si está activo (envía notificaciones)
- `is_default`: Si es evento por defecto del país
- `last_notified_at`: Última vez que se notificó

**Tipos de Eventos:**
- `vat_liquidation` - Liquidación de IVA
- `income_tax` - Impuesto a la Renta
- `tax_declaration` - Declaración de Impuestos
- `social_security` - Seguridad Social
- `annual_accounts` - Cuentas Anuales
- `quarterly_declaration` - Declaración Trimestral
- `monthly_declaration` - Declaración Mensual
- `custom` - Evento Personalizado

#### **2. Modelo**

**Archivo:** `app/Models/FiscalEvent.php`

**Características:**
- Relación con Tenant
- Scopes útiles: `active()`, `upcoming()`, `needsNotification()`, `country()`, `type()`
- Atributos calculados: `days_until`, `is_today`, `is_past`, `event_type_name`, `event_color`
- Métodos: `markAsNotified()`, `duplicateForNextYear()`

#### **3. Seeder**

**Archivo:** `database/seeders/FiscalEventSeeder.php`

**Eventos pre-cargados por país:**

**Paraguay (PY):**
- IVA Mensual: 12 eventos (día 25 de cada mes)
- IPS (Seguridad Social): Día 10 mensual
- IRE (Impuesto Renta Empresarial): 3 cuotas (Abril, Julio, Octubre)

**España (ES):**
- Modelo 303 (IVA Trimestral): 4 eventos
- Modelo 390 (Resumen anual IVA): 30 enero
- Modelo 130 (IRPF Trimestral): 4 eventos
- Declaración de la Renta: 30 junio

**Argentina (AR):**
- IVA Mensual: Día 20
- Impuesto a las Ganancias: 5 anticipos + DJ anual
- Seguridad Social: Día 10 mensual

#### **4. Notificaciones**

**Mailable:** `app/Mail/FiscalEventNotificationMail.php`
**Vista:** `resources/views/emails/fiscal-event-notification.blade.php`

**Características del Email:**
- Diseño responsivo y profesional
- Color dinámico según urgencia (rojo si <= 3 días)
- Contador de días restantes destacado
- Checklist de preparación
- Link directo a Dataflow
- Información del evento completa

#### **5. Comando Automático**

**Archivo:** `app/Console/Commands/SendFiscalEventNotifications.php`

**Comando:** `php artisan fiscal:notify`

**Opciones:**
- `--tenant-id=X`: Procesar solo un tenant específico
- `--force`: Ejecutar sin confirmación (para cron)
- `--dry-run`: Mostrar qué se enviaría sin enviar

**Funcionalidad:**
- Busca eventos que necesiten notificación hoy
- Verifica días de anticipación configurados
- Envía emails transaccionales vía Brevo
- Marca eventos como notificados
- Logging completo de éxito/errores
- Estadísticas al finalizar

#### **6. Dashboard (CRUD Completo)**

**Controller:** `app/Http/Controllers/Dashboard/FiscalEventController.php`

**Rutas:**
- `GET /fiscal-events` - Lista de eventos
- `GET /fiscal-events/create` - Crear evento
- `POST /fiscal-events` - Guardar evento
- `GET /fiscal-events/{id}/edit` - Editar evento
- `PUT /fiscal-events/{id}` - Actualizar evento
- `DELETE /fiscal-events/{id}` - Eliminar evento
- `PATCH /fiscal-events/{id}/toggle-active` - Activar/Desactivar

**Vistas:**
- `resources/views/dashboard/fiscal-events/index.blade.php` - Lista y filtros
- `resources/views/dashboard/fiscal-events/create.blade.php` - Formulario crear
- `resources/views/dashboard/fiscal-events/edit.blade.php` - Formulario editar

**Funcionalidades del Dashboard:**
- Vista de eventos próximos (30 días)
- Filtros por tipo, estado, activo/inactivo
- Tabla completa con información detallada
- Indicadores visuales de urgencia
- Protección de eventos por defecto (no se pueden eliminar, solo desactivar)
- Badges de color según tipo de evento

---

## 🚀 Deployment en Producción

### **Paso 1: Ejecutar Migration**

```bash
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

# Pull los cambios
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

# Ejecutar migration
php artisan migrate --force

# Limpiar caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### **Paso 2: Cargar Eventos por Defecto (OPCIONAL)**

Si quieres cargar eventos fiscales por defecto para todos los tenants:

```bash
php artisan db:seed --class=FiscalEventSeeder --force
```

**Nota:** Esto creará eventos para TODOS los tenants basados en su `country_code`. Asegúrate de que los tenants tengan el campo `country_code` configurado.

### **Paso 3: Verificar que el Sistema Funciona**

```bash
# Verificar que no haya errores
php artisan about

# Listar comandos disponibles
php artisan list | grep fiscal

# Probar el comando de notificaciones en modo dry-run
php artisan fiscal:notify --dry-run
```

### **Paso 4: Configurar Cron Job**

El sistema necesita un cron job que verifique DIARIAMENTE si hay eventos que requieren notificación.

**Agregar al crontab:**

```bash
crontab -e

# Agregar esta línea (ejecuta diariamente a las 8:00 AM):
0 8 * * * cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && php artisan fiscal:notify --force >> /home/u489458217/fiscal_cron.log 2>&1
```

**O en hPanel:**
- **Frecuencia:** Diaria a las 8:00 AM
- **Comando:**
  ```bash
  cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html && php artisan fiscal:notify --force
  ```

---

## 🧪 Testing

### **1. Probar el Dashboard**

```
https://dataflow.guaraniappstore.com/fiscal-events
```

- ✅ Ver lista de eventos
- ✅ Crear nuevo evento
- ✅ Editar evento existente
- ✅ Activar/Desactivar eventos
- ✅ Filtrar eventos
- ✅ Ver eventos próximos

### **2. Probar Comando de Notificaciones**

```bash
# Modo dry-run (no envía emails realmente)
php artisan fiscal:notify --dry-run

# Probar con un tenant específico
php artisan fiscal:notify --tenant-id=1 --force

# Probar con todos los tenants
php artisan fiscal:notify --force
```

### **3. Crear Evento de Prueba**

1. Ir a: https://dataflow.guaraniappstore.com/fiscal-events
2. Clic en "Nuevo Evento"
3. Llenar formulario:
   - **Título:** Prueba de Notificación
   - **Tipo:** Evento Personalizado
   - **Fecha:** Mañana o pasado mañana
   - **Días de aviso:** 1 o 2
   - **Activo:** Sí
4. Guardar
5. Ejecutar comando manualmente: `php artisan fiscal:notify --force`
6. Verificar que llegue el email

---

## 📊 Flujo de Trabajo

### **Configuración Inicial (Una vez)**

1. ✅ Deployment completo ejecutado
2. ✅ Migration ejecutada
3. ✅ (Opcional) Seeder ejecutado para cargar eventos por defecto
4. ✅ Cron job configurado

### **Uso Diario (Automático)**

1. 🤖 **8:00 AM diario:** Cron ejecuta `php artisan fiscal:notify`
2. 🔍 Sistema busca eventos próximos que necesiten notificación
3. 📧 Envía emails a tenants con eventos próximos
4. ✅ Marca eventos como notificados
5. 📝 Registra logs de éxito/errores

### **Gestión por el Cliente (Manual)**

1. 👤 Cliente accede a `/fiscal-events`
2. 📅 Ve su calendario fiscal completo
3. ➕ Puede crear eventos personalizados
4. ✏️ Puede editar eventos existentes (incluso los por defecto)
5. 🔄 Puede activar/desactivar eventos
6. 🗑️ Puede eliminar eventos personalizados

---

## 🎨 Personalización

### **Modificar Eventos por Defecto de un País**

Editar `database/seeders/FiscalEventSeeder.php` y modificar el método correspondiente:
- `getParaguayEvents()`
- `getSpainEvents()`
- `getArgentinaEvents()`

### **Agregar Nuevo País**

1. Agregar método en el Seeder: `getNewCountryEvents()`
2. Agregar case en `getEventsByCountry()`:
   ```php
   return match($countryCode) {
       'PY' => $this->getParaguayEvents(),
       'ES' => $this->getSpainEvents(),
       'AR' => $this->getArgentinaEvents(),
       'MX' => $this->getNewCountryEvents(), // Nuevo país
       default => $this->getParaguayEvents(),
   };
   ```

### **Cambiar Hora de Ejecución del Cron**

Modificar la línea del crontab:
```bash
# Cambiar a las 6:00 AM
0 6 * * * cd /path && php artisan fiscal:notify --force

# Cambiar a las 9:30 PM
30 21 * * * cd /path && php artisan fiscal:notify --force
```

### **Personalizar Email de Notificación**

Editar `resources/views/emails/fiscal-event-notification.blade.php`:
- Cambiar colores
- Modificar textos
- Agregar/quitar secciones
- Cambiar el diseño

---

## 📝 Casos de Uso

### **Caso 1: Cliente con IVA Mensual**

**Configuración:**
- País: Paraguay
- Eventos: 12 eventos de IVA mensual (automáticos)
- Notificación: 7 días antes

**Flujo:**
1. Día 18 de cada mes: Cliente recibe email recordando vencimiento IVA día 25
2. Email incluye checklist de preparación
3. Cliente puede hacer clic y ver todos sus documentos del mes
4. Cliente prepara la declaración con tiempo

### **Caso 2: Cliente con Evento Personalizado**

**Configuración:**
- Cliente tiene reunión anual con contador cada 15 de marzo
- Crea evento personalizado: "Reunión Anual Contador"
- Notificación: 15 días antes

**Flujo:**
1. Día 1 de marzo: Cliente recibe email recordatorio
2. Cliente revisa sus documentos
3. Cliente prepara reportes para la reunión

### **Caso 3: Cliente Cambia Fecha de Vencimiento**

**Escenario:** Gobierno cambia vencimiento IVA de día 25 a día 28

**Solución:**
1. Cliente entra a `/fiscal-events`
2. Busca evento "Vencimiento IVA"
3. Edita fecha de 25 a 28
4. Guarda cambios
5. Sistema notificará con la nueva fecha

### **Caso 4: Cliente Desactiva Evento**

**Escenario:** Cliente cambió de régimen fiscal y ya no paga IVA trimestral

**Solución:**
1. Cliente entra a `/fiscal-events`
2. Busca eventos trimestrales de IVA
3. Clic en "Desactivar"
4. Ya no recibirá notificaciones de esos eventos

---

## 🔧 Troubleshooting

### **Problema: No llegan las notificaciones**

**Diagnóstico:**
```bash
# 1. Verificar que el cron se ejecuta
tail -50 /home/u489458217/fiscal_cron.log

# 2. Verificar logs de Laravel
tail -50 storage/logs/laravel.log | grep "Fiscal"

# 3. Verificar Brevo API key
php artisan tinker
>>> App\Models\SystemSetting::get('brevo_api_key')

# 4. Probar comando manualmente
php artisan fiscal:notify --dry-run
```

**Soluciones:**
- Verificar que Brevo API key esté configurada
- Verificar que el tenant tenga email
- Verificar que el evento esté activo
- Verificar que falten los días configurados para el vencimiento

### **Problema: Evento no aparece en la lista**

**Diagnóstico:**
```bash
# Verificar en base de datos
php artisan tinker
>>> App\Models\FiscalEvent::where('tenant_id', 1)->count()
>>> App\Models\FiscalEvent::where('tenant_id', 1)->get()
```

**Soluciones:**
- Verificar que el evento pertenezca al tenant correcto
- Limpiar filtros en el dashboard
- Verificar que no esté en página 2 de la paginación

### **Problema: Error 500 al acceder a /fiscal-events**

**Diagnóstico:**
```bash
# Ver logs
tail -50 storage/logs/laravel.log

# Limpiar caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear

# Verificar que la migration se ejecutó
php artisan migrate:status | grep fiscal
```

---

## 📋 Checklist de Deployment

- [ ] Git pull ejecutado
- [ ] Migration ejecutada (`php artisan migrate --force`)
- [ ] (Opcional) Seeder ejecutado
- [ ] Caches limpiados
- [ ] Dashboard accesible (`/fiscal-events`)
- [ ] Crear evento de prueba
- [ ] Probar comando: `php artisan fiscal:notify --dry-run`
- [ ] Verificar Brevo API key configurada
- [ ] Configurar cron job (diario 8:00 AM)
- [ ] Probar envío real de notificación
- [ ] Verificar email recibido
- [ ] Documentar para el equipo

---

## 🆘 Comandos Útiles

```bash
# Ver todos los eventos de un tenant
php artisan tinker
>>> App\Models\FiscalEvent::where('tenant_id', 1)->get()

# Ver eventos próximos
>>> App\Models\FiscalEvent::where('tenant_id', 1)->upcoming(30)->get()

# Ver eventos que necesitan notificación
>>> App\Models\FiscalEvent::needsNotification()->get()

# Probar notificaciones sin enviar
php artisan fiscal:notify --dry-run

# Probar con un tenant específico
php artisan fiscal:notify --tenant-id=1 --force

# Ver logs en tiempo real
tail -f storage/logs/laravel.log | grep "Fiscal"

# Ver cron logs
tail -f /home/u489458217/fiscal_cron.log

# Limpiar caches
php artisan cache:clear && php artisan view:clear && php artisan config:clear

# Listar rutas del calendario
php artisan route:list | grep fiscal
```

---

## 🌟 Características Destacadas

✅ **Eventos pre-cargados** por país (Paraguay, España, Argentina)
✅ **100% personalizable** desde el dashboard
✅ **Notificaciones automáticas** con días configurables
✅ **Emails profesionales** con diseño responsivo
✅ **Eventos recurrentes** que se repiten cada año
✅ **Protección de eventos por defecto** (no se pueden eliminar)
✅ **Dashboard intuitivo** con filtros y búsqueda
✅ **Alertas urgentes** cuando quedan <= 3 días
✅ **Logging completo** de todas las operaciones
✅ **Multi-tenant** con aislamiento total

---

**¡Sistema listo para producción!** 🚀

Para cualquier duda o personalización adicional, consultar este documento o los comentarios en el código fuente.
