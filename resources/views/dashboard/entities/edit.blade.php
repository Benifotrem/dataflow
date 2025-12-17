@extends('layouts.app')

@section('page-title', 'Editar Entidad')

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
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Editar Entidad: {{ $entity->name }}</h2>

        <form action="{{ route('entities.update', $entity) }}" method="POST" class="space-y-6" id="entityForm">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre de la Entidad *
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $entity->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                    NIF / CIF / RFC *
                </label>
                <input type="text" name="tax_id" id="tax_id" required
                       value="{{ old('tax_id', $entity->tax_id) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
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
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ old('country', $entity->country) === $code ? 'selected' : '' }}>
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
                       value="{{ old('fiscal_year_end', $entity->fiscal_year_end) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                       placeholder="MM-DD (Ej: 12-31)">
                <p class="mt-1 text-sm text-gray-500">Formato: MM-DD</p>
                @error('fiscal_year_end')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" id="submitBtn"
                        class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="btnText">Guardar Cambios</span>
                    <span id="btnLoading" class="hidden">
                        <svg class="animate-spin inline-block w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
                <a href="{{ route('entities.index') }}"
                   class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition text-center">
                    Cancelar
                </a>
            </div>
        </form>

        <script>
            // Protección contra doble submit
            document.getElementById('entityForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnLoading = document.getElementById('btnLoading');

                // Si ya está deshabilitado, cancelar el submit
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }

                // Deshabilitar botón y mostrar loading
                submitBtn.disabled = true;
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');

                // Deshabilitar todos los inputs del formulario
                const inputs = this.querySelectorAll('input, select, button');
                inputs.forEach(input => input.disabled = true);
            });
        </script>

        @if($entity->documents()->count() === 0 && $entity->transactions()->count() === 0)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-red-600 mb-4">Zona Peligrosa</h3>
            <form action="{{ route('entities.destroy', $entity) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta entidad? Esta acción no se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                    Eliminar Entidad
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
