<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Telegram\Bot\Api;

$telegram = new Api(config('telegram.bots.mybot.token'));

try {
    echo "Verificando webhook de Telegram...\n\n";

    $webhookInfo = $telegram->getWebhookInfo();

    echo "URL del webhook: " . ($webhookInfo['url'] ?? 'No configurado') . "\n";
    echo "¿Tiene certificado personalizado?: " . ($webhookInfo['has_custom_certificate'] ?? false ? 'Sí' : 'No') . "\n";
    echo "Actualizaciones pendientes: " . ($webhookInfo['pending_update_count'] ?? 0) . "\n";

    if (isset($webhookInfo['last_error_date'])) {
        echo "\n⚠️ ÚLTIMO ERROR:\n";
        echo "Fecha: " . date('Y-m-d H:i:s', $webhookInfo['last_error_date']) . "\n";
        echo "Mensaje: " . ($webhookInfo['last_error_message'] ?? 'Desconocido') . "\n";
    }

    if (isset($webhookInfo['last_synchronization_error_date'])) {
        echo "\n⚠️ ERROR DE SINCRONIZACIÓN:\n";
        echo "Fecha: " . date('Y-m-d H:i:s', $webhookInfo['last_synchronization_error_date']) . "\n";
    }

    echo "\n✅ Webhook verificado correctamente\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
