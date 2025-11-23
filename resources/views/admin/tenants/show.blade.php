@extends('layouts.app')

@section('page-title', 'Detalles del Cliente')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.tenants.index') }}" class="text-purple-600 hover:text-purple-900 text-sm mb-2 inline-block">
                ← Volver a clientes
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $tenant->email }}</p>
        </div>
        <div>
            @if($tenant->trial_ends_at && $tenant->trial_ends_at > now())
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">En Prueba</span>
            @elseif($tenant->subscription_ends_at && $tenant->subscription_ends_at > now())
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
            @else
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">Expirado</span>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Usuarios</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['users_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Documentos</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['documents_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Transacciones</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['transactions_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600">Días Restantes</div>
            <div class="text-3xl font-bold {{ $stats['days_remaining'] > 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ $stats['days_remaining'] }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Account Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Detalles de la Cuenta</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Registro</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                @if($tenant->trial_ends_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Período de Prueba</dt>
                    <dd class="text-sm text-gray-900">
                        Hasta {{ $tenant->trial_ends_at->format('d/m/Y') }}
                        <span class="text-gray-500">({{ $tenant->trial_ends_at->diffForHumans() }})</span>
                    </dd>
                </div>
                @endif
                @if($tenant->subscription_ends_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Suscripción</dt>
                    <dd class="text-sm text-gray-900">
                        Hasta {{ $tenant->subscription_ends_at->format('d/m/Y') }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Acciones Rápidas</h2>

            {{-- Extend Trial --}}
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Extender Período de Prueba</h3>
                <form action="{{ route('admin.tenants.extend-trial', $tenant) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="days" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="7">7 días</option>
                        <option value="15">15 días</option>
                        <option value="30">30 días</option>
                        <option value="60">60 días</option>
                        <option value="90">90 días</option>
                    </select>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                        Extender
                    </button>
                </form>
            </div>

            {{-- Reactivate --}}
            @if($tenant->trial_ends_at && $tenant->trial_ends_at <= now())
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Reactivar Cuenta</h3>
                <form action="{{ route('admin.tenants.reactivate', $tenant) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="trial_days" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="7">7 días</option>
                        <option value="15">15 días</option>
                        <option value="30">30 días</option>
                    </select>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                        Reactivar
                    </button>
                </form>
            </div>
            @endif

            {{-- Suspend --}}
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-red-700 mb-2">Zona de Peligro</h3>
                <form action="{{ route('admin.tenants.suspend', $tenant) }}" method="POST" onsubmit="return confirm('¿Estás seguro de suspender este cliente? Perderá acceso inmediatamente.')">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                        Suspender Cliente
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Esta acción suspende el acceso del cliente inmediatamente</p>
            </div>
        </div>
    </div>

    {{-- Users List --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Usuarios ({{ $tenant->users->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tenant->users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
