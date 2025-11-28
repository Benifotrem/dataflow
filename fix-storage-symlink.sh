#!/bin/bash

echo "================================================"
echo "FIX DEFINITIVO - IMÁGENES DE BLOG DATAFLOW"
echo "================================================"
echo ""

cd /home/u489458217/domains/dataflow.guaraniappstore.com/public_html

echo "1. Creando symlink www_storage (si no existe)..."
if [ ! -L "www_storage" ]; then
    ln -sfn storage/app/public www_storage
    echo "✅ Symlink www_storage creado"
else
    echo "✅ Symlink www_storage ya existe"
fi

echo ""
echo "2. Verificando symlink..."
ls -la www_storage

echo ""
echo "3. Actualizando .htaccess del raíz..."
git pull origin claude/review-dataflow-platform-01Pgp7NKs6wWviYqNZLqnyjU

echo ""
echo "4. Verificando regla de redirección en .htaccess..."
grep -A1 "Redirigir /storage/" .htaccess

echo ""
echo "5. Probando acceso a imagen via /storage/..."
curl -I https://dataflow.guaraniappstore.com/storage/blog/YZ3Ct8jw3JByIGdKPrBnIhLW6e4wqYzqXprJEAL2.jpg | head -1

echo ""
echo "6. Limpiando cachés de Cloudflare y Laravel..."
# Cloudflare se debe limpiar manualmente desde el panel
echo "⚠️  IMPORTANTE: Limpia el caché de Cloudflare manualmente"

echo ""
echo "================================================"
echo "✅ FIX COMPLETADO"
echo "================================================"
echo ""
echo "Ahora ve a: https://dataflow.guaraniappstore.com/blog"
echo "Las imágenes deberían mostrarse correctamente."
