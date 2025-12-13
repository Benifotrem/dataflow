# 🤖 Configuración Completa de Mini App en BotFather

## 📋 Requisitos Previos

- ✅ Backend implementado y funcionando
- ✅ Servidor accesible en `https://dataflow.guaraniappstore.com`
- ✅ Bot de Telegram: `@dataflow_guaraniappstore_bot`
- ✅ Token del bot configurado en `.env`

---

## 1️⃣ Crear Mini App con @BotFather

### Paso 1: Iniciar conversación con BotFather

Abre Telegram y busca `@BotFather`, luego ejecuta:

```
/newapp
```

### Paso 2: Seleccionar el bot

Cuando BotFather te pregunte "Choose a bot to create a Web App for", envía:

```
@dataflow_guaraniappstore_bot
```

### Paso 3: Enviar título de la Mini App

Cuando te pida "Send me the title for your Web App", envía:

```
📱 Dataflow - Gestión de Facturas
```

### Paso 4: Enviar descripción

Cuando te pida "Send me a description for your Web App", envía:

```
🇵🇾 Gestiona tus facturas de compra con OCR inteligente y consulta facturas electrónicas en la SET de Paraguay. Controla IVA crédito, exporta reportes fiscales y mantén tu contabilidad al día desde Telegram.

✨ Características:
• 📊 Dashboard con métricas en tiempo real
• 🔍 Consulta facturas electrónicas (eKuatia/SET)
• 📸 Escaneo de QR de facturas
• 📥 Exportación Excel formato RG-90
• 💰 Desglose automático de IVA 10%, 5% y exentas
• 📈 Gráficos de evolución mensual
```

### Paso 5: Subir foto (640x360px)

Cuando te pida "Now send me a photo or animation for your Web App", puedes:

**Opción A:** Enviar una imagen de 640x360px con el logo de Dataflow
**Opción B:** Saltar este paso enviando `/empty`

```
/empty
```

### Paso 6: Subir GIF/animación (opcional)

Si te pide una animación, puedes enviar:

```
/empty
```

### Paso 7: Configurar URL de la Mini App

Cuando te pida "Now send me a URL to the Web App", envía:

```
https://dataflow.guaraniappstore.com/miniapp
```

---

## 2️⃣ Configurar Botón de Menú para la Mini App

### Paso 1: Abrir configuración de menú

En @BotFather, ejecuta:

```
/mybots
```

Selecciona: `@dataflow_guaraniappstore_bot`

### Paso 2: Configurar botón de menú

Selecciona: `Bot Settings` → `Menu Button` → `Configure Menu Button`

Cuando te pida el texto del botón, envía:

```
🚀 Abrir Dataflow
```

Cuando te pida la URL, envía:

```
https://dataflow.guaraniappstore.com/miniapp
```

---

## 3️⃣ Configurar Comando /app (Opcional pero Recomendado)

Edita `app/Http/Controllers/TelegramController.php` y agrega este código en el método `handleMessage()`:

```php
// Comando /app - Abrir Mini App
if ($text === '/app' || $text === '/app@dataflow_guaraniappstore_bot') {
    $this->telegramService->sendMessage(
        $chatId,
        "📱 *Dataflow Mini App*\n\n" .
        "Presiona el botón de abajo para abrir la aplicación móvil completa\\.\n\n" .
        "✨ Podrás:\n" .
        "• Ver dashboard con métricas\n" .
        "• Consultar facturas electrónicas\n" .
        "• Escanear códigos QR\n" .
        "• Exportar reportes fiscales\n" .
        "• Gestionar todas tus facturas",
        'MarkdownV2',
        null,
        [[
            'text' => '🚀 Abrir Dataflow',
            'web_app' => ['url' => 'https://dataflow.guaraniappstore.com/miniapp']
        ]]
    );
    return;
}
```

Luego actualiza la lista de comandos en BotFather:

```
/mybots
```

Selecciona: `@dataflow_guaraniappstore_bot` → `Edit Commands`

Envía la lista completa de comandos:

```
start - Iniciar el bot
help - Ver ayuda y funciones
app - 📱 Abrir Mini App de Dataflow
processar - Procesar factura desde foto
consultar - Consultar factura electrónica por CDC
exportar - Exportar liquidación de IVA
```

---

## 4️⃣ Crear Frontend de la Mini App

### Estructura de archivos

```
public/miniapp/
├── index.html
├── app.js
├── components/
│   ├── Dashboard.js
│   ├── DocumentList.js
│   ├── CDCConsult.js
│   └── ExportDialog.js
└── styles.css
```

### Archivo principal: `public/miniapp/index.html`

Ver detalles completos en `docs/TELEGRAM_MINIAPP_GUIDE.md`

Estructura básica:

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
        const tg = window.Telegram.WebApp;
        tg.ready();
        tg.expand();

        const initData = tg.initData;
        const apiHeaders = {
            'Content-Type': 'application/json',
            'X-Telegram-Init-Data': initData
        };

        // Tu aplicación React aquí
    </script>
</body>
</html>
```

---

## 5️⃣ Deployment a Producción

### Paso 1: Verificar que todo está en Git

```bash
cd /home/user/dataflow
git status
git log --oneline -5
```

### Paso 2: Merge a main

```bash
# Cambiar a main
git checkout main

# Hacer pull de cambios remotos
git pull origin main

# Merge de la rama de desarrollo
git merge claude/aranduka-core-architecture-013R2N35J7x7K8PwQETakzRW

# Push a main
git push -u origin main
```

### Paso 3: Deploy en el servidor de producción

```bash
# Conectar al servidor
ssh u489458217@dataflow.guaraniappstore.com

# Ir al directorio del proyecto
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

# Hacer pull de los cambios
git pull origin main

# Instalar dependencias (si hay nuevas)
composer install --optimize-autoloader --no-dev

# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cachear configuración para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permisos
chmod -R 775 storage bootstrap/cache
```

### Paso 4: Verificar variables de entorno

Asegúrate de que `.env` en producción tenga:

```env
TELEGRAM_BOT_TOKEN=tu_token_real_del_bot
TELEGRAM_BOT_USERNAME=dataflow_guaraniappstore_bot

# URLs correctas
APP_URL=https://dataflow.guaraniappstore.com
```

### Paso 5: Probar la Mini App

1. Abre Telegram
2. Busca `@dataflow_guaraniappstore_bot`
3. Envía `/app`
4. Presiona el botón "🚀 Abrir Dataflow"
5. Deberías ver la Mini App cargando

---

## 6️⃣ Testing y Validación

### Test 1: Autenticación

Verifica que el middleware funcione:

```bash
# En el servidor
tail -f storage/logs/laravel.log
```

Luego abre la Mini App y verifica que no haya errores 401/403.

### Test 2: Endpoint Dashboard

```bash
# Desde la Mini App, abre la consola del navegador (Telegram Desktop)
fetch('/api/miniapp/dashboard', {
    headers: {
        'X-Telegram-Init-Data': window.Telegram.WebApp.initData
    }
}).then(r => r.json()).then(console.log)
```

Deberías ver la respuesta JSON con las métricas.

### Test 3: Consulta CDC

En la Mini App:
1. Ve a "Consultar Factura"
2. Ingresa un CDC de prueba (o escanea un QR)
3. Verifica que la consulta funcione

---

## 7️⃣ Troubleshooting

### Error: "Invalid hash"

**Problema:** El middleware rechaza la autenticación
**Solución:** Verifica que `TELEGRAM_BOT_TOKEN` en `.env` sea correcto

### Error: 404 en endpoints

**Problema:** Las rutas no se registran
**Solución:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list | grep miniapp
```

### Error: CORS

**Problema:** Telegram bloquea las peticiones
**Solución:** Las Mini Apps no sufren de CORS porque se sirven desde el mismo dominio

### Mini App no se abre

**Problema:** La URL no está bien configurada
**Solución:** Verifica en BotFather que la URL sea exactamente:
```
https://dataflow.guaraniappstore.com/miniapp
```

---

## 8️⃣ Próximos Pasos

- [ ] Implementar frontend React completo (ver `TELEGRAM_MINIAPP_GUIDE.md`)
- [ ] Crear assets visuales (logo 640x360px, GIF demo)
- [ ] Configurar analytics para la Mini App
- [ ] Agregar notificaciones push
- [ ] Implementar modo offline con Service Workers

---

## 📚 Referencias

- [Telegram Mini Apps Docs](https://core.telegram.org/bots/webapps)
- [BotFather Commands](https://core.telegram.org/bots#botfather)
- [Telegram WebApp SDK](https://core.telegram.org/bots/webapps#initializing-mini-apps)

---

## ✅ Checklist Final

- [ ] Mini App creada en BotFather
- [ ] URL configurada: `https://dataflow.guaraniappstore.com/miniapp`
- [ ] Botón de menú configurado
- [ ] Comando `/app` implementado
- [ ] Frontend React creado en `public/miniapp/`
- [ ] Código merged a `main`
- [ ] Deployed en producción
- [ ] Variables `.env` configuradas
- [ ] Cachés limpiados
- [ ] Testing completado
- [ ] Mini App funcionando en Telegram

---

**¡Todo listo!** 🚀 Una vez completes estos pasos, tus usuarios podrán gestionar sus facturas desde la Mini App de Telegram.
