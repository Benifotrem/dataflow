@extends('layouts.app')

@section('page-title', 'Transacciones')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Transacciones</h2>
        <a href="{{ route('transactions.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            + Nueva Transacción
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Importe</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $transaction->description }}</div>
                        @if($transaction->category)
                        <div class="text-xs text-gray-500">{{ $transaction->category }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->entity->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $transaction->type === 'income' ? 'Ingreso' : 'Gasto' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-purple-600 hover:text-purple-900 mr-3">Editar</a>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta transacción?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-gray-500 mb-3">No hay transacciones registradas</p>
                        <a href="{{ route('transactions.create') }}" class="text-purple-600 hover:text-purple-700 font-medium">Crear tu primera transacción →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $transactions->links() }}
</div>
@endsection
