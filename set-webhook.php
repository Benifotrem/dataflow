<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Telegram\Bot\Api;

$telegram = new Api(config('telegram.bots.mybot.token'));

try {
    $webhookUrl = 'https://dataflow.guaraniappstore.com/api/telegram/webhook';

    echo "Configurando webhook...\n";
    echo "URL: {$webhookUrl}\n\n";

    $result = $telegram->setWebhook(['url' => $webhookUrl]);

    echo "Respuesta de Telegram:\n";
    print_r($result);
    echo "\n";

    if ($result) {
        echo "✅ Webhook configurado exitosamente\n\n";

        // Verificar
        $info = $telegram->getWebhookInfo();
        echo "URL configurada: " . $info['url'] . "\n";
        echo "Actualizaciones pendientes: " . ($info['pending_update_count'] ?? 0) . "\n";
    } else {
        echo "❌ No se pudo configurar el webhook\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
