@extends('layouts.landing')

@section('content')

{{-- Article Header --}}
<article class="pt-24 pb-12">
    <div class="container mx-auto px-6 max-w-4xl">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-purple-600">Inicio</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('blog') }}" class="hover:text-purple-600">Blog</a>
            <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-gray-900">{{ $post->title }}</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

        {{-- Meta Info --}}
        <div class="flex flex-wrap items-center gap-4 text-gray-600 mb-8">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                </svg>
                {{ $post->published_at->format('d M Y') }}
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                {{ $post->reading_time }} min lectura
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                </svg>
                {{ $post->views }} vistas
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
            <div class="mb-8 rounded-xl overflow-hidden shadow-lg">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-auto">
                @if($post->image_credits)
                    <p class="text-xs text-gray-500 mt-2 text-center">{{ $post->image_credits }}</p>
                @endif
            </div>
        @endif

        {{-- Article Content --}}
        <div class="prose prose-lg max-w-none mb-12">
            <div class="text-xl text-gray-700 mb-8 font-medium">{{ $post->excerpt }}</div>
            <div class="article-content">
                {!! $post->content !!}
            </div>
        </div>

        {{-- Keywords --}}
        @if($post->keywords && count($post->keywords) > 0)
            <div class="flex flex-wrap gap-2 mb-12">
                @foreach($post->keywords as $keyword)
                    <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                        {{ $keyword }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- CTA Banner --}}
        <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl p-8 text-center text-white mb-12">
            <h3 class="text-2xl md:text-3xl font-bold mb-4">¿Listo para automatizar tu contabilidad?</h3>
            <p class="text-purple-100 mb-6 max-w-2xl mx-auto">
                Dataflow te ayuda a aplicar todo lo que has aprendido. Prueba gratis durante 14 días.
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Comenzar Prueba Gratis
            </a>
        </div>
    </div>
</article>

{{-- Related Posts --}}
@if($relatedPosts->count() > 0)
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6 max-w-6xl">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Artículos Relacionados</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedPosts as $related)
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow">
                        @if($related->featured_image)
                            <a href="{{ route('blog.show', $related->slug) }}">
                                <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <div class="h-48 bg-gradient-to-br from-purple-500 to-blue-600"></div>
                        @endif
                        <div class="p-6">
                            <div class="text-sm text-gray-500 mb-2">{{ $related->published_at->format('d M Y') }}</div>
                            <a href="{{ route('blog.show', $related->slug) }}">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 hover:text-purple-600 transition">{{ $related->title }}</h3>
                            </a>
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($related->excerpt, 100) }}</p>
                            <a href="{{ route('blog.show', $related->slug) }}" class="inline-flex items-center text-purple-600 font-semibold text-sm hover:text-purple-700">
                                Leer más
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

<style>
    .article-content h2 {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }
    .article-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
    }
    .article-content h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #374151;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .article-content p {
        margin-bottom: 1.25rem;
        line-height: 1.75;
        color: #4b5563;
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .article-content li {
        margin-bottom: 0.5rem;
        line-height: 1.75;
        color: #4b5563;
    }
    .article-content strong {
        font-weight: 600;
        color: #1f2937;
    }
    .article-content em {
        font-style: italic;
    }
    .article-content a {
        color: #7c3aed;
        text-decoration: underline;
    }
    .article-content a:hover {
        color: #6d28d9;
    }
</style>

@endsection
