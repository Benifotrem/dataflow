<?php

$token = '8553737193:AAHcPl56x62OkO8BGofALcycssa7WdN2WQo';

// Obtener info del webhook usando file_get_contents
$url = "https://api.telegram.org/bot{$token}/getWebhookInfo";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

echo "Consultando webhook info...\n\n";

$response = @file_get_contents($url, false, $context);

if ($response) {
    $data = json_decode($response, true);

    if ($data['ok'] ?? false) {
        $info = $data['result'];
        echo "✅ Webhook encontrado:\n";
        echo "URL: " . ($info['url'] ?? 'No configurado') . "\n";
        echo "Actualizaciones pendientes: " . ($info['pending_update_count'] ?? 0) . "\n";

        if (!empty($info['url'])) {
            echo "\n🎉 ¡WEBHOOK CONFIGURADO CORRECTAMENTE!\n";
        }
    } else {
        echo "❌ Error: " . ($data['description'] ?? 'Desconocido') . "\n";
    }
} else {
    echo "❌ No se pudo conectar a Telegram API (firewall del servidor)\n";
}
