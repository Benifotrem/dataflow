<?php

/**
 * SCRIPT DE EMERGENCIA - DETENER BUCLE
 * Ejecutar: php stop-loop-emergency.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🚨 MODO EMERGENCIA - DETENIENDO BUCLE\n\n";

try {
    // 1. Eliminar todos los jobs pendientes
    echo "1. Limpiando cola de jobs...\n";
    try {
        $deleted = DB::table('jobs')->delete();
        echo "   ✅ Eliminados {$deleted} jobs pendientes\n";
    } catch (\Exception $e) {
        echo "   ⚠️  No se pudo limpiar jobs: " . $e->getMessage() . "\n";
    }

    // 2. Limpiar jobs fallidos
    echo "2. Limpiando jobs fallidos...\n";
    try {
        $deletedFailed = DB::table('failed_jobs')->delete();
        echo "   ✅ Eliminados {$deletedFailed} jobs fallidos\n";
    } catch (\Exception $e) {
        echo "   ⚠️  No se pudo limpiar failed_jobs: " . $e->getMessage() . "\n";
    }

    // 3. Limpiar caché
    echo "3. Limpiando cachés...\n";
    Artisan::call('cache:clear');
    echo "   ✅ Cache cleared\n";

    Artisan::call('config:clear');
    echo "   ✅ Config cleared\n";

    Artisan::call('route:clear');
    echo "   ✅ Routes cleared\n";

    Artisan::call('view:clear');
    echo "   ✅ Views cleared\n";

    // 4. Log de emergencia
    Log::emergency('🚨 BUCLE DETENIDO MANUALMENTE - Script de emergencia ejecutado');

    echo "\n✅ BUCLE DETENIDO\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "El sistema está ahora en estado limpio.\n";
    echo "Todos los jobs pendientes han sido eliminados.\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR CRÍTICO:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
