@extends('layouts.app')

@section('page-title', $entity->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('entities.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a Entidades
            </a>
            <h2 class="text-2xl font-bold text-gray-900">{{ $entity->name }}</h2>
        </div>
        <a href="{{ route('entities.edit', $entity) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            Editar Entidad
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">NIF / CIF / RFC</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $entity->tax_id }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">País</h3>
            <p class="text-2xl font-bold text-gray-900">{{ strtoupper($entity->country) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Cierre Fiscal</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $entity->fiscal_year_end ?? 'No definido' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Documentos</h3>
                <span class="text-2xl font-bold text-purple-600">{{ $documentsCount }}</span>
            </div>
            <p class="text-gray-600 mb-4">Total de documentos procesados para esta entidad</p>
            <a href="{{ route('documents.index') }}?entity={{ $entity->id }}" class="text-purple-600 hover:text-purple-700 font-medium">
                Ver todos los documentos →
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Transacciones</h3>
                <span class="text-2xl font-bold text-green-600">{{ $transactionsCount }}</span>
            </div>
            <p class="text-gray-600 mb-4">Total de transacciones registradas</p>
            <a href="{{ route('transactions.index') }}?entity={{ $entity->id }}" class="text-purple-600 hover:text-purple-700 font-medium">
                Ver todas las transacciones →
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Adicional</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-600">Creada el</dt>
                <dd class="text-gray-900">{{ $entity->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Última actualización</dt>
                <dd class="text-gray-900">{{ $entity->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
