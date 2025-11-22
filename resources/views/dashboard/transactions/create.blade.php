@extends('layouts.app')

@section('page-title', 'Nueva Transacción')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('transactions.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Transacciones
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Registrar Nueva Transacción</h2>

        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-2">Entidad Fiscal *</label>
                <select name="entity_id" id="entity_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Selecciona una entidad</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                    @endforeach
                </select>
                @error('entity_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                    <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="income">Ingreso</option>
                        <option value="expense" selected>Gasto</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                    <input type="date" name="date" id="date" required value="{{ old('date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Importe *</label>
                    <input type="number" name="amount" id="amount" required step="0.01" min="0" value="{{ old('amount') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="0.00">
                    @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Moneda *</label>
                    <select name="currency" id="currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="EUR">EUR (€)</option>
                        <option value="USD">USD ($)</option>
                        <option value="MXN">MXN ($)</option>
                        <option value="ARS">ARS ($)</option>
                        <option value="COP">COP ($)</option>
                    </select>
                    @error('currency')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                <input type="text" name="description" id="description" required value="{{ old('description') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Ej: Pago de servicios">
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoría (Opcional)</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Ej: Servicios, Nómina, etc.">
                @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">Crear Transacción</button>
                <a href="{{ route('transactions.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition text-center">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
