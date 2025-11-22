@extends('layouts.app')

@section('page-title', 'Extractos Bancarios')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Extractos Bancarios</h2>
            <p class="text-sm text-gray-600 mt-1">⚠️ Retención automática de 60 días desde fin de mes</p>
        </div>
        <a href="{{ route('bank-statements.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            + Cargar Extracto
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Período</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expira</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bankStatements as $statement)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $statement->entity->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $statement->period_start->format('d/m/Y') }} - {{ $statement->period_end->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $statement->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm {{ $statement->willExpireSoon() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        {{ $statement->expires_at ? $statement->expires_at->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <form action="{{ route('bank-statements.destroy', $statement) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este extracto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <p class="text-gray-500 mb-3">No hay extractos bancarios cargados</p>
                        <a href="{{ route('bank-statements.create') }}" class="text-purple-600 hover:text-purple-700 font-medium">Cargar tu primer extracto →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $bankStatements->links() }}
</div>
@endsection
