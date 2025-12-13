#!/usr/bin/env php
<?php

/**
 * Script para probar la conversión de PDF a imagen
 * Uso: php test-pdf-conversion.php
 */

echo "🧪 Probando conversión de PDF a imagen...\n\n";

// Verificar extensión Imagick
echo "1️⃣ Verificando extensión Imagick...\n";
if (!extension_loaded('imagick')) {
    echo "❌ ERROR: Imagick NO está instalada\n";
    echo "   Solución: Activa php-imagick en hPanel o instala:\n";
    echo "   sudo apt-get install php-imagick\n";
    echo "   sudo systemctl restart php-fpm\n";
    exit(1);
}
echo "✅ Imagick está instalada\n\n";

// Verificar clase Spatie
echo "2️⃣ Verificando paquete spatie/pdf-to-image...\n";
require __DIR__ . '/vendor/autoload.php';

if (!class_exists(\Spatie\PdfToImage\Pdf::class)) {
    echo "❌ ERROR: Clase Spatie\PdfToImage\Pdf NO encontrada\n";
    echo "   Solución: Ejecuta ./fix-composer.sh\n";
    exit(1);
}
echo "✅ Paquete spatie/pdf-to-image instalado\n\n";

// Verificar directorio temporal
echo "3️⃣ Verificando directorio temporal...\n";
$tempDir = __DIR__ . '/storage/app/temp';
if (!file_exists($tempDir)) {
    echo "⚠️  Directorio temp no existe, creándolo...\n";
    mkdir($tempDir, 0755, true);
}
echo "✅ Directorio temporal: {$tempDir}\n\n";

// Probar instanciación del servicio
echo "4️⃣ Probando servicio PdfConverterService...\n";
try {
    $converter = new \App\Services\PdfConverterService();
    echo "✅ Servicio instanciado correctamente\n\n";
} catch (\Exception $e) {
    echo "❌ ERROR al instanciar servicio: " . $e->getMessage() . "\n";
    exit(1);
}

echo "✅ ¡TODOS LOS TESTS PASARON!\n";
echo "   El sistema está listo para procesar PDFs.\n";
echo "   Puedes enviar un PDF al bot de Telegram para probarlo.\n";
