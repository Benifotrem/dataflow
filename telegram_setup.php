<?php

/**
 * Script de configuración del webhook de Telegram
 *
 * Este script configura el webhook del bot de Telegram para recibir actualizaciones.
 *
 * Uso recomendado: php artisan telegram:manage setup
 *
 * Este archivo se mantiene para compatibilidad, pero se recomienda usar el comando Artisan.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$telegram = $app->make(\App\Services\TelegramService::class);
$webhookUrl = config('services.telegram.webhook_url');

if (!$webhookUrl) {
    echo "❌ Error: TELEGRAM_WEBHOOK_URL no está configurado en .env\n";
    exit(1);
}

echo "═══════════════════════════════════════\n";
echo "  🤖 Configuración de Telegram Bot\n";
echo "═══════════════════════════════════════\n\n";
echo "🔧 Configurando webhook...\n";
echo "URL: $webhookUrl\n\n";

$result = $telegram->setWebhook($webhookUrl);

if ($result['success']) {
    echo "✅ Webhook configurado correctamente\n\n";

    echo "📊 Información del webhook:\n";
    $info = $telegram->getWebhookInfo();

    if ($info['success']) {
        echo json_encode($info['data'], JSON_PRETTY_PRINT) . "\n\n";
        echo "✅ Todo está configurado correctamente\n";
    }
} else {
    echo "❌ Error al configurar el webhook\n";
    echo "Mensaje: {$result['message']}\n\n";
    echo "Verifica que:\n";
    echo "1. El TELEGRAM_BOT_TOKEN esté configurado correctamente\n";
    echo "2. La URL del webhook sea accesible desde internet\n";
    echo "3. La URL use HTTPS (requerido por Telegram)\n";
    exit(1);
}

echo "\n═══════════════════════════════════════\n";
echo "  ℹ️  Comandos disponibles\n";
echo "═══════════════════════════════════════\n\n";
echo "php artisan telegram:manage setup  - Configurar webhook\n";
echo "php artisan telegram:manage info   - Ver información del webhook\n";
echo "php artisan telegram:manage delete - Eliminar webhook\n";
echo "php artisan telegram:manage me     - Ver información del bot\n";
echo "php artisan telegram:manage link   - Generar código de vinculación\n";
echo "\n";
