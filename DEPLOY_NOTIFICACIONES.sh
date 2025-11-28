#!/bin/bash

# Script de Despliegue - Sistema de Notificaciones Dataflow
# Ejecutar en producción: bash DEPLOY_NOTIFICACIONES.sh

set -e  # Detener si hay errores

echo "======================================"
echo "DESPLIEGUE - SISTEMA DE NOTIFICACIONES"
echo "======================================"
echo ""

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}[1/7] Verificando directorio...${NC}"
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html
pwd

echo ""
echo -e "${BLUE}[2/7] Obteniendo últimos cambios del repositorio...${NC}"
git fetch origin
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

echo ""
echo -e "${BLUE}[3/7] Instalando dependencias de Composer...${NC}"
composer install --no-dev --optimize-autoloader

echo ""
echo -e "${BLUE}[4/7] Actualizando configuración .env...${NC}"
echo -e "${YELLOW}IMPORTANTE: Verifica estas variables en tu .env:${NC}"
echo "  - QUEUE_CONNECTION=sync"
echo "  - BREVO_API_KEY=tu_api_key_de_brevo"
echo "  - MAIL_FROM_ADDRESS=dataflow@guaraniappstore.com"
echo ""
read -p "¿Ya configuraste QUEUE_CONNECTION=sync en .env? (s/n): " confirm
if [ "$confirm" != "s" ]; then
    echo -e "${YELLOW}Por favor, edita el .env antes de continuar${NC}"
    echo "Ejecuta: nano .env"
    echo "Cambia: QUEUE_CONNECTION=sync"
    exit 1
fi

echo ""
echo -e "${BLUE}[5/7] Limpiando y optimizando caché...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo -e "${BLUE}[6/7] Generando cachés optimizados...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo -e "${BLUE}[7/7] Limpiando cola antigua (database)...${NC}"
php artisan queue:clear

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}✓ DESPLIEGUE COMPLETADO${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "Archivos nuevos desplegados:"
echo "  ✓ app/Notifications/VerifyEmailNotification.php"
echo "  ✓ app/Notifications/ResetPasswordNotification.php"
echo "  ✓ app/Console/Commands/SendMonthlyReports.php"
echo "  ✓ app/Services/BrevoService.php (actualizado)"
echo "  ✓ app/Http/Controllers/Auth/VerificationController.php"
echo "  ✓ resources/views/auth/verify-email.blade.php"
echo "  ✓ Rutas de verificación configuradas"
echo ""
echo "Siguiente paso:"
echo "  1. Configurar CRON en hPanel para informes mensuales"
echo "  2. Probar registro de nuevo usuario y verificación"
echo ""
echo -e "${YELLOW}Ver guía completa: cat HOSTINGER_CRON_SETUP.md${NC}"
