@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Configuración del Blog</h1>
            <p class="text-gray-600 mt-2">Configura las API keys y parámetros para la generación automática de artículos</p>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-red-800 font-medium">Error al guardar configuración</h3>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.blog.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- API Keys Section --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">API Keys</h2>

                {{-- OpenRouter API Key --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        OpenRouter API Key
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        name="openrouter_api_key"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="sk-or-v1-..."
                        value="{{ old('openrouter_api_key') }}"
                    >
                    <p class="text-sm text-gray-500 mt-1">
                        Obtén tu API key en <a href="https://openrouter.ai/keys" target="_blank" class="text-purple-600 hover:underline">openrouter.ai/keys</a>
                    </p>
                    @if($settings['openrouter_api_key'] ?? null)
                        <p class="text-sm text-green-600 mt-1">✓ API key configurada</p>
                    @endif
                </div>

                {{-- Pexels API Key --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pexels API Key
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        name="pexels_api_key"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        value="{{ old('pexels_api_key') }}"
                    >
                    <p class="text-sm text-gray-500 mt-1">
                        Obtén tu API key en <a href="https://www.pexels.com/api/" target="_blank" class="text-purple-600 hover:underline">pexels.com/api</a>
                    </p>
                    @if($settings['pexels_api_key'] ?? null)
                        <p class="text-sm text-green-600 mt-1">✓ API key configurada</p>
                    @endif
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Sobre las API Keys:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>OpenRouter:</strong> Se usa para generar el contenido de los artículos con DeepSeek (muy económico)</li>
                                <li><strong>Pexels:</strong> API gratuita para descargar imágenes de stock de alta calidad</li>
                                <li>Las API keys se guardan encriptadas en la base de datos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Generation Parameters --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Parámetros de Generación</h2>

                {{-- Model --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Modelo de IA
                    </label>
                    <select name="blog_generation_model" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="deepseek/deepseek-chat" {{ ($settings['blog_generation_model'] ?? 'deepseek/deepseek-chat') == 'deepseek/deepseek-chat' ? 'selected' : '' }}>
                            DeepSeek Chat (Recomendado - $0.14/1M tokens)
                        </option>
                        <option value="openai/gpt-4o-mini" {{ ($settings['blog_generation_model'] ?? '') == 'openai/gpt-4o-mini' ? 'selected' : '' }}>
                            GPT-4o Mini ($0.15/1M tokens)
                        </option>
                        <option value="anthropic/claude-3.5-sonnet" {{ ($settings['blog_generation_model'] ?? '') == 'anthropic/claude-3.5-sonnet' ? 'selected' : '' }}>
                            Claude 3.5 Sonnet ($3/1M tokens)
                        </option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">DeepSeek ofrece excelente calidad a muy bajo costo</p>
                </div>

                {{-- Word Count Range --}}
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Mínimo de palabras
                        </label>
                        <input
                            type="number"
                            name="blog_min_words"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            value="{{ old('blog_min_words', $settings['blog_min_words'] ?? 1200) }}"
                            min="500"
                            max="5000"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Máximo de palabras
                        </label>
                        <input
                            type="number"
                            name="blog_max_words"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            value="{{ old('blog_max_words', $settings['blog_max_words'] ?? 1800) }}"
                            min="500"
                            max="5000"
                        >
                    </div>
                </div>

                <p class="text-sm text-gray-500">
                    Los artículos se generarán con una extensión entre estos valores (recomendado: 1200-1800 palabras)
                </p>
            </div>

            {{-- Author Configuration --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Autor de los Artículos</h2>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del autor
                    </label>
                    <input
                        type="text"
                        name="blog_author_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        value="{{ old('blog_author_name', $settings['blog_author_name'] ?? 'César Ruzafa') }}"
                        placeholder="César Ruzafa"
                    >
                    <p class="text-sm text-gray-500 mt-1">
                        Este nombre aparecerá como autor de todos los artículos generados
                    </p>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Importante:</p>
                            <p class="mt-1">Los artículos nunca mencionarán que están escritos con IA. Se presentarán como contenido original del autor especificado.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Automatic Generation --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Generación Automática</h2>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="blog_auto_generation_enabled"
                            id="blog_auto_generation_enabled"
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            {{ ($settings['blog_auto_generation_enabled'] ?? false) ? 'checked' : '' }}
                        >
                        <label for="blog_auto_generation_enabled" class="ml-2 block text-sm font-medium text-gray-700">
                            Activar generación automática de artículos
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="blog_auto_publish"
                            id="blog_auto_publish"
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            {{ ($settings['blog_auto_publish'] ?? false) ? 'checked' : '' }}
                        >
                        <label for="blog_auto_publish" class="ml-2 block text-sm font-medium text-gray-700">
                            Publicar artículos automáticamente (sin revisión)
                        </label>
                    </div>
                </div>

                <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-2">¿Cómo funciona la generación automática?</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Genera 1 artículo por país en orden alfabético</li>
                                <li>Recorre los 21 países de habla hispana + Brasil</li>
                                <li>Al llegar al final, vuelve a empezar desde Argentina</li>
                                <li>Si no marcas "Publicar automáticamente", los artículos quedan como borradores para revisión</li>
                            </ul>
                            <p class="mt-3 font-medium">Comando cron sugerido (diario a las 9am):</p>
                            <code class="block mt-1 bg-blue-100 p-2 rounded text-xs">
                                0 9 * * * cd /ruta/proyecto && php artisan blog:generate --sequence
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.blog.create') }}" class="text-gray-600 hover:text-gray-800">
                    ← Volver a generar artículo
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Guardar Configuración
                </button>
            </div>
        </form>

        {{-- Cost Estimator --}}
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">💰 Estimación de Costos</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p>• <strong>DeepSeek Chat:</strong> ~$0.01 USD por artículo de 1500 palabras</p>
                <p>• <strong>Pexels:</strong> Gratis (API gratuita sin límites)</p>
                <p class="text-green-600 font-medium mt-3">✓ Puedes generar ~100 artículos por $1 USD usando DeepSeek</p>
            </div>
        </div>
    </div>
</div>
@endsection
