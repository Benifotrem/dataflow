<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\BlogGeneratorService;
use App\Services\TrendingTopicsService;
use Illuminate\Console\Command;

class GenerateBlogPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:generate
                            {--country= : Código de país específico (ej: py, ar, mx)}
                            {--topic= : Tema específico para el artículo}
                            {--sequence : Usar secuencia automática de países}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar un artículo de blog automáticamente con IA';

    protected $blogGenerator;
    protected $trendsService;

    public function __construct(BlogGeneratorService $blogGenerator, TrendingTopicsService $trendsService)
    {
        parent::__construct();
        $this->blogGenerator = $blogGenerator;
        $this->trendsService = $trendsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verificar si la generación automática está activada
        $autoGenerationEnabled = Setting::get('blog_auto_generation_enabled', false);

        if (!$autoGenerationEnabled && $this->option('sequence')) {
            $this->error('La generación automática está desactivada. Actívala desde el panel admin.');
            return 1;
        }

        $this->info('🚀 Iniciando generación de artículo...');

        // Determinar el país
        $country = $this->option('country');
        $topic = $this->option('topic');

        if ($this->option('sequence')) {
            // Usar secuencia automática
            $lastCountry = Setting::get('blog_last_generated_country');
            $country = $this->trendsService->getNextCountryInSequence($lastCountry);
            $countryName = $this->trendsService->getCountryNames()[$country] ?? $country;
            $this->info("📍 País en secuencia: {$countryName} ({$country})");
        } elseif ($country) {
            $countryName = $this->trendsService->getCountryNames()[$country] ?? $country;
            $this->info("📍 País seleccionado: {$countryName} ({$country})");
        } else {
            $this->info("📍 País: Aleatorio");
        }

        if ($topic) {
            $this->info("📝 Tema: {$topic}");
        } else {
            $this->info("📝 Tema: Trending automático");
        }

        try {
            // Generar el artículo
            $post = $this->blogGenerator->generateArticle($country, $topic, null);

            // Verificar si debe auto-publicarse
            $autoPublish = Setting::get('blog_auto_publish', false);
            if ($autoPublish && $this->option('sequence')) {
                $post->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);
                $this->info('📢 Artículo publicado automáticamente');
            }

            $this->newLine();
            $this->info('✅ Artículo generado exitosamente!');
            $this->newLine();
            $this->line("Título: <fg=green>{$post->title}</>");
            $this->line("Slug: <fg=cyan>{$post->slug}</>");
            $this->line("País: <fg=yellow>{$post->country}</>");
            $this->line("Estado: <fg=blue>{$post->status}</>");
            $this->line("ID: <fg=magenta>{$post->id}</>");
            $this->newLine();

            // Si se usó secuencia, guardar el país generado
            if ($this->option('sequence') || (!$this->option('country') && !$topic)) {
                Setting::set('blog_last_generated_country', $country, 'string', 'blog');
                $this->info("💾 País guardado para próxima generación en secuencia");
            }

            // Incrementar contador
            $totalGenerated = Setting::get('blog_total_generated', 0);
            Setting::set('blog_total_generated', $totalGenerated + 1, 'integer', 'blog');

            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error al generar artículo:');
            $this->error($e->getMessage());
            $this->newLine();

            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }

            return 1;
        }
    }
}
