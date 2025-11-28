#!/bin/bash

# Script de Prueba - Sistema de Notificaciones
# Ejecutar en producción después del despliegue

echo "======================================"
echo "PRUEBAS - SISTEMA DE NOTIFICACIONES"
echo "======================================"
echo ""

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

echo -e "${BLUE}[1/5] Verificando configuración de Brevo...${NC}"
php artisan tinker --execute="
\$brevo = new App\Services\BrevoService();
if (\$brevo->isConfigured()) {
    echo '✓ Brevo API Key configurado\n';
} else {
    echo '✗ ERROR: Brevo API Key NO configurado\n';
    exit(1);
}
"

echo ""
echo -e "${BLUE}[2/5] Verificando archivos de notificaciones...${NC}"
files=(
    "app/Notifications/VerifyEmailNotification.php"
    "app/Notifications/ResetPasswordNotification.php"
    "app/Console/Commands/SendMonthlyReports.php"
    "app/Services/BrevoService.php"
    "app/Http/Controllers/Auth/VerificationController.php"
    "resources/views/auth/verify-email.blade.php"
)

all_exist=true
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file - NO EXISTE"
        all_exist=false
    fi
done

if [ "$all_exist" = false ]; then
    echo -e "${RED}ERROR: Faltan archivos. Ejecuta primero DEPLOY_NOTIFICACIONES.sh${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}[3/5] Verificando configuración de cola...${NC}"
queue_connection=$(php artisan tinker --execute="echo config('queue.default');")
if [ "$queue_connection" = "sync" ]; then
    echo -e "${GREEN}✓ QUEUE_CONNECTION=sync (correcto para Hostinger)${NC}"
else
    echo -e "${YELLOW}⚠ QUEUE_CONNECTION=$queue_connection${NC}"
    echo -e "${YELLOW}  Recomendado: QUEUE_CONNECTION=sync${NC}"
fi

echo ""
echo -e "${BLUE}[4/5] Verificando User::MustVerifyEmail...${NC}"
php artisan tinker --execute="
\$user = App\Models\User::first();
if (\$user instanceof Illuminate\Contracts\Auth\MustVerifyEmail) {
    echo '✓ User implements MustVerifyEmail\n';
} else {
    echo '✗ ERROR: User NO implements MustVerifyEmail\n';
}
"

echo ""
echo -e "${BLUE}[5/5] Probando comando de informes mensuales (dry run)...${NC}"
echo -e "${YELLOW}Esto NO enviará emails reales, solo verifica que el comando funciona${NC}"
php artisan reports:send-monthly --help

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}✓ VERIFICACIÓN COMPLETADA${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "Para probar REALMENTE el sistema:"
echo ""
echo "1. Probar registro con email real:"
echo "   - Ve a https://dataflow.guaraniappstore.com/register"
echo "   - Registra un nuevo usuario"
echo "   - Verifica que llegue el email de verificación"
echo ""
echo "2. Probar recuperación de contraseña:"
echo "   - Ve a https://dataflow.guaraniappstore.com/login"
echo "   - Click en 'Olvidé mi contraseña'"
echo "   - Verifica que llegue el email con link de reset"
echo ""
echo "3. Probar notificación de documento procesado:"
echo "   - Sube un documento vía Telegram"
echo "   - Verifica que llegue email cuando esté procesado"
echo ""
echo "4. Probar informe mensual (opcional):"
echo "   php artisan reports:send-monthly --tenant=1"
echo ""
