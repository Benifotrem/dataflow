#!/bin/bash

echo "🔧 Resolviendo conflicto de dependencias de Composer..."

# Paso 1: Eliminar composer.lock para forzar resolución limpia
echo "📦 Paso 1: Eliminando composer.lock..."
rm -f composer.lock

# Paso 2: Instalar dependencias desde cero
echo "📦 Paso 2: Instalando dependencias..."
composer install --no-dev --optimize-autoloader

# Paso 3: Verificar que Imagick esté instalada
echo "🔍 Paso 3: Verificando extensión Imagick..."
php -m | grep imagick
if [ $? -eq 0 ]; then
    echo "✅ Imagick está instalada"
else
    echo "⚠️  Imagick NO está instalada. Necesitas activarla en hPanel o instalarla:"
    echo "   sudo apt-get install php-imagick"
    echo "   sudo systemctl restart php-fpm"
fi

# Paso 4: Verificar que spatie/pdf-to-image esté disponible
echo "🔍 Paso 4: Verificando spatie/pdf-to-image..."
php -r "echo class_exists('Spatie\PdfToImage\Pdf') ? '✅ Spatie PDF instalado' : '❌ Spatie PDF NO instalado';" 2>&1

# Paso 5: Limpiar caché de Laravel
echo "🧹 Paso 5: Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "✅ Script completado. Ahora ejecuta:"
echo "   ./restart-webhook.sh"
