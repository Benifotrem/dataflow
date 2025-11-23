<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PexelsService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.pexels.com/v1';

    public function __construct()
    {
        $this->apiKey = Setting::get('pexels_api_key');
    }

    /**
     * Buscar imágenes relacionadas con el tema
     */
    public function searchImages(string $query, int $perPage = 15)
    {
        if (!$this->apiKey) {
            throw new \Exception('Pexels API key no configurada');
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get("{$this->baseUrl}/search", [
            'query' => $query,
            'per_page' => $perPage,
            'orientation' => 'landscape',
        ]);

        if ($response->failed()) {
            throw new \Exception('Error al buscar imágenes en Pexels: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Obtener una imagen aleatoria para un tema
     */
    public function getRandomImage(string $query)
    {
        $result = $this->searchImages($query, 15);

        if (empty($result['photos'])) {
            // Si no hay resultados, intentar con un término genérico
            $result = $this->searchImages('business finance', 15);
        }

        if (empty($result['photos'])) {
            return null;
        }

        // Seleccionar una imagen aleatoria
        $photo = $result['photos'][array_rand($result['photos'])];

        return [
            'url' => $photo['src']['large2x'] ?? $photo['src']['large'] ?? $photo['src']['original'],
            'photographer' => $photo['photographer'],
            'photographer_url' => $photo['photographer_url'],
            'pexels_url' => $photo['url'],
        ];
    }

    /**
     * Descargar y guardar una imagen
     */
    public function downloadAndSave(string $imageUrl, string $filename = null): string
    {
        if (!$filename) {
            $filename = Str::random(40) . '.jpg';
        }

        // Descargar la imagen
        $imageContent = Http::get($imageUrl)->body();

        // Guardar en storage/app/public/blog
        $path = "blog/{$filename}";
        Storage::disk('public')->put($path, $imageContent);

        return $path;
    }

    /**
     * Buscar y descargar imagen para un artículo
     */
    public function fetchImageForArticle(string $topic, array $keywords = []): ?array
    {
        try {
            // Construir query de búsqueda optimizada para contexto fiscal/contable
            $searchQuery = $this->buildAccountingSearchQuery($topic, $keywords);

            // Intentar búsqueda principal
            $image = $this->getRandomImage($searchQuery);

            // Si no hay resultados, probar con términos alternativos
            if (!$image) {
                $fallbackQueries = $this->getFallbackSearchQueries($topic);

                foreach ($fallbackQueries as $fallbackQuery) {
                    $image = $this->getRandomImage($fallbackQuery);
                    if ($image) {
                        break;
                    }
                }
            }

            if (!$image) {
                return null;
            }

            // Descargar y guardar
            $localPath = $this->downloadAndSave($image['url']);

            return [
                'path' => $localPath,
                'url' => Storage::url($localPath),
                'credits' => "Foto por {$image['photographer']} en Pexels",
                'photographer' => $image['photographer'],
                'photographer_url' => $image['photographer_url'],
                'pexels_url' => $image['pexels_url'],
            ];

        } catch (\Exception $e) {
            \Log::error('Error al obtener imagen de Pexels: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Construir query de búsqueda optimizada para temas fiscales/contables
     */
    protected function buildAccountingSearchQuery(string $topic, array $keywords = []): string
    {
        // Mapeo de términos fiscales españoles a términos de búsqueda en inglés
        $fiscalTerms = [
            'impuesto' => 'tax',
            'impuestos' => 'taxes',
            'iva' => 'vat tax',
            'irpf' => 'income tax',
            'declaración' => 'tax return',
            'renta' => 'income tax',
            'autónomo' => 'self employed',
            'autónomos' => 'freelancer',
            'factura' => 'invoice',
            'facturas' => 'invoices',
            'contabilidad' => 'accounting',
            'fiscal' => 'tax',
            'tributario' => 'tax',
            'deducciones' => 'deductions',
            'retención' => 'withholding',
            'nómina' => 'payroll',
            'empresas' => 'business',
            'empresa' => 'business',
            'pyme' => 'small business',
            'sociedades' => 'corporate',
            'beneficios' => 'profit',
            'pérdidas' => 'loss',
            'balance' => 'balance sheet',
            'cierre' => 'year end',
        ];

        $topicLower = mb_strtolower($topic);
        $searchTerms = [];

        // Extraer términos fiscales del tema
        foreach ($fiscalTerms as $spanish => $english) {
            if (str_contains($topicLower, $spanish)) {
                $searchTerms[] = $english;
            }
        }

        // Si no se encontraron términos específicos, usar términos genéricos de negocios
        if (empty($searchTerms)) {
            $searchTerms = ['accounting', 'business'];
        }

        // Agregar contexto de oficina/negocios
        $contextTerms = ['office', 'professional', 'business'];
        $searchTerms[] = $contextTerms[array_rand($contextTerms)];

        // Limitar a 3 términos para mejor precisión
        $searchTerms = array_slice(array_unique($searchTerms), 0, 3);

        return implode(' ', $searchTerms);
    }

    /**
     * Obtener queries de búsqueda alternativas si falla la principal
     */
    protected function getFallbackSearchQueries(string $topic): array
    {
        return [
            'accounting office professional',
            'tax documents business',
            'financial planning office',
            'business meeting finance',
            'accountant calculator office',
            'tax form paperwork',
            'financial advisor business',
            'business finance professional',
            'accounting calculator laptop',
            'tax planning office',
        ];
    }
}
