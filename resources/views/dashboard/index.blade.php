@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Document Limit Alerts --}}
    @if($documentStats['at_limit'])
        <div class="bg-red-50 border-l-4 border-red-500 p-4">
            <div class="flex">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold">Límite de Documentos Alcanzado</h3>
                    <p class="text-red-700 mt-1">Has alcanzado el límite de {{ $documentStats['limit'] }} documentos para este mes. No podrás procesar más documentos hasta el próximo mes o hasta que actualices tu plan.</p>
                    <div class="mt-3">
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Actualizar Plan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($documentStats['near_limit'])
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
            <div class="flex">
                <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-yellow-800 font-semibold">Acercándote al Límite de Documentos</h3>
                    <p class="text-yellow-700 mt-1">Has utilizado {{ $documentStats['used'] }} de {{ $documentStats['limit'] }} documentos este mes ({{ $documentStats['percentage'] }}%). Considera actualizar tu plan para evitar interrupciones.</p>
                    <div class="mt-3">
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                            Ver Planes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Documentos Totales --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Documentos Totales</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['documents_total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            @if($stats['documents_pending'] > 0)
                <p class="text-sm text-orange-600 mt-2">{{ $stats['documents_pending'] }} pendientes</p>
            @endif
        </div>

        {{-- Transacciones --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Transacciones</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['transactions_total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ $stats['transactions_this_month'] }} este mes</p>
        </div>

        {{-- Entidades --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Entidades Fiscales</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['entities_total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            @if($stats['entities_total'] === 0)
                <a href="{{ route('entities.create') }}" class="text-sm text-purple-600 hover:text-purple-700 mt-2 inline-block">
                    Crear primera entidad →
                </a>
            @endif
        </div>

        {{-- Uso de Documentos --}}
        <div class="bg-white rounded-lg shadow p-6 {{ $documentStats['at_limit'] ? 'ring-2 ring-red-500' : ($documentStats['near_limit'] ? 'ring-2 ring-yellow-500' : '') }}">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Documentos Este Mes</p>
                    <p class="text-3xl font-bold {{ $documentStats['warning_level'] === 'danger' ? 'text-red-600' : ($documentStats['warning_level'] === 'warning' ? 'text-yellow-600' : 'text-gray-900') }}">
                        {{ $documentStats['used'] }}<span class="text-lg text-gray-500">/{{ $documentStats['limit'] }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 {{ $documentStats['warning_level'] === 'danger' ? 'bg-red-100' : ($documentStats['warning_level'] === 'warning' ? 'bg-yellow-100' : 'bg-green-100') }} rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $documentStats['warning_level'] === 'danger' ? 'text-red-600' : ($documentStats['warning_level'] === 'warning' ? 'text-yellow-600' : 'text-green-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="{{ $documentStats['warning_level'] === 'danger' ? 'bg-gradient-to-r from-red-500 to-red-600' : ($documentStats['warning_level'] === 'warning' ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-green-500 to-green-600') }} h-2.5 rounded-full transition-all duration-300" style="width: {{ min($documentStats['percentage'], 100) }}%"></div>
            </div>
            <p class="text-sm {{ $documentStats['warning_level'] === 'danger' ? 'text-red-600 font-medium' : ($documentStats['warning_level'] === 'warning' ? 'text-yellow-600 font-medium' : 'text-gray-600') }} mt-2">
                {{ $documentStats['percentage'] }}% utilizado
                @if($documentStats['at_limit'])
                    <span class="block mt-1 font-semibold">¡Límite alcanzado!</span>
                @elseif($documentStats['percentage'] >= 90)
                    <span class="block mt-1">Quedan {{ $documentStats['limit'] - $documentStats['used'] }} documentos</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($documentStats['at_limit'])
                <div class="flex items-center gap-3 p-4 border-2 border-dashed border-red-300 rounded-lg bg-red-50 opacity-60 cursor-not-allowed">
                    <div class="w-10 h-10 bg-red-400 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-red-700">Subir Documento</p>
                        <p class="text-sm text-red-600">Límite alcanzado</p>
                    </div>
                </div>
            @else
                <a href="{{ route('documents.create') }}" class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Subir Documento</p>
                        <p class="text-sm text-gray-600">Factura, recibo, extracto...</p>
                    </div>
                </a>
            @endif

            <a href="{{ route('transactions.create') }}" class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Nueva Transacción</p>
                    <p class="text-sm text-gray-600">Registrar manualmente</p>
                </div>
            </a>

            <a href="{{ route('entities.index') }}" class="flex items-center gap-3 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Gestionar Entidades</p>
                    <p class="text-sm text-gray-600">Ver y configurar</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Documentos Recientes --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Documentos Recientes</h2>
                    <a href="{{ route('documents.index') }}" class="text-sm text-purple-600 hover:text-purple-700">Ver todos →</a>
                </div>
            </div>
            <div class="p-6">
                @forelse($recentDocuments as $document)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $document->file_name ?? 'Sin nombre' }}</p>
                                <p class="text-xs text-gray-500">{{ $document->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $document->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $document->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $document->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ ucfirst($document->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-sm mb-3">No hay documentos aún</p>
                        <a href="{{ route('documents.create') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            Subir tu primer documento →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Transacciones Recientes --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Transacciones Recientes</h2>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-purple-600 hover:text-purple-700">Ver todas →</a>
                </div>
            </div>
            <div class="p-6">
                @forelse($recentTransactions as $transaction)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description ?? 'Sin descripción' }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->date->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-500 text-sm mb-3">No hay transacciones aún</p>
                        <a href="{{ route('transactions.create') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            Crear tu primera transacción →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
