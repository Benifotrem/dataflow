<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1';
    protected $model;
    protected $minWords;
    protected $maxWords;

    public function __construct()
    {
        $this->apiKey = Setting::get('openrouter_api_key');
        $this->model = Setting::get('blog_generation_model', 'deepseek/deepseek-chat');
        $this->minWords = Setting::get('blog_min_words', 1200);
        $this->maxWords = Setting::get('blog_max_words', 1800);
    }

    /**
     * Generar un artículo completo sobre un tema
     */
    public function generateArticle(string $topic, string $country = 'py', array $keywords = []): array
    {
        if (!$this->apiKey) {
            throw new \Exception('OpenRouter API key no configurada');
        }

        // Obtener nombre del país y autor
        $countryName = $this->getCountryName($country);
        $authorName = Setting::get('blog_author_name', 'César Ruzafa');

        // Construir el prompt
        $prompt = $this->buildPrompt($topic, $countryName, $authorName, $keywords);

        // Llamar a la API
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(120) // 2 minutos de timeout
        ->post("{$this->baseUrl}/chat/completions", [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Eres {$authorName}, un experto contador y asesor fiscal con años de experiencia en Latinoamérica y España. Escribes artículos informativos, precisos y útiles sobre temas de contabilidad, fiscalidad y finanzas para PyMEs y profesionales contables. Escribes desde tu experiencia profesional directa, nunca mencionas que eres una IA o que el contenido es generado automáticamente. Escribes en primera persona cuando es apropiado y compartes tus conocimientos como profesional del sector.",
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ]);

        if ($response->failed()) {
            throw new \Exception('Error al generar artículo con OpenRouter: ' . $response->body());
        }

        $result = $response->json();

        $content = $result['choices'][0]['message']['content'] ?? '';

        if (empty($content)) {
            throw new \Exception('La API no devolvió contenido');
        }

        // Parsear el contenido para extraer título, excerpt y contenido
        return $this->parseGeneratedContent($content, $topic);
    }

    /**
     * Construir el prompt para generar el artículo
     */
    protected function buildPrompt(string $topic, string $country, string $authorName, array $keywords): string
    {
        $keywordsStr = !empty($keywords) ? implode(', ', $keywords) : '';

        return <<<PROMPT
Escribe un artículo de blog profesional basado en tu experiencia como experto en contabilidad y fiscalidad sobre el siguiente tema para {$country}:

TEMA: {$topic}

REQUISITOS OBLIGATORIOS:
- Extensión: Entre {$this->minWords} y {$this->maxWords} palabras
- Audiencia: PyMEs, autónomos y profesionales contables de {$country}
- Tono: Profesional pero accesible, educativo, escrito desde tu experiencia directa
- NO menciones IA, generación automática, ni herramientas de escritura automática
- Escribe como {$authorName}, compartiendo tu conocimiento y experiencia profesional
- Estructura requerida:
  * Título atractivo y SEO-optimizado (máximo 60 caracteres)
  * Extracto/Resumen cautivador (150-160 caracteres)
  * Introducción que enganche al lector
  * 3-5 secciones principales con subtítulos descriptivos
  * Ejemplos prácticos basados en casos reales
  * Conclusión con consejos accionables
  * Llamada a la acción relacionada con Contaplus (software de contabilidad)

PALABRAS CLAVE A INCLUIR NATURALMENTE: {$keywordsStr}

FORMATO DE RESPUESTA:
Devuelve el artículo en el siguiente formato exacto:

TÍTULO: [Tu título aquí]

EXTRACTO: [Tu extracto aquí]

CONTENIDO:
[El contenido completo del artículo en formato HTML con tags <h2>, <h3>, <p>, <ul>, <ol>, <strong>, <em> según corresponda]

IMPORTANTE:
- Usa información actualizada y precisa sobre la legislación fiscal de {$country}
- Incluye ejemplos prácticos y casos de uso reales
- Menciona naturalmente cómo Contaplus puede ayudar a automatizar/simplificar los procesos mencionados
- Usa HTML semántico para el contenido
- NO incluyas el tag <h1> en el contenido (solo h2, h3, h4)
- Escribe desde tu perspectiva como profesional experimentado
- NUNCA menciones que este contenido es generado por IA o herramientas automáticas
PROMPT;
    }

    /**
     * Parsear el contenido generado
     */
    protected function parseGeneratedContent(string $content, string $fallbackTopic): array
    {
        $title = '';
        $excerpt = '';
        $articleContent = '';

        // Extraer título
        if (preg_match('/TÍTULO:\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $title = trim($matches[1]);
            $content = preg_replace('/TÍTULO:\s*.+?(?:\n|$)/i', '', $content, 1);
        }

        // Extraer extracto
        if (preg_match('/EXTRACTO:\s*(.+?)(?:\n|$)/i', $content, $matches)) {
            $excerpt = trim($matches[1]);
            $content = preg_replace('/EXTRACTO:\s*.+?(?:\n|$)/i', '', $content, 1);
        }

        // Extraer contenido
        if (preg_match('/CONTENIDO:\s*(.+)/is', $content, $matches)) {
            $articleContent = trim($matches[1]);
        } else {
            // Si no encuentra el formato, usar todo el contenido restante
            $articleContent = trim($content);
        }

        // Fallbacks si algo falta
        if (empty($title)) {
            $title = $fallbackTopic;
        }

        if (empty($excerpt)) {
            // Generar excerpt del primer párrafo
            $excerpt = $this->generateExcerptFromContent($articleContent);
        }

        return [
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => $articleContent,
        ];
    }

    /**
     * Generar excerpt del contenido
     */
    protected function generateExcerptFromContent(string $content): string
    {
        // Remover tags HTML
        $text = strip_tags($content);

        // Limitar a 160 caracteres
        if (strlen($text) > 160) {
            $text = substr($text, 0, 157) . '...';
        }

        return $text;
    }

    /**
     * Obtener nombre del país por código
     */
    protected function getCountryName(string $code): string
    {
        $countries = [
            'ar' => 'Argentina',
            'bo' => 'Bolivia',
            'br' => 'Brasil',
            'cl' => 'Chile',
            'co' => 'Colombia',
            'cr' => 'Costa Rica',
            'cu' => 'Cuba',
            'do' => 'República Dominicana',
            'ec' => 'Ecuador',
            'es' => 'España',
            'gt' => 'Guatemala',
            'hn' => 'Honduras',
            'mx' => 'México',
            'ni' => 'Nicaragua',
            'pa' => 'Panamá',
            'pe' => 'Perú',
            'pr' => 'Puerto Rico',
            'py' => 'Paraguay',
            'sv' => 'El Salvador',
            'uy' => 'Uruguay',
            've' => 'Venezuela',
        ];

        return $countries[strtolower($code)] ?? 'Latinoamérica';
    }
}
