# Sistema Offline de Consulta de RUC - SET Paraguay

## 📋 Descripción

Sistema completo para descargar, procesar y consultar datos oficiales de RUC (Registro Único de Contribuyentes) de la SET Paraguay de forma **100% offline**.

## 🎯 Características

- ✅ **Datos Oficiales**: Descarga directa desde SET Paraguay
- ✅ **100% Offline**: No requiere API keys ni conexión externa
- ✅ **Gratuito**: No tiene costos de uso
- ✅ **Shared Hosting**: Compatible con hosting compartido
- ✅ **Base de Datos Local**: Consultas ultra-rápidas
- ✅ **Actualizable**: Comando simple para actualizar datos

## 🏗️ Arquitectura

### Componentes

1. **Migración**: `database/migrations/xxxx_create_ruc_contribuyentes_table.php`
   - Tabla `ruc_contribuyentes` con índices optimizados
   - Campos: RUC, DV, razón social, tipo, estado

2. **Comando de Descarga**: `app/Console/Commands/DownloadRucData.php`
   - Descarga archivos ZIP oficiales de la SET
   - Procesa y carga datos a MySQL
   - Soporte para actualizaciones incrementales

3. **Integración DnitConnector**: `app/Services/DnitConnector.php`
   - Consultas RUC desde base de datos local
   - Fallback a modo simulado si no hay datos
   - Caché de 30 días para optimizar

## 📦 Instalación

### 1. Ejecutar Migración

```bash
php artisan migrate
```

### 2. Descargar Datos de RUC

```bash
# Descargar todos los archivos (0-9)
php artisan ruc:download

# Descargar solo un archivo específico (más rápido para pruebas)
php artisan ruc:download --file=5

# Procesar archivos existentes sin descargar
php artisan ruc:download --skip-download
```

El comando descargará aproximadamente 10 archivos ZIP desde:
```
http://www.set.gov.py/rest/contents/download/collaboration/sites/PARAGUAY-SET/documents/informes-periodicos/ruc/
```

### 3. Verificar Datos

```bash
php artisan tinker
```

```php
// Verificar cantidad de registros
DB::table('ruc_contribuyentes')->count();

// Buscar un RUC específico
DB::table('ruc_contribuyentes')->where('ruc', '9028805')->first();
```

## 🚀 Uso

### Desde DnitConnector

```php
$dnit = new \App\Services\DnitConnector();

$resultado = $dnit->validateRuc('9028805-0');

if ($resultado['valid']) {
    echo "RUC válido\n";
    echo "Razón Social: " . $resultado['data']['razon_social'] . "\n";
    echo "Estado: " . $resultado['data']['estado'] . "\n";
} else {
    echo "Error: " . $resultado['error'] . "\n";
}
```

### Desde Job de OCR

El job `OcrInvoiceProcessingJob` usa automáticamente el DnitConnector, por lo que las validaciones de RUC serán offline:

```php
OcrInvoiceProcessingJob::dispatch($tenant, $documentUrl, $metadata);
```

## 🔄 Actualización de Datos

### Frecuencia Recomendada

- **Producción**: Cada 30 días (los RUC no cambian tan frecuentemente)
- **Desarrollo**: Solo cuando sea necesario

### Automatizar con Cron

Agregar a `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Actualizar RUC cada 30 días a las 2 AM
    $schedule->command('ruc:download')
        ->monthlyOn(1, '02:00')
        ->withoutOverlapping();
}
```

## 📊 Estructura de Datos

### Tabla: `ruc_contribuyentes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT | ID autoincremental |
| ruc | VARCHAR(20) | RUC sin guión (único, indexado) |
| dv | VARCHAR(2) | Dígito verificador |
| razon_social | VARCHAR(255) | Nombre o razón social (fulltext) |
| tipo_contribuyente | VARCHAR(50) | Tipo de contribuyente |
| estado | VARCHAR(50) | ACTIVO, INACTIVO, etc. |
| ruc_anterior | VARCHAR(20) | RUC anterior si fue reemplazado |
| datos_adicionales | TEXT | JSON con datos extras |
| fecha_actualizacion_set | TIMESTAMP | Última actualización en SET |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Última actualización |

### Índices

- **PRIMARY**: id
- **UNIQUE**: ruc
- **INDEX**: ruc, razon_social, estado
- **FULLTEXT**: razon_social (búsqueda por nombre)

## 🎛️ Configuración

### Variables de Entorno

```env
# No requiere API keys para RUC offline
# Solo configurar si quieres forzar modo simulado en desarrollo
DNIT_SIMULATE=false
```

### Modo Simulado (Desarrollo)

En `config/services.php` agregar:

```php
'dnit' => [
    'simulate' => env('DNIT_SIMULATE', false),
    // ... resto de configuración
],
```

## 🔍 Troubleshooting

### Error: "RUC no encontrado en la base de datos local"

**Solución**: Ejecutar `php artisan ruc:download` para descargar los datos.

### Error: "Table 'ruc_contribuyentes' doesn't exist"

**Solución**: Ejecutar `php artisan migrate`.

### Descarga muy lenta

**Opciones**:
1. Descargar solo archivos necesarios: `php artisan ruc:download --file=5`
2. Los archivos pueden ser grandes (varios MB cada uno)
3. Ejecutar en horarios de baja demanda

### Error de memoria al procesar

**Solución**: El comando procesa en lotes de 500 registros. Si aún así hay problemas:

```bash
# Aumentar límite de memoria en php.ini
memory_limit = 512M

# O ejecutar con límite aumentado
php -d memory_limit=512M artisan ruc:download
```

## 📈 Rendimiento

- **Consultas**: < 10ms (con índices)
- **Almacenamiento**: ~100-500 MB (todos los RUC de Paraguay)
- **Actualización**: 5-15 minutos (todos los archivos)

## 🔐 Seguridad

- ✅ **Datos Públicos**: Los RUC son información pública de la SET
- ✅ **Sin API Keys**: No maneja credenciales sensibles
- ✅ **Offline**: No expone datos a servicios externos
- ✅ **Validación**: Limpia y valida datos antes de insertar

## 📝 Notas Importantes

1. Los archivos de la SET pueden cambiar de formato sin previo aviso
2. El parseo está optimizado para el formato actual (pipe o tab delimited)
3. Si la SET cambia las URLs, actualizar `$baseUrl` en `DownloadRucData.php`
4. Los datos se actualizan con `upsert` (insert or update)

## 🆚 Comparación con Alternativas

| Característica | RUC Offline | API SET Oficial | Paquetes Terceros |
|----------------|-------------|-----------------|-------------------|
| **Costo** | Gratis | Requiere autorización | Pago mensual |
| **API Key** | No | Sí | Sí |
| **Offline** | ✅ | ❌ | ❌ |
| **Shared Hosting** | ✅ | ✅ | ✅ |
| **Datos Oficiales** | ✅ | ✅ | ✅ |
| **Actualización** | Manual/Cron | Tiempo real | Automática |

## 🔗 Referencias

- **Fuente de Datos**: [DNIT Paraguay - Listado de RUC](https://www.dnit.gov.py/web/portal-institucional/listado-de-ruc-con-sus-equivalencias)
- **Formato**: Archivos ZIP con TXT delimitados
- **Licencia**: Datos públicos del gobierno de Paraguay

---

**Desarrollado para Aranduka-Core Platform**
Sistema de gestión fiscal inteligente para Paraguay 🇵🇾
