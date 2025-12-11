#!/bin/bash

echo "🛑 DETENIENDO BUCLE DE EMERGENCIA..."

# Leer token
BOT_TOKEN=$(grep TELEGRAM_BOT_TOKEN .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")

# 1. Eliminar webhook
echo "1️⃣ Eliminando webhook..."
curl -X POST "https://api.telegram.org/bot${BOT_TOKEN}/deleteWebhook?drop_pending_updates=true"
echo ""

# 2. Detener workers de queue
echo "2️⃣ Matando procesos de queue..."
pkill -f "artisan queue"
echo "✅ Procesos detenidos"

# 3. Limpiar todos los jobs fallidos
echo "3️⃣ Limpiando jobs fallidos..."
php artisan queue:flush
echo "✅ Cola limpiada"

# 4. Limpiar caché
echo "4️⃣ Limpiando caché..."
php artisan cache:clear
php artisan config:clear

echo ""
echo "✅ BUCLE DETENIDO"
echo ""
echo "Ahora ejecuta: tail -100 storage/logs/laravel.log"
echo "Para ver qué causó el error."
