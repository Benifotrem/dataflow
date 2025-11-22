@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión del Blog</h1>
            <p class="text-gray-600 mt-2">Administra los artículos generados automáticamente</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Generar Artículo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total</div>
            <div class="text-2xl font-bold">{{ $posts->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Publicados</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Post::where('status', 'published')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Borradores</div>
            <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\Post::where('status', 'draft')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Vistas</div>
            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Post::sum('views') }}</div>
        </div>
    </div>

    {{-- Posts Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">País</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vistas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($post->excerpt, 60) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ strtoupper($post->country ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($post->status === 'published')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                            @elseif($post->status === 'draft')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Borrador</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Archivado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->views }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-900">Ver</a>
                                @endif
                                @if($post->status === 'draft')
                                    <form action="{{ route('admin.blog.publish', $post) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Publicar</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.blog.edit', $post) }}" class="text-purple-600 hover:text-purple-900">Editar</a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-4 text-gray-500">No hay artículos todavía</p>
                            <a href="{{ route('admin.blog.create') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-700 font-medium">
                                Generar tu primer artículo →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="font-bold text-gray-900 mb-3">Acciones Rápidas</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.settings.blog') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                ⚙️ Configurar API Keys
            </a>
            <a href="{{ route('blog') }}" target="_blank" class="text-purple-600 hover:text-purple-700 font-medium">
                👁️ Ver Blog Público
            </a>
        </div>
    </div>
</div>
@endsection
