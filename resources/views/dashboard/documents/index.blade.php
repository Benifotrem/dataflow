@extends('layouts.app')

@section('page-title', 'Documentos')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Documentos</h2>
        <a href="{{ route('documents.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            + Subir Documento
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Archivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($documents as $document)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $document->file_name }}</div>
                                <div class="text-sm text-gray-500">{{ number_format($document->file_size / 1024, 2) }} KB</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $document->entity->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $document->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $document->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $document->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($document->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $document->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('documents.show', $document) }}" class="text-purple-600 hover:text-purple-900 mr-3">Ver</a>
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este documento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 mb-3">No hay documentos subidos</p>
                        <a href="{{ route('documents.create') }}" class="text-purple-600 hover:text-purple-700 font-medium">Subir tu primer documento →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $documents->links() }}
</div>
@endsection
