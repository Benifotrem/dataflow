#!/bin/bash

echo "==================================="
echo "FIX DE IMÁGENES DE BLOG - DATAFLOW"
echo "==================================="
echo ""

# Navegar al directorio
cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

echo "1. Verificando branch actual..."
git branch

echo ""
echo "2. Verificando commits locales..."
git log --oneline -3

echo ""
echo "3. Haciendo pull de los últimos cambios..."
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

echo ""
echo "4. Verificando rutas de imágenes en el código..."
grep "asset.*featured_image" resources/views/landing/blog.blade.php | head -1
grep "asset.*featured_image" resources/views/landing/blog-show.blade.php | head -2

echo ""
echo "5. Verificando symlink storage..."
ls -la public/storage

echo ""
echo "6. Verificando imágenes físicas..."
echo "Imágenes en storage/app/public/blog:"
ls -l storage/app/public/blog/ | head -5

echo ""
echo "7. Limpiando cachés..."
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

echo ""
echo "8. Verificando contenido de la base de datos..."
php artisan tinker --execute="echo 'Featured images en DB:'; \$posts = App\Models\Post::whereNotNull('featured_image')->get(['id', 'title', 'featured_image']); foreach(\$posts as \$p) { echo \$p->id . ': ' . \$p->featured_image . PHP_EOL; }"

echo ""
echo "==================================="
echo "✅ DEPLOYMENT COMPLETADO"
echo "==================================="
echo ""
echo "Verifica ahora en tu navegador:"
echo "https://dataflow.guaraniappstore.com/blog"
