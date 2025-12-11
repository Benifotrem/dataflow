#!/bin/bash

echo "🔄 Reiniciando webhook de Telegram..."

# Leer el token del .env
BOT_TOKEN=$(grep TELEGRAM_BOT_TOKEN .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
WEBHOOK_URL="https://dataflow.guaraniappstore.com/api/telegram/webhook"

if [ -z "$BOT_TOKEN" ]; then
    echo "❌ Error: No se pudo leer TELEGRAM_BOT_TOKEN del archivo .env"
    exit 1
fi

# Eliminar webhook existente
echo "🗑️  Eliminando webhook anterior..."
curl -X POST "https://api.telegram.org/bot${BOT_TOKEN}/deleteWebhook"
echo ""

# Esperar 2 segundos
sleep 2

# Configurar nuevo webhook
echo "🔗 Configurando nuevo webhook: ${WEBHOOK_URL}"
RESPONSE=$(curl -X POST "https://api.telegram.org/bot${BOT_TOKEN}/setWebhook" \
     -d "url=${WEBHOOK_URL}" \
     -d "drop_pending_updates=true")

echo "$RESPONSE"
echo ""

# Verificar webhook
echo "🔍 Verificando webhook..."
curl -X POST "https://api.telegram.org/bot${BOT_TOKEN}/getWebhookInfo"
echo ""

echo "✅ Webhook reiniciado. Ahora puedes enviar un mensaje al bot para probar."
