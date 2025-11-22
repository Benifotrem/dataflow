@extends('layouts.app')

@section('page-title', 'Detalle de Documento')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('documents.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Documentos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $document->file_name }}</h2>
                <p class="text-gray-600 mt-1">Subido el {{ $document->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $document->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $document->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $document->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($document->status) }}
            </span>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-600">Entidad</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $document->entity->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Tamaño</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Tipo</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $document->file_type }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Estado</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($document->status) }}</dd>
            </div>
        </dl>

        @if($document->extracted_data)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Extraída (IA)</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($document->extracted_data, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        <div class="mt-8 flex gap-4">
            <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('¿Eliminar este documento?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                    Eliminar Documento
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
