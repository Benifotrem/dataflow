# Dataflow

## Plataforma SaaS de Automatización Contable Multi-Jurisdiccional

Dataflow es una plataforma moderna de automatización contable que utiliza inteligencia artificial para simplificar la gestión fiscal y contable de pequeñas y medianas empresas, así como de despachos contables en España e Hispanoamérica.

---

## Características Principales

### 1. Arquitectura Multi-Tenant
- Soporte para múltiples organizaciones aisladas (B2C y B2B)
- Gestión de múltiples entidades fiscales por tenant
- Optimizado para hosting compartido (Hostinger)

### 2. Niveles de Servicio

#### Plan Básico (B2C)
- Orientado a PyMEs y personas físicas
- 1 entidad fiscal
- 500 documentos IA/mes incluidos
- Precio base: $19.99/mes

#### Plan Avanzado (B2B)
- Orientado a contadores y despachos
- Gestión ilimitada de clientes
- 500 documentos IA/mes incluidos
- Colaboración síncrona (Propietario/Asesor)
- Precio base: $49.99/mes

### 3. Automatización con IA
- OCR inteligente para documentos (facturas, recibos, extractos)
- Clasificación automática de transacciones
- Soporte para OpenAI y OpenRoute
- Límite de 500 documentos/mes (con posibilidad de addons)

### 4. Conciliación Bancaria
- Importación manual de extractos (PDF, Excel, CSV, Imágenes)
- No conexión directa con bancos (política de seguridad)
- Conciliación automática con transacciones

### 5. Localización Fiscal
- Soporte para España y toda Hispanoamérica
- Configuración de reglas fiscales por país
- Gestión de IVA/VAT, retenciones y otros impuestos

### 6. Política de Retención de Datos
- Extractos bancarios: retención máxima de 60 días desde fin de mes
- Eliminación automática física y lógica
- Cumplimiento con normativas de protección de datos

### 7. Importación/Exportación CSV
- Mapeador visual de columnas
- Compatible con Excel, Google Sheets y Apple Numbers
- Plantillas predefinidas

### 8. Calendario Fiscal (iCalendar)
- Generación de feeds .ics por entidad
- Sincronización con Google Calendar, Apple Calendar, Outlook
- Recordatorios automáticos de plazos fiscales

### 9. Panel de Administración
- Gestión de precios y planes
- Configuración de credenciales de IA
- Configuración de correo transaccional (Brevo)
- Métricas y estadísticas del sistema

---

## Tecnologías Utilizadas

- **Backend**: PHP 8.4 / Laravel 12
- **Base de Datos**: MySQL
- **Frontend**: Laravel Blade, Vue.js 3, Tailwind CSS
- **IA**: OpenAI GPT-4o-mini / OpenRoute
- **Email**: Brevo (transaccional)
- **Almacenamiento**: Local / S3-compatible

---

## Requisitos

- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js >= 18.x
- NPM o Yarn

---

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/dataflow.git
cd dataflow
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con tus configuraciones:
```env
APP_NAME=Dataflow
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dataflow
DB_USERNAME=root
DB_PASSWORD=

# AI API Configuration
OPENAI_API_KEY=tu_api_key
AI_PROVIDER=openai
AI_MODEL=gpt-4o-mini

# Brevo Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_password
BREVO_API_KEY=tu_api_key

# Dataflow Configuration
DOCUMENT_LIMIT_BASE=500
DATA_RETENTION_DAYS=60
ADDON_PRICE_PER_500_DOCS=9.99
```

### 4. Ejecutar migraciones
```bash
php artisan migrate --seed
```

### 5. Compilar assets
```bash
npm run build
```

### 6. Iniciar servidor de desarrollo
```bash
php artisan serve
```

---

## Estructura de Base de Datos

### Tablas Principales

#### `tenants`
- Organizaciones/Cuentas principales
- Tipos: B2C (Individual) / B2B (Despacho)
- Configuración de país y moneda

#### `users`
- Usuarios del sistema
- Roles: owner (propietario), advisor (asesor), admin (superadmin)
- Relación many-to-one con tenants

#### `entities`
- Entidades fiscales
- Configuración fiscal por jurisdicción
- Plan contable personalizado

#### `documents`
- Documentos procesados por OCR/IA
- Estados: pending, processing, completed, failed
- Metadatos extraídos (importe, fecha, emisor, etc.)

#### `transactions`
- Transacciones contables
- Clasificación fiscal automática
- Conciliación bancaria

#### `bank_statements`
- Extractos bancarios
- Retención automática de 60 días
- Eliminación física programada

#### `fiscal_deadlines`
- Plazos fiscales
- Generación de feeds iCalendar
- Recordatorios automáticos

#### `ai_usage`
- Contador de uso mensual de IA
- Control de límite de 500 documentos
- Métricas por tenant

#### `addons`
- Paquetes adicionales de documentos
- Addon de 500 documentos: $9.99
- Gestión por mes/año

#### `subscriptions`
- Planes activos por tenant
- Estados: active, cancelled, expired

#### `system_settings`
- Configuración global del sistema
- Precios, credenciales de APIs
- Gestionado desde Admin Panel

---

## Seguridad

### Política de Datos Bancarios
- **Prohibida** la conexión directa con APIs bancarias
- Solo carga manual de extractos
- Retención máxima: 60 días desde fin de mes
- Eliminación automática física y lógica
- Notificaciones claras al usuario

### Protección de Datos
- Encriptación de credenciales de APIs
- Aislamiento total entre tenants
- Soft deletes en todas las tablas principales
- Auditoría de acciones sensibles

---

## Monetización

### Planes Base
- **Básico (B2C)**: $19.99/mes - 1 entidad, 500 docs/mes
- **Avanzado (B2B)**: $49.99/mes - Clientes ilimitados, 500 docs/mes

### Addons
- **500 documentos adicionales**: $9.99/mes
- Se activa automáticamente al superar el límite
- Notificación al usuario antes de aplicar cargo

---

## Landing Page y SEO

### Características de la Landing
- Diseño moderno y espectacular
- Enfoque problema → solución
- Casos de uso reales
- Testimonios y prueba social
- Call-to-action claro

### Optimización SEO
- URL amigables
- Meta tags optimizados
- Schema markup (JSON-LD)
- Sitemap.xml automático
- robots.txt configurado
- Velocidad de carga optimizada

### Páginas Legales
- FAQ completa
- Términos y Condiciones
- Política de Privacidad y Tratamiento de Datos

---

## Roadmap

### Fase 1: MVP (Actual)
- [x] Arquitectura multi-tenant
- [x] Migraciones y modelos base
- [ ] Sistema de autenticación
- [ ] Landing page y páginas legales
- [ ] Panel de administración básico

### Fase 2: Core Features
- [ ] Módulo OCR/IA
- [ ] Conciliación bancaria
- [ ] Importación/exportación CSV
- [ ] Calendario fiscal (iCalendar)

### Fase 3: Colaboración B2B
- [ ] Sistema de roles (Propietario/Asesor)
- [ ] Colaboración en tiempo real
- [ ] Notificaciones y workflows

### Fase 4: Optimización
- [ ] Caché y performance
- [ ] Tests automatizados
- [ ] CI/CD
- [ ] Monitoreo y logs

### Fase 5: Expansión
- [ ] Integraciones con ERPs
- [ ] App móvil
- [ ] Reportes avanzados
- [ ] Más países soportados

---

## Contribuir

Este es un proyecto privado. Si deseas contribuir, contacta al equipo de desarrollo.

---

## Soporte

Para soporte técnico, envía un email a: soporte@dataflow.com

---

## Licencia

Propietario. Todos los derechos reservados © 2025 Dataflow

---

## Contacto

- **Web**: https://dataflow.com
- **Email**: contacto@dataflow.com
- **Twitter**: @dataflow
