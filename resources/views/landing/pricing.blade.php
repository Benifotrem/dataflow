@extends('layouts.landing')

@section('content')

{{-- Hero Section --}}
<section class="gradient-bg text-white py-16 md:py-24">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">
            Planes Simples, <span class="text-yellow-300">Precios Transparentes</span>
        </h1>
        <p class="text-xl md:text-2xl text-purple-100 max-w-3xl mx-auto">
            Elige el plan perfecto para tu negocio. Sin costos ocultos, sin contratos largos. Empieza gratis hoy.
        </p>
    </div>
</section>

{{-- Pricing Plans --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">

            {{-- Plan Básico --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-8">
                    <h3 class="text-2xl font-bold mb-2">Plan Básico</h3>
                    <p class="text-blue-100 mb-6">Perfecto para PyMEs y personas físicas</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-bold">$9.99</span>
                        <span class="text-blue-100">/mes</span>
                    </div>
                </div>

                <div class="p-8">
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">1 Entidad Fiscal</p>
                                <p class="text-sm text-gray-600">Gestiona una empresa o actividad económica</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">300 Documentos IA/mes</p>
                                <p class="text-sm text-gray-600">Procesamiento automático con OCR inteligente</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Conciliación Bancaria</p>
                                <p class="text-sm text-gray-600">Importación manual segura de extractos</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Calendario Fiscal</p>
                                <p class="text-sm text-gray-600">Sincronización con Google/Outlook/Apple</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Import/Export CSV</p>
                                <p class="text-sm text-gray-600">Compatible con Excel y Google Sheets</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Soporte Email</p>
                                <p class="text-sm text-gray-600">Respuesta en 24-48 horas</p>
                            </div>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Comenzar Prueba Gratis
                    </a>
                    <p class="text-center text-sm text-gray-500 mt-3">14 días gratis, sin tarjeta</p>
                </div>
            </div>

            {{-- Plan Avanzado - Popular --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden relative border-4 border-purple-500">
                <div class="absolute top-0 right-0 bg-yellow-400 text-purple-900 px-4 py-1 rounded-bl-lg font-bold text-sm">
                    MÁS POPULAR
                </div>

                <div class="bg-gradient-to-br from-purple-600 to-purple-700 text-white p-8">
                    <h3 class="text-2xl font-bold mb-2">Plan Avanzado</h3>
                    <p class="text-purple-100 mb-6">Para contadores y despachos profesionales</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-bold">$29.99</span>
                        <span class="text-purple-100">/mes</span>
                    </div>
                </div>

                <div class="p-8">
                    <p class="text-sm font-semibold text-purple-600 mb-4">TODO LO DEL PLAN BÁSICO, MÁS:</p>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Clientes Ilimitados</p>
                                <p class="text-sm text-gray-600">Gestiona múltiples empresas clientes</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">1000 Documentos IA/mes</p>
                                <p class="text-sm text-gray-600">Compartidos entre todos tus clientes</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Colaboración en Tiempo Real</p>
                                <p class="text-sm text-gray-600">Trabaja con asesores simultáneamente</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Roles y Permisos</p>
                                <p class="text-sm text-gray-600">Propietario/Asesor con accesos diferenciados</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Soporte Prioritario</p>
                                <p class="text-sm text-gray-600">Respuesta en menos de 12 horas</p>
                            </div>
                        </li>

                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Reportes Avanzados</p>
                                <p class="text-sm text-gray-600">Analytics y métricas por cliente</p>
                            </div>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}" class="block w-full gradient-bg text-white text-center px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        Comenzar Prueba Gratis
                    </a>
                    <p class="text-center text-sm text-gray-500 mt-3">14 días gratis, sin tarjeta</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Addons Section --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-5xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Complementos Disponibles</h2>
            <p class="text-xl text-gray-600">Aumenta tu capacidad según crezcas</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Addon Pequeño --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-blue-200">
                <div class="text-center mb-6">
                    <div class="inline-block bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-semibold mb-4">ADDON PEQUEÑO</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">+250 Documentos</h3>
                    <div class="text-4xl font-bold text-blue-600 mb-1">$4.99</div>
                    <div class="text-gray-600 text-sm">por mes</div>
                </div>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>250 documentos adicionales/mes</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Compatible con ambos planes</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Sin compromiso a largo plazo</span>
                    </li>
                </ul>
            </div>

            {{-- Addon Grande --}}
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 border-2 border-purple-300 relative">
                <div class="absolute top-0 right-0 bg-green-500 text-white px-3 py-1 rounded-bl-lg text-xs font-bold">MEJOR VALOR</div>
                <div class="text-center mb-6">
                    <div class="inline-block bg-purple-600 text-white px-4 py-1 rounded-full text-sm font-semibold mb-4">ADDON GRANDE</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">+1000 Documentos</h3>
                    <div class="text-4xl font-bold text-purple-600 mb-1">$9.99</div>
                    <div class="text-gray-600 text-sm">por mes</div>
                </div>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>1000 documentos adicionales/mes</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Compatible con ambos planes</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Sin compromiso a largo plazo</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span>Ahorra $10+ vs addon pequeño</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Comparison Table --}}
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Comparación Detallada</h2>
            <p class="text-xl text-gray-600">Todas las características al detalle</p>
        </div>

        <div class="max-w-5xl mx-auto overflow-x-auto">
            <table class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-gray-900 font-semibold">Característica</th>
                        <th class="px-6 py-4 text-center text-gray-900 font-semibold">Básico</th>
                        <th class="px-6 py-4 text-center text-gray-900 font-semibold bg-purple-50">Avanzado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Entidades fiscales</td>
                        <td class="px-6 py-4 text-center">1</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">Ilimitadas</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Documentos IA/mes</td>
                        <td class="px-6 py-4 text-center">300</td>
                        <td class="px-6 py-4 text-center bg-purple-50">1000</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Procesamiento OCR automático</td>
                        <td class="px-6 py-4 text-center">✓</td>
                        <td class="px-6 py-4 text-center bg-purple-50">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Conciliación bancaria</td>
                        <td class="px-6 py-4 text-center">✓</td>
                        <td class="px-6 py-4 text-center bg-purple-50">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Calendario fiscal iCalendar</td>
                        <td class="px-6 py-4 text-center">✓</td>
                        <td class="px-6 py-4 text-center bg-purple-50">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Import/Export CSV</td>
                        <td class="px-6 py-4 text-center">✓</td>
                        <td class="px-6 py-4 text-center bg-purple-50">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Gestión multi-cliente</td>
                        <td class="px-6 py-4 text-center text-gray-400">—</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Colaboración en tiempo real</td>
                        <td class="px-6 py-4 text-center text-gray-400">—</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Roles y permisos</td>
                        <td class="px-6 py-4 text-center text-gray-400">—</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Reportes avanzados</td>
                        <td class="px-6 py-4 text-center text-gray-400">—</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">✓</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-gray-700">Soporte</td>
                        <td class="px-6 py-4 text-center">Email (24-48h)</td>
                        <td class="px-6 py-4 text-center bg-purple-50 font-semibold">Prioritario (&lt;12h)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Preguntas Frecuentes sobre Precios</h2>
        </div>

        <div class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Puedo cambiar de plan en cualquier momento?</h3>
                <p class="text-gray-600">Sí, puedes actualizar o degradar tu plan cuando quieras. Los cambios se reflejan en tu próximo ciclo de facturación.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Qué pasa si supero el límite de documentos de mi plan?</h3>
                <p class="text-gray-600">Te notificaremos cuando estés cerca del límite. Puedes agregar paquetes adicionales en cualquier momento: +250 documentos por $4.99/mes o +1000 documentos por $9.99/mes.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Hay contratos o compromisos a largo plazo?</h3>
                <p class="text-gray-600">No. Todos nuestros planes son mensuales y puedes cancelar en cualquier momento sin penalización.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿La prueba gratis de 14 días requiere tarjeta de crédito?</h3>
                <p class="text-gray-600">No, puedes probar Dataflow completamente gratis durante 14 días sin proporcionar información de pago.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">¿Ofrecen descuentos por pago anual?</h3>
                <p class="text-gray-600">¡Sí! Ahorra 2 meses pagando anualmente. Contacta con ventas para más información.</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">¿Tienes más preguntas sobre precios?</p>
            <a href="{{ route('faq') }}" class="text-purple-600 font-semibold hover:text-purple-700">Ver todas las preguntas frecuentes →</a>
        </div>
    </div>
</section>

{{-- CTA Final --}}
<section class="gradient-bg text-white py-16">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            ¿Listo para Automatizar tu Contabilidad?
        </h2>
        <p class="text-xl text-purple-100 mb-8 max-w-2xl mx-auto">
            Únete a más de 500 empresas que ya confían en Dataflow para su gestión fiscal
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
                Empezar Prueba Gratis
            </a>
            <a href="{{ route('home') }}#demo" class="glass-effect text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/20 transition">
                Ver Demo
            </a>
        </div>
        <p class="text-sm text-purple-100 mt-4">Sin tarjeta de crédito • Cancela cuando quieras</p>
    </div>
</section>

@endsection
