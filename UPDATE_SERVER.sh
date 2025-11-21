#!/bin/bash
# Script para actualizar el servidor de producción

echo "=== ACTUALIZANDO CONTAPLUS EN PRODUCCIÓN ==="
cd /home/u489458217/domains/contaplus.guaraniappstore.com/

echo "1. Haciendo pull de los últimos cambios..."
git fetch origin
git reset --hard origin/claude/contaplus-saas-platform-01Gogn2DJtLmkTPq15MUxMWf

echo "2. Copiando archivos públicos a public_html..."
cp -r public/* public_html/
cp public/.htaccess public_html/

echo "3. Limpiando cache..."
php artisan optimize:clear

echo "4. Verificando permisos..."
chmod -R 775 storage bootstrap/cache

echo "=== ACTUALIZACIÓN COMPLETADA ==="
echo "Verifica: https://contaplus.guaraniappstore.com/"
