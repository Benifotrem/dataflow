<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNÓSTICO DOCUMENTO 757 ===\n\n";

try {
    // 1. Verificar que el documento existe
    echo "1. Buscando documento 757...\n";
    $document = \App\Models\Document::find(757);

    if (!$document) {
        echo "   ❌ ERROR: Documento 757 NO EXISTE en la base de datos\n";
        exit(1);
    }

    echo "   ✓ Documento encontrado: {$document->original_filename}\n";
    echo "   - ID: {$document->id}\n";
    echo "   - Tenant ID: {$document->tenant_id}\n";
    echo "   - Entity ID: {$document->entity_id}\n";
    echo "   - User ID: {$document->user_id}\n";
    echo "   - OCR Status: {$document->ocr_status}\n";
    echo "   - Deleted at: " . ($document->deleted_at ?? 'NULL') . "\n\n";

    // 2. Verificar relación entity
    echo "2. Verificando relación entity...\n";
    try {
        $entity = $document->entity;
        if ($entity) {
            echo "   ✓ Entity encontrada: {$entity->name} (ID: {$entity->id})\n\n";
        } else {
            echo "   ⚠️ WARNING: Entity es NULL (entity_id={$document->entity_id})\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERROR al cargar entity: " . $e->getMessage() . "\n\n";
    }

    // 3. Verificar relación user
    echo "3. Verificando relación user...\n";
    try {
        $user = $document->user;
        if ($user) {
            echo "   ✓ User encontrado: {$user->name} (ID: {$user->id})\n\n";
        } else {
            echo "   ⚠️ WARNING: User es NULL (user_id={$document->user_id})\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERROR al cargar user: " . $e->getMessage() . "\n\n";
    }

    // 4. Verificar tenant
    echo "4. Verificando relación tenant...\n";
    try {
        $tenant = $document->tenant;
        if ($tenant) {
            echo "   ✓ Tenant encontrado: {$tenant->name} (ID: {$tenant->id})\n\n";
        } else {
            echo "   ⚠️ WARNING: Tenant es NULL\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERROR al cargar tenant: " . $e->getMessage() . "\n\n";
    }

    // 5. Intentar cargar la vista
    echo "5. Verificando vista...\n";
    try {
        $viewPath = resource_path('views/dashboard/documents/show.blade.php');
        if (file_exists($viewPath)) {
            echo "   ✓ Vista existe: $viewPath\n\n";
        } else {
            echo "   ❌ ERROR: Vista NO existe: $viewPath\n\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
    }

    // 6. Simular eager loading como hace el controlador
    echo "6. Simulando eager loading...\n";
    try {
        $document->load('entity', 'user');
        echo "   ✓ Eager loading exitoso\n\n";
    } catch (\Exception $e) {
        echo "   ❌ ERROR en eager loading: " . $e->getMessage() . "\n";
        echo "   Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    }

    echo "\n=== DIAGNÓSTICO COMPLETADO ===\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR FATAL:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
