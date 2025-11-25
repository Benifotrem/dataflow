<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$telegram = $app->make(\App\Services\TelegramService::class);
$webhookUrl = env('APP_URL') . '/api/telegram/webhook';
echo "🔧 Configurando webhook: $webhookUrl\n";
$result = $telegram->setWebhook($webhookUrl);
if ($result && $result['ok']) {
    echo "✅ Webhook configurado\n\n";
    $info = $telegram->getWebhookInfo();
    echo json_encode($info, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ Error\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
}
