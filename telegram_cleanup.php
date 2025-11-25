<?php

/**
 * Script de Limpieza del Bot de Telegram
 *
 * Este script elimina toda la configuración anterior del bot para empezar de cero.
 *
 * Uso: php telegram_cleanup.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "╔═══════════════════════════════════════╗\n";
echo "║  🧹 Limpieza del Bot de Telegram     ║\n";
echo "╔═══════════════════════════════════════╗\n\n";

// Verificar que el token esté configurado
$botToken = env('TELEGRAM_BOT_TOKEN');

if (!$botToken) {
    echo "⚠️  TELEGRAM_BOT_TOKEN no está configurado en .env\n\n";
    echo "Para limpiar el bot, necesitas:\n";
    echo "1. Editar el archivo .env\n";
    echo "2. Agregar tu TELEGRAM_BOT_TOKEN (obtenerlo de @BotFather)\n";
    echo "3. Ejecutar este script nuevamente\n\n";
    echo "Ejemplo en .env:\n";
    echo "TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz\n";
    exit(1);
}

try {
    $telegram = $app->make(\App\Services\TelegramService::class);

    // 1. Obtener información actual del webhook
    echo "📊 Paso 1: Verificando webhook actual...\n";
    $webhookInfo = $telegram->getWebhookInfo();

    if ($webhookInfo['success'] && isset($webhookInfo['data']['url'])) {
        $currentUrl = $webhookInfo['data']['url'];

        if (empty($currentUrl)) {
            echo "   ✅ No hay webhook configurado\n\n";
        } else {
            echo "   📍 Webhook actual: {$currentUrl}\n";
            echo "   📬 Actualizaciones pendientes: " . ($webhookInfo['data']['pending_update_count'] ?? 0) . "\n";

            if (isset($webhookInfo['data']['last_error_message'])) {
                echo "   ⚠️  Último error: {$webhookInfo['data']['last_error_message']}\n";
            }
            echo "\n";
        }
    }

    // 2. Eliminar webhook
    echo "🗑️  Paso 2: Eliminando webhook...\n";
    $deleteResult = $telegram->deleteWebhook();

    if ($deleteResult['success']) {
        echo "   ✅ Webhook eliminado correctamente\n\n";
    } else {
        echo "   ❌ Error al eliminar webhook: {$deleteResult['message']}\n\n";
    }

    // 3. Obtener información del bot
    echo "🤖 Paso 3: Información del bot...\n";
    $botInfo = $telegram->getMe();

    if ($botInfo) {
        echo "   📱 ID: {$botInfo['id']}\n";
        echo "   👤 Nombre: {$botInfo['first_name']}\n";
        echo "   🔗 Username: @{$botInfo['username']}\n\n";
    }

    // 4. Verificar que el webhook fue eliminado
    echo "✅ Paso 4: Verificando limpieza...\n";
    $finalCheck = $telegram->getWebhookInfo();

    if ($finalCheck['success']) {
        $finalUrl = $finalCheck['data']['url'] ?? '';

        if (empty($finalUrl)) {
            echo "   ✅ Webhook confirmado como eliminado\n";
            echo "   ✅ Bot limpio y listo para nueva configuración\n\n";
        } else {
            echo "   ⚠️  Todavía hay un webhook: {$finalUrl}\n";
            echo "   Intenta ejecutar el script nuevamente\n\n";
        }
    }

    echo "╔═══════════════════════════════════════╗\n";
    echo "║  ✨ Limpieza Completada              ║\n";
    echo "╔═══════════════════════════════════════╗\n\n";

    echo "📝 Próximos pasos:\n";
    echo "1. Configura las variables en .env:\n";
    echo "   - TELEGRAM_BOT_TOKEN (ya configurado)\n";
    echo "   - TELEGRAM_BOT_USERNAME (ej: DataflowBot)\n";
    echo "   - PAGOPAR_PUBLIC_KEY\n";
    echo "   - PAGOPAR_PRIVATE_KEY\n\n";

    echo "2. Ejecuta las migraciones:\n";
    echo "   php artisan migrate\n\n";

    echo "3. Configura el nuevo webhook:\n";
    echo "   php artisan telegram:manage setup\n\n";

} catch (\Exception $e) {
    echo "❌ Error durante la limpieza:\n";
    echo "   {$e->getMessage()}\n\n";

    if (strpos($e->getMessage(), 'Unauthorized') !== false) {
        echo "⚠️  El token parece ser inválido.\n";
        echo "   Verifica que el TELEGRAM_BOT_TOKEN en .env sea correcto.\n";
        echo "   Obtén un nuevo token de @BotFather si es necesario.\n\n";
    }

    exit(1);
}
