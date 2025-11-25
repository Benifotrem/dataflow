<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Landing page principal
     */
    public function index()
    {
        $seo = [
            'title' => 'Dataflow - Automatización Contable con Inteligencia Artificial',
            'description' => 'Simplifica tu contabilidad con IA. Procesamiento automático de facturas, conciliación bancaria y cumplimiento fiscal para España e Hispanoamérica. Desde $19.99/mes.',
            'keywords' => 'contabilidad automática, software contable, automatización fiscal, OCR facturas, contabilidad España, contabilidad Latinoamérica',
            'image' => asset('images/og-image.jpg'),
        ];

        // Usar la landing completa
        return view('landing.index', compact('seo'));
    }

    /**
     * Página de precios
     */
    public function pricing()
    {
        $seo = [
            'title' => 'Precios - Dataflow',
            'description' => 'Planes flexibles para PyMEs y despachos contables. Básico desde $9.99/mes, Avanzado desde $29.99/mes. Prueba gratis 14 días.',
            'keywords' => 'precios contabilidad, software contable precio, plan contable',
        ];

        return view('landing.pricing', compact('seo'));
    }

    /**
     * FAQ - Preguntas Frecuentes
     */
    public function faq()
    {
        $seo = [
            'title' => 'Preguntas Frecuentes - Dataflow',
            'description' => 'Resuelve tus dudas sobre Dataflow. Respuestas sobre funcionalidad, seguridad, precios y más.',
            'keywords' => 'FAQ dataflow, preguntas contabilidad, ayuda software contable',
        ];

        return view('landing.faq', compact('seo'));
    }

    /**
     * Blog - Lista de artículos
     */
    public function blog()
    {
        $posts = \App\Models\Post::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $seo = [
            'title' => 'Blog - Dataflow',
            'description' => 'Artículos, guías y novedades sobre contabilidad, fiscalidad y automatización con IA.',
            'keywords' => 'blog contabilidad, guías fiscales, automatización contable, noticias fiscales',
        ];

        return view('landing.blog', compact('seo', 'posts'));
    }

    /**
     * Mostrar artículo individual
     */
    public function blogShow(string $slug)
    {
        $post = \App\Models\Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Incrementar vistas
        $post->incrementViews();

        // Posts relacionados (mismo país)
        $relatedPosts = \App\Models\Post::published()
            ->where('id', '!=', $post->id)
            ->where('country', $post->country)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $seo = [
            'title' => $post->title . ' - Dataflow Blog',
            'description' => $post->excerpt,
            'keywords' => implode(', ', $post->keywords ?? []),
            'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/og-image.jpg'),
        ];

        return view('landing.blog-show', compact('post', 'relatedPosts', 'seo'));
    }

    /**
     * Términos y Condiciones
     */
    public function terms()
    {
        $seo = [
            'title' => 'Términos y Condiciones - Dataflow',
            'description' => 'Términos y condiciones de uso de la plataforma Dataflow.',
            'keywords' => 'términos y condiciones, términos de servicio',
        ];

        return view('landing.terms', compact('seo'));
    }

    /**
     * Política de Privacidad
     */
    public function privacy()
    {
        $seo = [
            'title' => 'Política de Privacidad - Dataflow',
            'description' => 'Cómo protegemos y tratamos tus datos en Dataflow. Cumplimiento GDPR y normativas de protección de datos.',
            'keywords' => 'política de privacidad, protección de datos, GDPR',
        ];

        return view('landing.privacy', compact('seo'));
    }

    /**
     * Sitemap XML
     */
    public function sitemap()
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('pricing'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => route('faq'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('terms'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => route('privacy'), 'priority' => '0.5', 'changefreq' => 'yearly'],
        ];

        return response()->view('landing.sitemap', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }
}
