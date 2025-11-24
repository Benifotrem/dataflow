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
                    Olvídate de las tareas manuales. Dataflow procesa facturas, concilia extractos y gestiona tu fiscalidad automáticamente.
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
                Dataflow automatiza todo el proceso contable con inteligencia artificial
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
                    Importa tus extractos bancarios y Dataflow concilia automáticamente con tus transacciones. Sin más reconciliaciones manuales.
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
            Únete a cientos de empresas que ya transformaron su contabilidad con Dataflow
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
