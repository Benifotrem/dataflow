@extends('layouts.landing')

@section('content')

{{-- Hero Section --}}
<section class="gradient-bg text-white py-16 md:py-24">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">
            Blog de <span class="text-yellow-300">Dataflow</span>
        </h1>
        <p class="text-xl md:text-2xl text-purple-100 max-w-3xl mx-auto">
            Artículos, guías y novedades sobre contabilidad, fiscalidad y automatización
        </p>
    </div>
</section>

{{-- Blog Posts --}}
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6 max-w-6xl">
        @if($posts->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow">
                        @if($post->featured_image)
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <div class="h-48 bg-gradient-to-br from-purple-500 to-blue-600 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                                </svg>
                                {{ $post->published_at->format('d M Y') }}
                                <span class="mx-2">•</span>
                                <span>{{ $post->reading_time }} min lectura</span>
                            </div>
                            <a href="{{ route('blog.show', $post->slug) }}">
                                <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-purple-600 transition">{{ $post->title }}</h3>
                            </a>
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700">
                                Leer más
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            {{-- No Posts Yet --}}
            <div class="text-center">
                <div class="bg-white rounded-xl shadow-lg p-8 max-w-2xl mx-auto">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Artículos Próximamente</h3>
                    <p class="text-gray-600 mb-6">
                        Estamos generando contenido valioso para ayudarte a mejorar tu gestión contable.
                        Mientras tanto, puedes explorar nuestra plataforma y comenzar tu prueba gratuita.
                    </p>
                    <a href="{{ route('register') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Comenzar Prueba Gratis
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Newsletter Subscription --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-2xl p-8 md:p-12 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Suscríbete a nuestro Newsletter</h2>
            <p class="text-purple-100 mb-6 max-w-2xl mx-auto">
                Recibe artículos, guías y actualizaciones directamente en tu correo. Sin spam, solo contenido valioso.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input
                    type="email"
                    placeholder="tu@email.com"
                    class="flex-1 px-6 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-300"
                >
                <button class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Suscribirme
                </button>
            </div>
            <p class="text-sm text-purple-200 mt-4">Próximamente disponible</p>
        </div>
    </div>
</section>

@endsection
