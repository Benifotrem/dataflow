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
            'title' => 'Contaplus - Automatización Contable con Inteligencia Artificial',
            'description' => 'Simplifica tu contabilidad con IA. Procesamiento automático de facturas, conciliación bancaria y cumplimiento fiscal para España e Hispanoamérica. Desde $19.99/mes.',
            'keywords' => 'contabilidad automática, software contable, automatización fiscal, OCR facturas, contabilidad España, contabilidad Latinoamérica',
            'image' => asset('images/og-image.jpg'),
        ];

        return view('landing.index', compact('seo'));
    }

    /**
     * Página de precios
     */
    public function pricing()
    {
        $seo = [
            'title' => 'Precios - Contaplus',
            'description' => 'Planes flexibles para PyMEs y despachos contables. Básico desde $19.99/mes, Avanzado desde $49.99/mes. Prueba gratis 14 días.',
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
            'title' => 'Preguntas Frecuentes - Contaplus',
            'description' => 'Resuelve tus dudas sobre Contaplus. Respuestas sobre funcionalidad, seguridad, precios y más.',
            'keywords' => 'FAQ contaplus, preguntas contabilidad, ayuda software contable',
        ];

        return view('landing.faq', compact('seo'));
    }

    /**
     * Términos y Condiciones
     */
    public function terms()
    {
        $seo = [
            'title' => 'Términos y Condiciones - Contaplus',
            'description' => 'Términos y condiciones de uso de la plataforma Contaplus.',
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
            'title' => 'Política de Privacidad - Contaplus',
            'description' => 'Cómo protegemos y tratamos tus datos en Contaplus. Cumplimiento GDPR y normativas de protección de datos.',
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
