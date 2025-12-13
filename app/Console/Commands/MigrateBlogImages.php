<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateBlogImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:migrate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar imágenes de blog de /uploads/blog a /public/uploads/blog';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de imágenes de blog...');

        // Rutas
        $oldPath = base_path('uploads/blog');
        $newPath = public_path('uploads/blog');

        // Verificar si existe la carpeta antigua
        if (!File::exists($oldPath)) {
            $this->warn("No existe la carpeta antigua: {$oldPath}");
            $this->info('No hay imágenes para migrar.');
            return 0;
        }

        // Crear carpeta nueva si no existe
        if (!File::exists($newPath)) {
            File::makeDirectory($newPath, 0755, true);
            $this->info("Creado directorio: {$newPath}");
        }

        // Obtener todas las imágenes
        $images = File::files($oldPath);

        if (empty($images)) {
            $this->info('No se encontraron imágenes para migrar.');
            return 0;
        }

        $this->info(sprintf('Encontradas %d imágenes para migrar.', count($images)));

        $moved = 0;
        $errors = 0;

        foreach ($images as $image) {
            $filename = $image->getFilename();
            $destination = "{$newPath}/{$filename}";

            try {
                // Si ya existe en el destino, saltar
                if (File::exists($destination)) {
                    $this->line("⏩ Ya existe: {$filename}");
                    continue;
                }

                // Copiar (no mover) para mantener backup
                File::copy($image->getPathname(), $destination);
                $moved++;
                $this->info("✅ Migrada: {$filename}");

            } catch (\Exception $e) {
                $errors++;
                $this->error("❌ Error al migrar {$filename}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("=== RESUMEN ===");
        $this->info("Total de imágenes: " . count($images));
        $this->info("Migradas exitosamente: {$moved}");
        if ($errors > 0) {
            $this->error("Errores: {$errors}");
        }

        $this->newLine();
        $this->warn("IMPORTANTE: Las imágenes originales se mantuvieron en {$oldPath}");
        $this->warn("Una vez confirmado que todo funciona, puedes eliminar esa carpeta manualmente.");

        return 0;
    }
}
