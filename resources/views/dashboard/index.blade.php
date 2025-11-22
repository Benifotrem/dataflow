@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
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

        {{-- Uso de IA --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Uso de IA</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $aiStats['used'] }}<span class="text-lg text-gray-500">/{{ $aiStats['limit'] }}</span></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: {{ min($aiStats['percentage'], 100) }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ $aiStats['percentage'] }}% utilizado</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
