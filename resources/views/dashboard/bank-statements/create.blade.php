@extends('layouts.app')

@section('page-title', 'Cargar Extracto Bancario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('bank-statements.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Extractos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cargar Extracto Bancario</h2>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-yellow-900 mb-2">⚠️ Política de Retención de Datos</h3>
            <p class="text-sm text-yellow-800">Los extractos bancarios se eliminan automáticamente 60 días después del fin de mes por motivos de seguridad y privacidad.</p>
        </div>

        <form action="{{ route('bank-statements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-2">Entidad Fiscal *</label>
                <select name="entity_id" id="entity_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Selecciona una entidad</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                    @endforeach
                </select>
                @error('entity_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-700 mb-2">Inicio del Período *</label>
                    <input type="date" name="period_start" id="period_start" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('period_start')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">Fin del Período *</label>
                    <input type="date" name="period_end" id="period_end" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('period_end')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Archivo del Extracto *</label>
                <input type="file" name="file" id="file" required accept=".pdf,.xlsx,.xls,.csv" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <p class="mt-1 text-sm text-gray-500">Formatos: PDF, Excel, CSV. Máximo 10MB.</p>
                @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">Cargar Extracto</button>
                <a href="{{ route('bank-statements.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition text-center">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
