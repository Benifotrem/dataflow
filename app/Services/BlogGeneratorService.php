<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogGeneratorService
{
    protected $trendsService;
    protected $openRouterService;
    protected $pexelsService;

    public function __construct(
        TrendingTopicsService $trendsService,
        OpenRouterService $openRouterService,
        PexelsService $pexelsService
    ) {
        $this->trendsService = $trendsService;
        $this->openRouterService = $openRouterService;
        $this->pexelsService = $pexelsService;
    }

    /**
     * Generar un artículo completo de blog
     */
    public function generateArticle(?string $country = null, ?string $topic = null, ?int $userId = null): Post
    {
        try {
            // 1. Seleccionar país si no se especificó
            if (!$country) {
                $countries = $this->trendsService->getAvailableCountries();
                $country = $countries[array_rand($countries)];
            }

            // 2. Seleccionar tema trending si no se especificó
            if (!$topic) {
                $topic = $this->trendsService->getRandomTopic($country);
            }

            Log::info("Generando artículo", ['topic' => $topic, 'country' => $country]);

            // 3. Obtener palabras clave sugeridas
            $keywords = $this->trendsService->getKeywordsForTopic($topic);

            // 4. Generar contenido con IA (puede tardar mucho, no usar transacción aquí)
            Log::info("Generando contenido con OpenRouter...");
            $article = $this->openRouterService->generateArticle($topic, $country, $keywords);

            // 5. Buscar y descargar imagen
            Log::info("Buscando imagen en Pexels...");
            $image = $this->pexelsService->fetchImageForArticle($topic, $keywords);

            // 6. Reconectar DB por si la conexión se cerró durante las llamadas a APIs
            DB::reconnect();

            // 7. Crear el post en la base de datos (ahora sí usamos transacción)
            DB::beginTransaction();
            try {
                $post = Post::create([
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'content' => $article['content'],
                    'featured_image' => $image ? $image['path'] : null,
                    'image_credits' => $image ? $image['credits'] : null,
                    'keywords' => $keywords,
                    'country' => $country,
                    'status' => 'draft', // Crear como borrador para revisión
                    'created_by' => $userId,
                    'meta_data' => [
                        'topic_source' => 'trending_topics',
                        'original_topic' => $topic,
                        'generation_date' => now()->toISOString(),
                        'model_used' => $this->openRouterService->model ?? 'deepseek/deepseek-chat',
                        'image_source' => $image ? 'pexels' : null,
                        'photographer' => $image['photographer'] ?? null,
                        'photographer_url' => $image['photographer_url'] ?? null,
                    ],
                ]);

                DB::commit();

                Log::info("Artículo generado exitosamente", ['post_id' => $post->id]);

                return $post;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Error generando artículo: " . $e->getMessage(), [
                'exception' => $e,
                'topic' => $topic ?? 'unknown',
                'country' => $country ?? 'unknown',
            ]);
            throw $e;
        }
    }

    /**
     * Generar múltiples artículos
     */
    public function generateMultipleArticles(int $count, ?string $country = null, ?int $userId = null): array
    {
        $posts = [];
        $errors = [];

        for ($i = 0; $i < $count; $i++) {
            try {
                $posts[] = $this->generateArticle($country, null, $userId);

                // Pequeña pausa entre generaciones para no saturar las APIs
                if ($i < $count - 1) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $i,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => count($posts),
            'errors' => count($errors),
            'posts' => $posts,
            'error_details' => $errors,
        ];
    }

    /**
     * Publicar un post
     */
    public function publishPost(Post $post): bool
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return true;
    }

    /**
     * Archivar un post
     */
    public function archivePost(Post $post): bool
    {
        $post->update([
            'status' => 'archived',
        ]);

        return true;
    }
}
