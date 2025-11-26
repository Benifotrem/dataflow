@extends('layouts.app')

@section('page-title', 'Calendario Fiscal')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📅 Calendario Fiscal</h1>
            <p class="text-gray-600 mt-1">Gestiona y programa tus eventos fiscales importantes</p>
        </div>
        <a href="{{ route('fiscal-events.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition flex items-center gap-2 shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Evento
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
        {{ session('warning') }}
    </div>
    @endif

    {{-- Upcoming Events Card --}}
    @if($upcomingEvents->count() > 0)
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-6">
        <h2 class="text-xl font-bold text-purple-900 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Próximos Eventos (30 días)
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($upcomingEvents as $event)
            <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-{{ $event->event_color }}-500">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-gray-900">{{ $event->title }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full bg-{{ $event->event_color }}-100 text-{{ $event->event_color }}-800">
                        {{ $event->event_type_name }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ $event->event_date->format('d/m/Y') }}</p>
                <div class="flex items-center gap-2">
                    @if($event->days_until <= 3)
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800 font-semibold">
                        ¡{{ $event->days_until }} días!
                    </span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                        {{ $event->days_until }} días
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Filtros</h3>
        <form method="GET" action="{{ route('fiscal-events.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evento</label>
                <select name="type" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="vat_liquidation" {{ request('type') === 'vat_liquidation' ? 'selected' : '' }}>Liquidación IVA</option>
                    <option value="income_tax" {{ request('type') === 'income_tax' ? 'selected' : '' }}>Impuesto Renta</option>
                    <option value="tax_declaration" {{ request('type') === 'tax_declaration' ? 'selected' : '' }}>Declaración Impuestos</option>
                    <option value="social_security" {{ request('type') === 'social_security' ? 'selected' : '' }}>Seguridad Social</option>
                    <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Próximos</option>
                    <option value="past" {{ request('status') === 'past' ? 'selected' : '' }}>Pasados</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Activo</label>
                <select name="active" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex-1">
                    Filtrar
                </button>
                <a href="{{ route('fiscal-events.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Events Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días de Aviso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                <tr class="{{ $event->is_active ? '' : 'bg-gray-50 opacity-60' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                @if($event->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($event->description, 60) }}</div>
                                @endif
                                @if($event->is_default)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                    Evento por defecto
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $event->event_color }}-100 text-{{ $event->event_color }}-800">
                            {{ $event->event_type_name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $event->event_date->format('d/m/Y') }}</div>
                        @if(!$event->is_past)
                        <div class="text-xs text-gray-500">{{ $event->days_until }} días restantes</div>
                        @else
                        <div class="text-xs text-red-500">Pasado</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $event->notification_days_before }} días antes
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($event->is_active)
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                        @else
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Inactivo
                        </span>
                        @endif
                        @if($event->is_recurring)
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 ml-1">
                            Recurrente
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('fiscal-events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900">
                                Editar
                            </a>
                            <form action="{{ route('fiscal-events.toggle-active', $event) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                    {{ $event->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            @if(!$event->is_default)
                            <form action="{{ route('fiscal-events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este evento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium">No hay eventos fiscales registrados</p>
                            <p class="text-xs mt-1">Crea tu primer evento fiscal para comenzar</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($events->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection
