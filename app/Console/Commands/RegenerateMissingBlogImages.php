<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\PexelsService;
use Illuminate\Console\Command;

class RegenerateMissingBlogImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:regenerate-images {--force : Regenerar todas las imágenes, incluso las existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerar imágenes faltantes de posts de blog usando Pexels';

    protected $pexelsService;

    public function __construct(PexelsService $pexelsService)
    {
        parent::__construct();
        $this->pexelsService = $pexelsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando posts con imágenes faltantes...');

        $posts = Post::published()->get();
        $missing = [];
        $existing = [];

        foreach ($posts as $post) {
            if ($post->featured_image) {
                $path = public_path('uploads/' . $post->featured_image);
                if (file_exists($path) && !$this->option('force')) {
                    $existing[] = $post;
                } else {
                    $missing[] = $post;
                }
            } else {
                $missing[] = $post;
            }
        }

        $this->newLine();
        $this->info(sprintf('Total de posts: %d', $posts->count()));
        $this->info(sprintf('Con imágenes OK: %d', count($existing)));
        $this->warn(sprintf('Con imágenes FALTANTES: %d', count($missing)));
        $this->newLine();

        if (empty($missing)) {
            $this->info('✅ No hay imágenes faltantes. Todo está correcto.');
            return 0;
        }

        if (!$this->confirm('¿Deseas regenerar las imágenes faltantes usando Pexels?', true)) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $this->newLine();
        $this->info('🎨 Regenerando imágenes...');
        $this->newLine();

        $regenerated = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($missing));
        $progressBar->start();

        foreach ($missing as $post) {
            try {
                // Buscar y descargar imagen
                $keywords = $post->keywords ?? [];
                $image = $this->pexelsService->fetchImageForArticle($post->title, $keywords);

                if ($image) {
                    // Actualizar post con nueva imagen
                    $post->update([
                        'featured_image' => $image['path'],
                        'image_credits' => $image['credits'],
                    ]);

                    // Actualizar meta_data
                    $metaData = $post->meta_data ?? [];
                    $metaData['image_regenerated_at'] = now()->toISOString();
                    $metaData['photographer'] = $image['photographer'];
                    $metaData['photographer_url'] = $image['photographer_url'];
                    $post->update(['meta_data' => $metaData]);

                    $regenerated++;
                } else {
                    $errors++;
                    $this->newLine();
                    $this->error("❌ No se pudo encontrar imagen para: {$post->title}");
                }

                $progressBar->advance();

                // Pausa de 1 segundo entre requests para no saturar la API
                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("❌ Error regenerando imagen para '{$post->title}': {$e->getMessage()}");
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('=== RESUMEN ===');
        $this->info("Posts procesados: " . count($missing));
        $this->info("✅ Imágenes regeneradas: {$regenerated}");
        if ($errors > 0) {
            $this->error("❌ Errores: {$errors}");
        }

        $this->newLine();
        $this->info('✨ Proceso completado.');

        return 0;
    }
}
