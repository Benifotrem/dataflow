@extends('layouts.app')

@section('page-title', 'Entidades Fiscales')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Entidades Fiscales</h2>
            <p class="text-gray-600 mt-1">Gestiona las empresas o actividades económicas</p>
        </div>
        <a href="{{ route('entities.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            + Nueva Entidad
        </a>
    </div>

    @if($entities->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay entidades fiscales</h3>
            <p class="text-gray-600 mb-6">Crea tu primera entidad para comenzar a gestionar documentos y transacciones</p>
            <a href="{{ route('entities.create') }}" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Primera Entidad
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($entities as $entity)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $entity->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $entity->tax_id }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">
                            {{ strtoupper($entity->country) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $entity->documents()->count() }} documentos
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            {{ $entity->transactions()->count() }} transacciones
                        </div>
                        @if($entity->fiscal_year_end)
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Cierre fiscal: {{ $entity->fiscal_year_end }}
                        </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('entities.show', $entity) }}" class="flex-1 text-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Ver Detalles
                        </a>
                        <a href="{{ route('entities.edit', $entity) }}" class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
