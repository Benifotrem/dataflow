@extends('layouts.app')

@section('page-title', 'Gestión de Clientes')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Clientes</h1>
            <p class="text-gray-600 mt-1">Administra todos los clientes y sus suscripciones</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>En Prueba</option>
                    <option value="subscribed" {{ request('status') === 'subscribed' ? 'selected' : '' }}>Suscritos</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirados</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Tenants Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prueba/Suscripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $tenant->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tenant->users->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($tenant->trial_ends_at && $tenant->trial_ends_at > now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    En Prueba
                                </span>
                            @elseif($tenant->subscription_ends_at && $tenant->subscription_ends_at > now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Expirado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($tenant->trial_ends_at)
                                <div>Prueba: {{ $tenant->trial_ends_at->format('d/m/Y') }}</div>
                                @if($tenant->trial_ends_at > now())
                                    <div class="text-xs text-gray-400">{{ $tenant->trial_ends_at->diffForHumans() }}</div>
                                @endif
                            @endif
                            @if($tenant->subscription_ends_at)
                                <div class="mt-1">Suscripción: {{ $tenant->subscription_ends_at->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $tenant->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-purple-600 hover:text-purple-900">Ver</a>

                                @if($tenant->trial_ends_at && $tenant->trial_ends_at <= now())
                                    <form action="{{ route('admin.tenants.reactivate', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="trial_days" value="30">
                                        <button type="submit" class="text-green-600 hover:text-green-900">Reactivar</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.tenants.suspend', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas suspender este cliente?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">Suspender</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="mt-2 text-sm">No se encontraron clientes</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
