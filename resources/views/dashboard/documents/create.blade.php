@extends('layouts.app')

@section('page-title', 'Subir Documento')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('documents.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Documentos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Subir Nuevo Documento</h2>

        @if($entities->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                <p class="font-medium">Primero necesitas crear una entidad fiscal</p>
                <a href="{{ route('entities.create') }}" class="text-yellow-900 hover:text-yellow-700 underline mt-2 inline-block">
                    Crear entidad →
                </a>
            </div>
        @else
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entidad Fiscal *
                    </label>
                    <select name="entity_id" id="entity_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Selecciona una entidad</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity->id }}" {{ old('entity_id') == $entity->id ? 'selected' : '' }}>
                                {{ $entity->name }} ({{ $entity->tax_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('entity_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Archivo *
                    </label>
                    <input type="file" name="file" id="file" required
                           accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.csv"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <p class="mt-1 text-sm text-gray-500">Formatos permitidos: PDF, JPG, PNG, Excel, CSV. Máximo 10MB.</p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-medium text-blue-900 mb-2">📄 Procesamiento Automático</h3>
                    <p class="text-sm text-blue-800">El documento será procesado automáticamente con IA para extraer información como importes, fechas, emisor, etc.</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit"
                            class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Subir Documento
                    </button>
                    <a href="{{ route('documents.index') }}"
                       class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
