@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Generar Artículo con IA</h1>
            <p class="text-gray-600 mt-2">Crea un artículo de blog automáticamente usando inteligencia artificial</p>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-red-800 font-medium">Error al generar artículo</h3>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Generation Form --}}
        <form action="{{ route('admin.blog.generate') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
            @csrf

            {{-- Country Selection --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    País Objetivo
                </label>
                <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Aleatorio</option>
                    @foreach($countries as $code)
                        <option value="{{ $code }}">{{ $countryNames[$code] ?? strtoupper($code) }}</option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Si no seleccionas ninguno, se elegirá un país aleatorio</p>
            </div>

            {{-- Topic (Optional) --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tema Específico (Opcional)
                </label>
                <input
                    type="text"
                    name="topic"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Ej: Declaración de la renta 2024"
                    value="{{ old('topic') }}"
                >
                <p class="text-sm text-gray-500 mt-1">Si lo dejas vacío, se generará un tema trending automáticamente</p>
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-blue-800 font-medium">¿Cómo funciona?</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• Se selecciona un tema fiscal trending (o usas el que especifiques)</li>
                            <li>• OpenRouter + DeepSeek generan un artículo de 1200-1800 palabras</li>
                            <li>• Se busca y descarga automáticamente una imagen relacionada de Pexels</li>
                            <li>• El artículo se crea como borrador para que lo revises antes de publicar</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- API Keys Warning --}}
            @if(!App\Models\Setting::get('openrouter_api_key') || !App\Models\Setting::get('pexels_api_key'))
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-yellow-800 font-medium">API Keys no configuradas</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Debes configurar tus API keys antes de generar artículos.
                                <a href="{{ route('admin.settings.blog') }}" class="underline font-medium">Ir a Configuración</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Buttons --}}
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('admin.blog.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Volver a la lista
                </a>
                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center"
                    {{ (!App\Models\Setting::get('openrouter_api_key') || !App\Models\Setting::get('pexels_api_key')) ? 'disabled' : '' }}
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generar Artículo con IA
                </button>
            </div>
        </form>

        {{-- Quick Stats --}}
        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Total Artículos</div>
                <div class="text-2xl font-bold text-gray-900">{{ \App\Models\Post::count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Publicados</div>
                <div class="text-2xl font-bold text-green-600">{{ \App\Models\Post::where('status', 'published')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Borradores</div>
                <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\Post::where('status', 'draft')->count() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
