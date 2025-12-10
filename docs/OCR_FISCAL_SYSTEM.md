# Sistema de OCR y Validación Fiscal - Aranduka Core

## 📋 Descripción General

Sistema completo de ingesta automatizada de facturas paraguayas con OCR (OpenAI Vision) y validación fiscal mediante integración con la DNIT/SET de Paraguay.

### 🎯 Características Principales

1. **OCR Avanzado**: Extracción automática de datos fiscales usando OpenAI Vision API
2. **Validación Fiscal**: Verificación de RUC y Timbrado con la SET (DNIT)
3. **Procesamiento Asíncrono**: Cola de trabajos optimizada para Shared Hosting
4. **Caché Inteligente**: Sistema de caché de 30 días para reducir llamadas a la API
5. **Notificaciones Telegram**: Feedback en tiempo real al usuario
6. **Reintentos Exponenciales**: Manejo robusto de errores temporales

---

## 🏗️ Arquitectura

```
┌─────────────────┐
│  Usuario        │
│  (Telegram)     │
└────────┬────────┘
         │ 1. Envía foto de factura
         ▼
┌─────────────────────────────┐
│ TelegramWebhookController   │
│ • Recibe archivo            │
│ • Despacha Job              │
└────────┬────────────────────┘
         │ 2. Encola
         ▼
┌──────────────────────────────────┐
│ OcrInvoiceProcessingJob          │
│ ┌─────────────────────────────┐  │
│ │ PASO 1: Descargar archivo   │  │
│ │ PASO 2: Crear documento     │  │
│ │ PASO 3: Guardar temp        │  │
│ └─────────────────────────────┘  │
│                                   │
│ ┌─────────────────────────────┐  │
│ │ PASO 4: OcrVisionService    │  │
│ │ • Extracción con OpenAI     │  │
│ │ • Prompt RG-90 específico   │  │
│ │ • Validación de datos       │  │
│ └────────┬────────────────────┘  │
│          │                        │
│ ┌────────▼────────────────────┐  │
│ │ PASO 5: DnitConnector       │  │
│ │ • Validar RUC (con caché)   │  │
│ │ • Validar Timbrado          │  │
│ │ • Reintentos exponenciales  │  │
│ └────────┬────────────────────┘  │
│          │                        │
│ ┌────────▼────────────────────┐  │
│ │ PASO 6: Reorganizar archivo │  │
│ │ PASO 7: Marcar estado       │  │
│ │ PASO 8: Notificar usuario   │  │
│ └─────────────────────────────┘  │
└───────────────────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Estados del Documento       │
│ • VALIDATED    ✅           │
│ • MANUAL_CHECK ⚠️           │
│ • FAILED       ❌           │
└─────────────────────────────┘
```

---

## 📦 Componentes del Sistema

### 1. **OcrVisionService** (`app/Services/OcrVisionService.php`)

Servicio especializado de OCR con OpenAI Vision API.

**Métodos Principales:**

```php
// Extracción completa de factura paraguaya
extractInvoiceData(string $base64Image, string $mimeType, string $promptContext): array

// Extracción rápida de campos básicos
extractBasicData(string $base64Image, string $mimeType): array
```

**Campos Extraídos (según RG-90):**
- `ruc_emisor`: RUC del emisor
- `razon_social_emisor`: Razón social
- `timbrado`: Número de timbrado (8 dígitos)
- `fecha_emision`: Fecha (YYYY-MM-DD)
- `numero_factura`: Número de factura
- `serie`: Serie de factura
- `condicion_venta`: CONTADO o CREDITO
- `tipo_factura`: FACTURA, BOLETA, etc.
- `subtotal`: Base imponible
- `iva_5`: IVA 5%
- `iva_10`: IVA 10%
- `total_iva`: Total IVA
- `monto_total`: Monto total
- `moneda`: Código de moneda (PYG, USD, etc.)
- `items`: Array de productos/servicios
- `observaciones`: Notas adicionales
- `calidad_imagen`: ALTA, MEDIA, BAJA

**Validaciones:**
- ✅ Formato de RUC (6-10 dígitos)
- ✅ Formato de Timbrado (8 dígitos)
- ✅ Formato de fecha (YYYY-MM-DD)
- ✅ Monto válido (número positivo)
- ✅ Cálculo de completitud (%)

---

### 2. **DnitConnector** (`app/Services/DnitConnector.php`)

Conector para validación fiscal con la SET de Paraguay.

**Métodos Principales:**

```php
// Validar RUC
validateRuc(string $ruc): array

// Validar Timbrado
validateTimbrado(string $timbrado, string $ruc): array

// Validar factura completa
validateInvoice(array $invoiceData): array
```

**Características:**

1. **Caché de Base de Datos**:
   - Cache key: `dnit:ruc:{ruc}` y `dnit:timbrado:{ruc}:{timbrado}`
   - TTL: 30 días (2,592,000 segundos)
   - Driver: `database` (compatible con Shared Hosting)

2. **Reintentos Exponenciales**:
   - Máximo 3 intentos
   - Backoff: 1s → 2s → 4s
   - Solo reintenta errores de red/servicio
   - No reintenta errores de validación

3. **Modo Desarrollo**:
   - Si `APP_ENV=local` o sin credenciales DNIT
   - Retorna validaciones simuladas
   - Útil para testing sin acceso a la API real

**Respuestas:**

```php
// Validación exitosa
[
    'valid' => true,
    'data' => [
        'ruc' => '80000001-7',
        'razon_social' => 'CONTRIBUYENTE EJEMPLO SA',
        'estado' => 'ACTIVO'
    ],
    'error' => null
]

// Validación fallida
[
    'valid' => false,
    'data' => null,
    'error' => 'RUC no encontrado en la base de datos de la SET'
]
```

---

### 3. **OcrInvoiceProcessingJob** (`app/Jobs/OcrInvoiceProcessingJob.php`)

Job de orquestación asíncrona para el procesamiento completo.

**Configuración:**

```php
public $tries = 3;           // 3 intentos
public $timeout = 600;       // 10 minutos
public $backoff = [30, 60, 120]; // Delay entre reintentos
```

**Flujo de Ejecución:**

1. **Descarga**: Obtener archivo de Telegram
2. **Creación**: Crear registro Document
3. **Almacenamiento**: Guardar temporalmente
4. **OCR**: Extraer datos con OcrVisionService
5. **Validación**: Validar con DnitConnector
6. **Organización**: Reorganizar en carpetas
7. **Estado**: Marcar como VALIDATED o MANUAL_CHECK
8. **Notificación**: Enviar mensaje a Telegram

**Estados del Documento:**

- ✅ **VALIDATED**: Datos completos y validados con DNIT
- ⚠️ **MANUAL_CHECK**: Requiere revisión manual (datos incompletos o validación fallida)
- ❌ **FAILED**: Error crítico en el procesamiento

---

## ⚙️ Configuración

### 1. Variables de Entorno (`.env`)

```bash
# OpenAI API (OCR)
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxx
AI_PROVIDER=openai
AI_MODEL=gpt-4o-mini

# DNIT/SET Paraguay (Validación Fiscal)
DNIT_WSDL_URL=https://ekuatia.set.gov.py/consultas/qr
DNIT_USERNAME=tu_usuario_set
DNIT_PASSWORD=tu_password_set
DNIT_TIMEOUT=30
DNIT_CACHE_TTL=2592000

# Cola de Trabajos (Database)
QUEUE_CONNECTION=database

# Caché (Database)
CACHE_STORE=database

# Telegram (para notificaciones)
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_BOT_USERNAME=TuBot
```

### 2. Cron Job (para procesar la cola)

En **CPanel > Cron Jobs**, configurar:

```bash
* * * * * cd /home/usuario/public_html && php artisan schedule:run >> /dev/null 2>&1
```

En `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Procesar cola cada minuto
    $schedule->command('queue:work --stop-when-empty')
        ->everyMinute()
        ->withoutOverlapping();
}
```

### 3. Migraciones

El sistema usa la tabla `documents` existente con estos campos:

```php
- ocr_status: pending | processing | completed | failed
- ocr_data: JSON con datos extraídos
- quality_status: VALIDATED | MANUAL_CHECK | FAILED
- rejection_reason: Motivo si falla validación
- validated: boolean
- validated_at: timestamp
```

---

## 🚀 Uso

### Desde Telegram

1. Usuario envía foto de factura al bot
2. Bot recibe y encola el procesamiento
3. Sistema procesa asíncronamente
4. Usuario recibe notificación con resultado

### Desde Código

```php
use App\Jobs\OcrInvoiceProcessingJob;
use App\Models\User;

$user = User::find(1);

OcrInvoiceProcessingJob::dispatch(
    $user,
    'telegram_file_id',
    'factura.jpg',
    'image/jpeg',
    12345678, // chat_id
    'Contexto adicional opcional'
);
```

### Validación Manual

```php
use App\Services\DnitConnector;

$dnit = new DnitConnector();

// Validar RUC
$result = $dnit->validateRuc('80000001-7');

// Validar Timbrado
$result = $dnit->validateTimbrado('12345678', '80000001-7');

// Validar factura completa
$result = $dnit->validateInvoice([
    'ruc_emisor' => '80000001-7',
    'timbrado' => '12345678',
    'fecha_emision' => '2025-12-10',
    'monto_total' => 1500000
]);
```

---

## 🔧 Mantenimiento

### Limpiar Caché de DNIT

```php
use App\Services\DnitConnector;

$dnit = new DnitConnector();

// Limpiar RUC específico
$dnit->clearRucCache('80000001-7');

// Limpiar Timbrado específico
$dnit->clearTimbradoCache('12345678', '80000001-7');
```

### Monitorear Jobs

```bash
# Ver jobs pendientes
php artisan queue:work --once

# Ver jobs fallidos
php artisan queue:failed

# Reintentar job fallido
php artisan queue:retry {job_id}

# Limpiar jobs fallidos
php artisan queue:flush
```

### Logs

El sistema registra todos los eventos importantes:

```bash
tail -f storage/logs/laravel.log | grep -E "(OCR|DNIT|OcrInvoiceProcessingJob)"
```

Eventos logueados:
- 🚀 Inicio de procesamiento
- 📄 Documento creado
- 🔍 Inicio de OCR
- ✅ OCR completado
- 🔐 Inicio de validación DNIT
- 🏛️ Validación DNIT completada
- 📁 Documento organizado
- ✨ Procesamiento completado
- ❌ Errores críticos

---

## 🎯 Optimizaciones para Shared Hosting

1. **Queue Driver**: `database` (no requiere Redis/Supervisor)
2. **Cache Driver**: `database` (no requiere Redis/Memcached)
3. **Timeout Largo**: 600s para manejar OCR lento
4. **Reintentos**: 3 intentos con backoff exponencial
5. **Caché de 30 días**: Reduce llamadas a DNIT
6. **Procesamiento Cron**: Compatible con CPanel

---

## 📊 Métricas y Monitoreo

### Campos de Análisis

```php
Document::where('quality_status', 'VALIDATED')->count();     // Facturas validadas
Document::where('quality_status', 'MANUAL_CHECK')->count();  // Requieren revisión
Document::where('ocr_status', 'failed')->count();            // Fallos de OCR

// Tasa de éxito
$total = Document::count();
$validated = Document::where('quality_status', 'VALIDATED')->count();
$successRate = ($validated / $total) * 100;
```

### Calidad de OCR

```php
$documents = Document::whereNotNull('ocr_data')->get();

foreach ($documents as $doc) {
    $completeness = $doc->ocr_data['validation']['completeness'] ?? 0;
    echo "Documento #{$doc->id}: {$completeness}% de completitud\n";
}
```

---

## 🐛 Troubleshooting

### Problema: OCR no extrae datos

**Solución:**
1. Verificar `OPENAI_API_KEY` en `.env`
2. Verificar formato de imagen (JPEG/PNG)
3. Revisar logs: `storage/logs/laravel.log`
4. Probar con imagen de mejor calidad

### Problema: Validación DNIT falla

**Solución:**
1. Verificar credenciales `DNIT_USERNAME` y `DNIT_PASSWORD`
2. En desarrollo, el sistema usa validaciones simuladas
3. Verificar conectividad con `https://ekuatia.set.gov.py`
4. Revisar logs de errores específicos

### Problema: Jobs no se procesan

**Solución:**
1. Verificar Cron Job configurado en CPanel
2. Ejecutar manualmente: `php artisan queue:work --once`
3. Verificar tabla `jobs` tiene registros
4. Revisar tabla `failed_jobs` para errores

### Problema: Cache no funciona

**Solución:**
1. Verificar `CACHE_STORE=database` en `.env`
2. Ejecutar: `php artisan cache:clear`
3. Verificar tabla `cache` existe
4. Ejecutar: `php artisan migrate`

---

## 📝 Notas Importantes

### Limitaciones de Shared Hosting

✅ **Compatible:**
- Queue driver: database
- Cache driver: database
- Cron jobs (scheduling)
- Timeouts largos

❌ **No Compatible:**
- Redis/Memcached
- Supervisor
- WebSockets
- Procesos en segundo plano persistentes

### Costos Estimados

**OpenAI Vision API (gpt-4o-mini):**
- Costo por imagen: ~$0.001 USD
- 1000 facturas/mes: ~$1 USD

**DNIT/SET:**
- API pública (consultas básicas): GRATIS
- Con caché de 30 días: Reduce 95% de llamadas

### Seguridad

🔒 **Datos Sensibles:**
- `.env` en `.gitignore` ✅
- Credenciales encriptadas en base de datos
- Logs no contienen datos fiscales completos
- Archivos organizados por usuario

---

## 🎓 Referencias

- [OpenAI Vision API](https://platform.openai.com/docs/guides/vision)
- [Laravel Queues](https://laravel.com/docs/11.x/queues)
- [SET Paraguay - e-Kuatia](https://www.set.gov.py/web/ekuatia)
- [RG-90 Resolución General](https://www.set.gov.py/web/portal-institucional/resoluciones)

---

## 🤝 Soporte

Para problemas o mejoras:
1. Revisar logs: `storage/logs/laravel.log`
2. Verificar configuración: `.env`
3. Consultar esta documentación
4. Contactar al equipo de desarrollo

---

**Versión del Sistema**: 1.0.0
**Fecha de Actualización**: 2025-12-10
**Autor**: Aranduka Development Team
