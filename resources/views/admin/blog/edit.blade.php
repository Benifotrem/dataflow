@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Editar Artículo</h1>
            <p class="text-gray-600 mt-2">Revisa y edita el artículo antes de publicar</p>
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
                        <h3 class="text-red-800 font-medium">Error al actualizar artículo</h3>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.blog.update', $post) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Status & Quick Actions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado del Artículo</label>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Publicado</option>
                            <option value="archived" {{ $post->status === 'archived' ? 'selected' : '' }}>Archivado</option>
                        </select>
                    </div>

                    @if($post->status === 'published')
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-700 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Ver artículo publicado
                        </a>
                    @endif
                </div>

                @if($post->published_at)
                    <p class="text-sm text-gray-600">
                        <strong>Publicado:</strong> {{ $post->published_at->format('d/m/Y H:i') }}
                    </p>
                @endif
                @if($post->views > 0)
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>Vistas:</strong> {{ number_format($post->views) }}
                    </p>
                @endif
            </div>

            {{-- Title --}}
            <div class="bg-white rounded-lg shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Título *
                </label>
                <input
                    type="text"
                    name="title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    value="{{ old('title', $post->title) }}"
                    required
                >
            </div>

            {{-- Slug --}}
            <div class="bg-white rounded-lg shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Slug (URL) *
                </label>
                <input
                    type="text"
                    name="slug"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    value="{{ old('slug', $post->slug) }}"
                    required
                >
                <p class="text-sm text-gray-500 mt-1">
                    URL: {{ url('/blog') }}/<strong>{{ old('slug', $post->slug) }}</strong>
                </p>
            </div>

            {{-- Excerpt --}}
            <div class="bg-white rounded-lg shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Resumen (Excerpt) *
                </label>
                <textarea
                    name="excerpt"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    required
                    maxlength="500"
                >{{ old('excerpt', $post->excerpt) }}</textarea>
                <p class="text-sm text-gray-500 mt-1">Máximo 500 caracteres para SEO</p>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-lg shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Contenido (HTML) *
                </label>
                <textarea
                    name="content"
                    rows="20"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm"
                    required
                >{{ old('content', $post->content) }}</textarea>
                <p class="text-sm text-gray-500 mt-1">El contenido se muestra como HTML en el artículo</p>
            </div>

            {{-- Featured Image --}}
            @if($post->featured_image)
            <div class="bg-white rounded-lg shadow p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Imagen Destacada
                </label>
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-w-2xl rounded-lg">
                @if($post->image_credits)
                    <p class="text-sm text-gray-500 mt-2">{{ $post->image_credits }}</p>
                @endif
            </div>
            @endif

            {{-- Metadata --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Metadatos</h3>

                <div class="grid md:grid-cols-2 gap-4">
                    {{-- Country --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            País
                        </label>
                        <select name="country" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Ninguno</option>
                            @foreach($countries as $code)
                                <option value="{{ $code }}" {{ old('country', $post->country) === $code ? 'selected' : '' }}>
                                    {{ $countryNames[$code] ?? strtoupper($code) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Author Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Autor
                        </label>
                        <input
                            type="text"
                            name="author_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            value="{{ old('author_name', $post->author_name) }}"
                            placeholder="Ej: Juan Pérez, Contador Público"
                        >
                    </div>

                    {{-- Keywords --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keywords (separadas por comas)
                        </label>
                        <input
                            type="text"
                            name="keywords"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            value="{{ old('keywords', is_array($post->keywords) ? implode(', ', $post->keywords) : '') }}"
                            placeholder="contabilidad, impuestos, pymes"
                        >
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-between bg-white rounded-lg shadow p-6">
                <a href="{{ route('admin.blog.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Volver a la lista
                </a>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        {{-- Danger Zone --}}
        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-red-900 mb-3">Zona de Peligro</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-700">Eliminar este artículo permanentemente</p>
                    <p class="text-xs text-red-600 mt-1">Esta acción no se puede deshacer</p>
                </div>
                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" onsubmit="return confirm('¿Estás completamente seguro de eliminar este artículo? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                        Eliminar Artículo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
