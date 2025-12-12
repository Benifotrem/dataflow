#!/bin/bash

echo "================================"
echo "  CONFIGURACIÓN BOT TELEGRAM"
echo "================================"
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: No se encuentra el archivo artisan${NC}"
    echo "Ejecuta este script desde el directorio raíz de Laravel"
    exit 1
fi

# Solicitar token del bot
echo -e "${YELLOW}📱 Paso 1: Token del Bot${NC}"
echo "Obtén el token desde @BotFather en Telegram"
echo ""
read -p "Ingresa el TELEGRAM_BOT_TOKEN: " BOT_TOKEN

if [ -z "$BOT_TOKEN" ]; then
    echo -e "${RED}❌ Error: El token no puede estar vacío${NC}"
    exit 1
fi

# Solicitar username del bot
echo ""
echo -e "${YELLOW}👤 Paso 2: Username del Bot${NC}"
echo "Ejemplo: @MiBot"
echo ""
read -p "Ingresa el TELEGRAM_BOT_USERNAME: " BOT_USERNAME

if [ -z "$BOT_USERNAME" ]; then
    echo -e "${RED}❌ Error: El username no puede estar vacío${NC}"
    exit 1
fi

# Asegurarse de que empiece con @
if [[ ! "$BOT_USERNAME" == @* ]]; then
    BOT_USERNAME="@$BOT_USERNAME"
fi

# Obtener APP_URL del .env actual
APP_URL=$(grep "^APP_URL=" .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")

if [ -z "$APP_URL" ]; then
    echo ""
    echo -e "${YELLOW}🌐 Paso 3: URL de la Aplicación${NC}"
    read -p "Ingresa la APP_URL (ej: https://dataflow.guaraniappstore.com): " APP_URL
fi

WEBHOOK_URL="${APP_URL}/api/telegram/webhook"

echo ""
echo -e "${GREEN}📝 Configuración a aplicar:${NC}"
echo "  Token: ${BOT_TOKEN:0:10}..."
echo "  Username: $BOT_USERNAME"
echo "  Webhook URL: $WEBHOOK_URL"
echo ""
read -p "¿Continuar? (s/n): " CONFIRM

if [[ ! "$CONFIRM" =~ ^[sS]$ ]]; then
    echo "Cancelado por el usuario"
    exit 0
fi

echo ""
echo -e "${YELLOW}⚙️  Actualizando archivo .env...${NC}"

# Verificar si ya existen las variables
if grep -q "^TELEGRAM_BOT_TOKEN=" .env; then
    # Actualizar existentes
    sed -i "s|^TELEGRAM_BOT_TOKEN=.*|TELEGRAM_BOT_TOKEN=\"${BOT_TOKEN}\"|g" .env
    sed -i "s|^TELEGRAM_BOT_USERNAME=.*|TELEGRAM_BOT_USERNAME=\"${BOT_USERNAME}\"|g" .env
    sed -i "s|^TELEGRAM_WEBHOOK_URL=.*|TELEGRAM_WEBHOOK_URL=\"${WEBHOOK_URL}\"|g" .env
    echo -e "${GREEN}✓ Variables actualizadas en .env${NC}"
else
    # Agregar al final del archivo
    cat >> .env << EOF

# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN="${BOT_TOKEN}"
TELEGRAM_BOT_USERNAME="${BOT_USERNAME}"
TELEGRAM_WEBHOOK_URL="${WEBHOOK_URL}"
EOF
    echo -e "${GREEN}✓ Variables agregadas a .env${NC}"
fi

echo ""
echo -e "${YELLOW}🔄 Limpiando caché de configuración...${NC}"
php artisan config:clear
echo -e "${GREEN}✓ Caché limpiada${NC}"

echo ""
echo -e "${YELLOW}🌐 Configurando webhook en Telegram...${NC}"
php artisan telegram:manage setup

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ ¡Webhook configurado exitosamente!${NC}"
else
    echo ""
    echo -e "${RED}❌ Error al configurar webhook${NC}"
    echo "Revisa los logs para más detalles"
    exit 1
fi

echo ""
echo -e "${YELLOW}ℹ️  Verificando configuración...${NC}"
php artisan telegram:manage info

echo ""
echo "================================"
echo -e "${GREEN}✅ CONFIGURACIÓN COMPLETADA${NC}"
echo "================================"
echo ""
echo "Próximos pasos:"
echo "1. Abre Telegram y busca: $BOT_USERNAME"
echo "2. Envía /start al bot"
echo "3. Envía /link para vincular tu cuenta"
echo "4. Ve a Configuración en el dashboard para ver las instrucciones"
echo ""
echo "Para probar el bot, envía una factura (PDF o imagen)"
echo ""
