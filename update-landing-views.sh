#!/bin/bash

# Script para actualizar las vistas de landing page en producción
# Usa el método heredoc para evitar corrupción de archivos Blade

set -e

echo "🔄 Actualizando vistas de landing page..."

# Crear directorio si no existe
mkdir -p resources/views/layouts
mkdir -p resources/views/landing

# Crear landing.blade.php usando heredoc
echo "📝 Creando resources/views/layouts/landing.blade.php..."
cat > resources/views/layouts/landing.blade.php << 'EOFBLADE'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- SEO Meta Tags --}}
    <title>{{ $seo['title'] ?? 'Contaplus - Automatización Contable con IA' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Plataforma de automatización contable con inteligencia artificial' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'contabilidad automática, software contable, IA contabilidad' }}">
    <meta name="author" content="Contaplus">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seo['title'] ?? 'Contaplus' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? 'Automatización contable con IA' }}">
    <meta property="og:image" content="{{ $seo['image'] ?? asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="es_ES">
    <meta property="og:site_name" content="Contaplus">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['title'] ?? 'Contaplus' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'Automatización contable con IA' }}">
    <meta name="twitter:image" content="{{ $seo['image'] ?? asset('images/og-image.jpg') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Contaplus",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "offers": {
            "@type": "AggregateOffer",
            "lowPrice": "19.99",
            "highPrice": "49.99",
            "priceCurrency": "USD",
            "offerCount": "2"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.8",
            "ratingCount": "150"
        },
        "description": "Plataforma de automatización contable con inteligencia artificial para España e Hispanoamérica"
    }
    </script>

    {{-- Tailwind CSS via CDN (en producción usar @vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased bg-gray-50">

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white shadow-sm">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z"/>
                    </svg>
                    <span class="text-2xl font-bold text-gray-900">Contaplus</span>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-purple-600 font-medium">Inicio</a>
                    <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-purple-600 font-medium">Precios</a>
                    <a href="{{ route('faq') }}" class="text-gray-700 hover:text-purple-600 font-medium">FAQ</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600 font-medium">Blog</a>
                </div>

                {{-- CTA Buttons --}}
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Iniciar Sesión</a>
                    <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        Probar Gratis
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <button class="md:hidden text-gray-700" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-purple-600 font-medium">Inicio</a>
                    <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-purple-600 font-medium">Precios</a>
                    <a href="{{ route('faq') }}" class="text-gray-700 hover:text-purple-600 font-medium">FAQ</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600 font-medium">Blog</a>
                    <div class="pt-4 border-t">
                        <a href="#" class="block text-purple-600 hover:text-purple-700 font-medium mb-3">Iniciar Sesión</a>
                        <a href="#" class="block bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium text-center transition">
                            Probar Gratis
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- Main Content --}}
    <main class="pt-16">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                {{-- Column 1: About --}}
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-8 h-8 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z"/>
                        </svg>
                        <span class="text-xl font-bold text-white">Contaplus</span>
                    </div>
                    <p class="text-sm">
                        Automatización contable con inteligencia artificial para España e Hispanoamérica.
                    </p>
                </div>

                {{-- Column 2: Product --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Producto</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Características</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition">Precios</a></li>
                        <li><a href="#" class="hover:text-white transition">Integraciones</a></li>
                        <li><a href="#" class="hover:text-white transition">Roadmap</a></li>
                    </ul>
                </div>

                {{-- Column 3: Company --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Empresa</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Sobre Nosotros</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Carreras</a></li>
                        <li><a href="#" class="hover:text-white transition">Contacto</a></li>
                    </ul>
                </div>

                {{-- Column 4: Legal --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">Términos y Condiciones</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Política de Privacidad</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Soporte</a></li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm mb-4 md:mb-0">
                    © {{ date('Y') }} Contaplus. Todos los derechos reservados.
                </p>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.840 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
EOFBLADE

echo "✅ Layout landing.blade.php creado ($(wc -l < resources/views/layouts/landing.blade.php) líneas)"

# Crear index.blade.php usando heredoc
echo "📝 Creando resources/views/landing/index.blade.php..."
cat > resources/views/landing/index.blade.php << 'EOFBLADE'
@extends('layouts.landing')

@section('content')

{{-- Hero Section --}}
<section class="gradient-bg text-white py-20 md:py-32">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Automatiza tu Contabilidad con <span class="text-yellow-300">Inteligencia Artificial</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-purple-100">
                    Olvídate de las tareas manuales. Contaplus procesa facturas, concilia extractos y gestiona tu fiscalidad automáticamente.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="#" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition text-center">
                        Prueba Gratis 14 Días
                    </a>
                    <a href="#demo" class="glass-effect text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/20 transition text-center">
                        Ver Demo
                    </a>
                </div>
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Sin tarjeta de crédito</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Cancela cuando quieras</span>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="relative animate-float">
                    <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                        <div class="bg-white rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-500">Factura #2024-001</span>
                                <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-semibold">Procesada</span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-700">
                                    <span>Total:</span>
                                    <span class="font-bold">€1,234.56</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>IVA (21%):</span>
                                    <span class="font-bold">€259.26</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Categoría:</span>
                                    <span class="font-bold">Servicios</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Trusted By Section --}}
<section class="py-12 bg-white border-y">
    <div class="container mx-auto px-6">
        <p class="text-center text-gray-500 mb-8">Confiado por más de 500 empresas en España e Hispanoamérica</p>
        <div class="flex justify-center items-center gap-12 flex-wrap opacity-50">
            <div class="text-2xl font-bold text-gray-400">Empresa 1</div>
            <div class="text-2xl font-bold text-gray-400">Empresa 2</div>
            <div class="text-2xl font-bold text-gray-400">Empresa 3</div>
            <div class="text-2xl font-bold text-gray-400">Empresa 4</div>
        </div>
    </div>
</section>

{{-- Problems Section --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                ¿Te sientes así cada cierre de mes?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Los contadores y PyMEs pierden incontables horas en tareas repetitivas y propensas a errores
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-md">
                <div class="text-red-500 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Pérdida de Tiempo</h3>
                <p class="text-gray-600">
                    Horas transcribiendo facturas manualmente, clasificando transacciones y buscando documentos perdidos.
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-md">
                <div class="text-red-500 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Errores Costosos</h3>
                <p class="text-gray-600">
                    Errores de transcripción, categorización incorrecta y multas por declaraciones fuera de plazo.
                </p>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-md">
                <div class="text-red-500 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Estrés y Agobio</h3>
                <p class="text-gray-600">
                    Noches sin dormir antes de los cierres contables, preocupación constante por cumplir plazos fiscales.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Solution Section --}}
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                La Solución Inteligente que Necesitas
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Contaplus automatiza todo el proceso contable con inteligencia artificial
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 rounded-xl">
                <div class="bg-purple-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">OCR Inteligente</h3>
                <p class="text-gray-600">
                    Sube facturas en PDF, imagen o Excel. Nuestra IA extrae todos los datos automáticamente: importes, IVA, fechas, proveedor.
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-teal-50 p-8 rounded-xl">
                <div class="bg-green-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Conciliación Automática</h3>
                <p class="text-gray-600">
                    Importa tus extractos bancarios y Contaplus concilia automáticamente con tus transacciones. Sin más reconciliaciones manuales.
                </p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-8 rounded-xl">
                <div class="bg-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Clasificación Fiscal</h3>
                <p class="text-gray-600">
                    La IA clasifica automáticamente cada transacción según las reglas fiscales de tu país. IVA, retenciones, todo gestionado.
                </p>
            </div>

            <div class="bg-gradient-to-br from-pink-50 to-red-50 p-8 rounded-xl">
                <div class="bg-pink-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Calendario Fiscal</h3>
                <p class="text-gray-600">
                    Nunca más pierdas un plazo. Sincroniza automáticamente todos tus plazos fiscales con Google Calendar, Outlook o Apple Calendar.
                </p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-xl">
                <div class="bg-indigo-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Colaboración B2B</h3>
                <p class="text-gray-600">
                    Para despachos: gestiona múltiples clientes, asigna roles (propietario/asesor) y colabora en tiempo real con validaciones.
                </p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-8 rounded-xl">
                <div class="bg-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Multi-Jurisdicción</h3>
                <p class="text-gray-600">
                    Soporte nativo para España y toda Hispanoamérica. Reglas fiscales específicas por país, moneda local, idioma.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-20 gradient-bg text-white">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-5xl font-bold mb-2">95%</div>
                <div class="text-purple-200">Reducción de errores</div>
            </div>
            <div>
                <div class="text-5xl font-bold mb-2">10h</div>
                <div class="text-purple-200">Ahorradas por semana</div>
            </div>
            <div>
                <div class="text-5xl font-bold mb-2">500+</div>
                <div class="text-purple-200">Empresas confían en nosotros</div>
            </div>
            <div>
                <div class="text-5xl font-bold mb-2">24/7</div>
                <div class="text-purple-200">Procesamiento continuo</div>
            </div>
        </div>
    </div>
</section>

{{-- Pricing Section --}}
<section id="pricing" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Precios Transparentes y Flexibles
            </h2>
            <p class="text-xl text-gray-600">
                Elige el plan que se adapte a tus necesidades. Sin sorpresas.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            {{-- Plan Básico --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-200 hover:border-purple-400 transition">
                <div class="text-center">
                    <h3 class="text-2xl font-bold mb-2">Plan Básico</h3>
                    <p class="text-gray-600 mb-6">Para PyMEs y autónomos</p>
                    <div class="mb-6">
                        <span class="text-5xl font-bold">$19.99</span>
                        <span class="text-gray-600">/mes</span>
                    </div>
                    <a href="#" class="block w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-bold transition mb-6">
                        Comenzar Prueba Gratis
                    </a>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>1 entidad fiscal</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>500 documentos IA/mes</strong> incluidos</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>OCR y clasificación automática</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Conciliación bancaria</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Calendario fiscal (iCalendar)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Soporte por email</span>
                    </li>
                </ul>
            </div>

            {{-- Plan Avanzado --}}
            <div class="bg-gradient-to-br from-purple-600 to-indigo-600 text-white rounded-2xl shadow-2xl p-8 border-2 border-purple-600 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-yellow-400 text-purple-900 px-4 py-1 rounded-full text-sm font-bold">
                    RECOMENDADO
                </div>
                <div class="text-center">
                    <h3 class="text-2xl font-bold mb-2">Plan Avanzado</h3>
                    <p class="text-purple-100 mb-6">Para despachos y contadores</p>
                    <div class="mb-6">
                        <span class="text-5xl font-bold">$49.99</span>
                        <span class="text-purple-100">/mes</span>
                    </div>
                    <a href="#" class="block w-full bg-white text-purple-600 hover:bg-gray-100 py-3 rounded-lg font-bold transition mb-6">
                        Comenzar Prueba Gratis
                    </a>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>Clientes ilimitados</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>500 documentos IA/mes</strong> incluidos</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>Todo del Plan Básico</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span><strong>Colaboración en tiempo real</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Roles Propietario/Asesor</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Soporte prioritario</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Onboarding personalizado</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">
                ¿Necesitas más documentos? <strong>$9.99</strong> por cada 500 documentos adicionales
            </p>
            <a href="{{ route('faq') }}" class="text-purple-600 hover:text-purple-700 font-medium underline">
                Ver preguntas frecuentes sobre precios →
            </a>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-20 gradient-bg text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Comienza a Automatizar Hoy Mismo
        </h2>
        <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
            Únete a cientos de empresas que ya transformaron su contabilidad con Contaplus
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
                Prueba Gratis 14 Días
            </a>
            <a href="#" class="glass-effect text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/20 transition">
                Hablar con Ventas
            </a>
        </div>
        <p class="mt-6 text-purple-200 text-sm">
            Sin tarjeta de crédito • Cancela cuando quieras • Soporte en español
        </p>
    </div>
</section>

@endsection
EOFBLADE

echo "✅ Index index.blade.php creado ($(wc -l < resources/views/landing/index.blade.php) líneas)"

# Limpiar caché de vistas
php artisan view:clear
php artisan config:clear
php artisan cache:clear

echo ""
echo "✅ Actualización completada!"
echo "📊 Archivos creados:"
echo "   - resources/views/layouts/landing.blade.php"
echo "   - resources/views/landing/index.blade.php"
echo ""
echo "🔗 Verifica en: https://dataflow.guaraniappstore.com/"
