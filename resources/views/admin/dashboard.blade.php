@extends('layouts.app')

@section('page-title', 'Panel de Administración')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Panel de Administración</h2>
        <p class="text-gray-600 mt-1">Vista general del sistema</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Tenants Totales</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</p>
            <p class="text-sm text-green-600 mt-2">{{ $stats['active_tenants'] }} activos</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Usuarios</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Documentos Totales</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_documents'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Documentos Este Mes</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['documents_this_month'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tenants Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">País</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentTenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $tenant->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ strtoupper($tenant->type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tenant->country }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $tenant->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
