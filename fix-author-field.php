<?php
/**
 * Fix Script: Add author_name column to posts table
 * Run this on production: php fix-author-field.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "🔧 Iniciando fix para campo author_name...\n\n";

try {
    // Step 1: Check if column exists
    echo "1️⃣ Verificando si la columna existe...\n";
    $exists = Schema::hasColumn('posts', 'author_name');

    if ($exists) {
        echo "   ✓ La columna 'author_name' ya existe\n\n";
    } else {
        echo "   ⚠ La columna 'author_name' NO existe\n";
        echo "   → Agregando columna...\n";

        // Add the column using Schema Builder
        Schema::table('posts', function ($table) {
            $table->string('author_name')->nullable()->after('created_by');
        });

        echo "   ✓ Columna 'author_name' agregada exitosamente\n\n";
    }

    // Step 2: Verify column was added
    echo "2️⃣ Verificando estructura de la tabla...\n";
    $columns = DB::select("SHOW COLUMNS FROM posts WHERE Field = 'author_name'");

    if (!empty($columns)) {
        $column = $columns[0];
        echo "   ✓ Columna encontrada:\n";
        echo "     - Campo: {$column->Field}\n";
        echo "     - Tipo: {$column->Type}\n";
        echo "     - Nulo: {$column->Null}\n\n";
    } else {
        echo "   ✗ Error: La columna no se pudo verificar\n\n";
        exit(1);
    }

    // Step 3: Test that we can query the column
    echo "3️⃣ Probando consulta a la columna...\n";
    $test = DB::table('posts')->select('id', 'title', 'author_name')->first();
    echo "   ✓ Consulta exitosa\n\n";

    echo "✅ ¡Fix completado exitosamente!\n";
    echo "\nPróximos pasos:\n";
    echo "1. Ejecuta: php artisan view:clear\n";
    echo "2. Ejecuta: php artisan config:clear\n";
    echo "3. Ejecuta: php artisan cache:clear\n";
    echo "4. Prueba crear un artículo en: https://dataflow.guaraniappstore.com/admin/blog/create\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
