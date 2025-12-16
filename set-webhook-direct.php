<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$token = config('telegram.bots.mybot.token');
$webhookUrl = 'https://dataflow.guaraniappstore.com/api/telegram/webhook';

echo "Token: " . substr($token, 0, 10) . "...\n";
echo "URL: {$webhookUrl}\n\n";

$apiUrl = "https://api.telegram.org/bot{$token}/setWebhook";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['url' => $webhookUrl]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: {$error}\n";
} else {
    echo "HTTP Code: {$httpCode}\n";
    echo "Response:\n";
    $data = json_decode($response, true);
    print_r($data);

    if ($data['ok'] ?? false) {
        echo "\n✅ Webhook configurado exitosamente\n";
    } else {
        echo "\n❌ Error: " . ($data['description'] ?? 'Desconocido') . "\n";
    }
}
