@extends('layouts.app')

@section('page-title', 'Nueva Entidad Fiscal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('entities.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Entidades
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Crear Nueva Entidad Fiscal</h2>

        <form action="{{ route('entities.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre de la Entidad *
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: Mi Empresa S.L.">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                    NIF / CIF / RFC *
                </label>
                <input type="text" name="tax_id" id="tax_id" required
                       value="{{ old('tax_id') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Ej: B12345678">
                @error('tax_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                    País *
                </label>
                <select name="country" id="country" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Selecciona un país</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ old('country') === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fiscal_year_end" class="block text-sm font-medium text-gray-700 mb-2">
                    Fin de Año Fiscal (Opcional)
                </label>
                <input type="text" name="fiscal_year_end" id="fiscal_year_end"
                       value="{{ old('fiscal_year_end', '12-31') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                       placeholder="MM-DD (Ej: 12-31)">
                <p class="mt-1 text-sm text-gray-500">Formato: MM-DD. Por defecto 31 de diciembre.</p>
                @error('fiscal_year_end')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit"
                        class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Crear Entidad
                </button>
                <a href="{{ route('entities.index') }}"
                   class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
