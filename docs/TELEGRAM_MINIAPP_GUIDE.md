# Telegram Mini App - Guía de Implementación

## 📱 Funcionalidades Implementadas

### Backend (Listo ✅)

1. **Autenticación**: `TelegramMiniAppAuth` middleware con validación HMAC-SHA256
2. **API Endpoints**:
   - `GET /api/miniapp/dashboard` - Dashboard con métricas
   - `GET /api/miniapp/documents` - Lista de facturas
   - `GET /api/miniapp/documents/{id}` - Detalle de factura
   - `PATCH /api/miniapp/documents/{id}` - Editar factura
   - `POST /api/miniapp/cdc/consult` - Consultar factura electrónica por CDC
   - `POST /api/miniapp/export/vat-liquidation` - Exportar Excel
   - `GET /api/miniapp/entities` - Listar entidades fiscales

3. **Servicio DNIT**: Método `consultarCDC()` para consultas de facturas electrónicas en API SET

### Frontend (Por implementar)

La Mini App se debe crear en `public/miniapp/index.html` con React.

## 🚀 Configuración en Telegram

### 1. Hablar con @BotFather

```
/newapp
@dataflow_guaraniappstore_bot
📱 Dataflow - Gestión de Facturas
🇵🇾 Procesa facturas con OCR y consulta en la SET de Paraguay
[Subir foto del logo - 640x360px]
[Subir GIF de demostración - opcional]
```

### 2. Configurar URL de la Mini App

```
/myapps
Seleccionar app
Edit Web App URL
Ingresar: https://dataflow.guaraniappstore.com/miniapp
```

### 3. Configurar Botón en el Bot

```php
// En TelegramController.php - Agregar comando /app
if ($text === '/app') {
    $this->telegramService->sendMessage($chatId,
        "📱 *Abre la Mini App de Dataflow*",
        'MarkdownV2',
        null,
        [[
            'text' => '🚀 Abrir Dataflow',
            'web_app' => ['url' => 'https://dataflow.guaraniappstore.com/miniapp']
        ]]
    );
}
```

## 📂 Estructura de Archivos a Crear

```
public/
└── miniapp/
    ├── index.html           # Punto de entrada
    ├── app.js               # App React
    ├── components/
    │   ├── Dashboard.js
    │   ├── DocumentList.js
    │   ├── CDCConsult.js
    │   └── ExportDialog.js
    └── styles.css
```

## 📝 Ejemplo de index.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dataflow - Mini App</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div id="root"></div>

    <script>
        // Inicializar Telegram WebApp
        const tg = window.Telegram.WebApp;
        tg.ready();
        tg.expand();

        // Obtener datos de autenticación
        const initData = tg.initData;

        // Configurar headers para todas las peticiones
        const apiHeaders = {
            'Content-Type': 'application/json',
            'X-Telegram-Init-Data': initData
        };

        // Tu código React aquí...
    </script>
</body>
</html>
```

## 🎨 Componentes Principales

### Dashboard Component

```javascript
function Dashboard() {
    const [stats, setStats] = React.useState(null);

    React.useEffect(() => {
        fetch('/api/miniapp/dashboard', { headers: apiHeaders })
            .then(r => r.json())
            .then(data => setStats(data.data));
    }, []);

    if (!stats) return <div>Cargando...</div>;

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">
                {stats.current_month.name}
            </h1>

            <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="bg-purple-100 p-4 rounded-lg">
                    <div className="text-3xl font-bold text-purple-700">
                        {stats.current_month.total_invoices}
                    </div>
                    <div className="text-sm text-gray-600">Facturas</div>
                </div>

                <div className="bg-green-100 p-4 rounded-lg">
                    <div className="text-3xl font-bold text-green-700">
                        ₲ {stats.current_month.total_iva_credito.toLocaleString()}
                    </div>
                    <div className="text-sm text-gray-600">IVA Crédito</div>
                </div>
            </div>

            {/* Más componentes... */}
        </div>
    );
}
```

### CDC Consult Component

```javascript
function CDCConsult() {
    const [cdc, setCdc] = React.useState('');
    const [result, setResult] = React.useState(null);
    const [loading, setLoading] = React.useState(false);

    const handleConsult = async () => {
        setLoading(true);
        try {
            const response = await fetch('/api/miniapp/cdc/consult', {
                method: 'POST',
                headers: apiHeaders,
                body: JSON.stringify({
                    cdc: cdc,
                    entity_id: 1, // Seleccionar de lista
                    auto_save: true
                })
            });
            const data = await response.json();
            setResult(data);

            if (data.success) {
                tg.showAlert('✅ Factura guardada exitosamente');
            }
        } catch (error) {
            tg.showAlert('❌ Error al consultar CDC');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-4">
            <h2 className="text-xl font-bold mb-4">Consultar Factura Electrónica</h2>

            <input
                type="text"
                value={cdc}
                onChange={(e) => setCdc(e.target.value)}
                placeholder="Ingresa CDC de 44 dígitos"
                maxLength={44}
                className="w-full p-3 border rounded-lg mb-4"
            />

            <button
                onClick={handleConsult}
                disabled={loading || cdc.length !== 44}
                className="w-full bg-purple-600 text-white py-3 rounded-lg font-bold"
            >
                {loading ? 'Consultando...' : 'Consultar en SET'}
            </button>

            {/* Escanear QR */}
            <button
                onClick={() => {
                    tg.showScanQrPopup({}, (text) => {
                        setCdc(text);
                        tg.closeScanQrPopup();
                    });
                }}
                className="w-full mt-4 border-2 border-purple-600 text-purple-600 py-3 rounded-lg font-bold"
            >
                📷 Escanear QR
            </button>
        </div>
    );
}
```

## 🔄 Flujo de Autenticación

```
1. Usuario abre Mini App desde Telegram
2. Telegram genera initData con hash HMAC
3. Frontend envía initData en header X-Telegram-Init-Data
4. Backend valida hash con bot token
5. Backend autentica usuario por telegram_id
6. Retorna datos del usuario
```

## 📊 Métricas del Dashboard

El endpoint `/api/miniapp/dashboard` retorna:

```json
{
  "success": true,
  "data": {
    "current_month": {
      "name": "Diciembre 2025",
      "total_invoices": 124,
      "validated_set": 116,
      "pending_validation": 8,
      "total_iva_credito": 12450000,
      "breakdown": {
        "iva_10": { "base": 102000000, "iva": 10200000 },
        "iva_5": { "base": 45000000, "iva": 2250000 },
        "exentas": 1800000
      }
    },
    "charts": {
      "daily_evolution": [...],
      "top_suppliers": [...],
      "iva_distribution": [...]
    },
    "alerts": [...]
  }
}
```

## 🎯 Próximos Pasos

1. **Crear** `public/miniapp/index.html` con React
2. **Implementar** componentes (Dashboard, DocumentList, CDCConsult, Export)
3. **Configurar** Mini App en @BotFather
4. **Probar** en Telegram
5. **Desplegar** en producción

## 📚 Referencias

- [Telegram Mini Apps Documentation](https://core.telegram.org/bots/webapps)
- [Telegram WebApp SDK](https://core.telegram.org/bots/webapps#initializing-mini-apps)
- [Laravel API Resources](https://laravel.com/docs/11.x/eloquent-resources)
