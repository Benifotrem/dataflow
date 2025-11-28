@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Configuración del Perfil</h1>
            <p class="text-gray-600 mt-2">Configura la información básica de tu cuenta</p>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-red-800 font-medium">Error</h3>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.settings.tenant-profile.update') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Información Básica --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información Básica</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Nombre de la Empresa/Cuenta --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Empresa/Cuenta *
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $tenant->name) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    {{-- Email de Contacto --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Contacto *
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $tenant->email) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            {{-- Configuración Regional --}}
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Configuración Regional</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- País --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            País *
                        </label>
                        <select
                            name="country_code"
                            id="country_code"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            onchange="updateCurrency()"
                        >
                            <option value="">Selecciona un país</option>
                            @foreach($countries as $code => $country)
                                <option
                                    value="{{ $code }}"
                                    data-currency="{{ $country['currency']['code'] ?? '' }}"
                                    {{ old('country_code', $tenant->country_code) == $code ? 'selected' : '' }}
                                >
                                    {{ $country['name'] }} ({{ $country['currency']['name'] ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">La moneda se actualizará automáticamente según el país</p>
                    </div>

                    {{-- Moneda --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Moneda *
                        </label>
                        <select
                            name="currency_code"
                            id="currency_code"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            @foreach($currencies as $code => $currency)
                                <option
                                    value="{{ $code }}"
                                    {{ old('currency_code', $tenant->currency_code) == $code ? 'selected' : '' }}
                                >
                                    {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Puedes seleccionar cualquier moneda disponible</p>
                    </div>
                </div>
            </div>

            {{-- Información Actual --}}
            <div class="border-t pt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-900 mb-2">Configuración Actual:</h3>
                <div class="grid md:grid-cols-3 gap-4 text-sm text-blue-700">
                    <div>
                        <span class="font-medium">País:</span> {{ App\Services\CurrencyService::getCountryName($tenant->country_code) ?? $tenant->country_code }}
                    </div>
                    <div>
                        <span class="font-medium">Moneda:</span> {{ $tenant->currency_code }}
                    </div>
                    <div>
                        <span class="font-medium">Símbolo:</span> {{ App\Services\CurrencyService::getCurrencyByCode($tenant->currency_code)['symbol'] ?? '' }}
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('dashboard.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Volver al Panel
                </a>
                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>

        {{-- Info sobre Guaraní --}}
        @if($tenant->currency_code === 'PYG' || $tenant->country_code === 'PY')
            <div class="mt-6 bg-green-50 border-l-4 border-green-500 p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-green-800 font-medium">Soporte de Guaraní Paraguayo (₲)</h4>
                        <p class="text-sm text-green-700 mt-1">
                            Dataflow tiene soporte completo para el Guaraní paraguayo. Los montos se mostrarán sin decimales según las convenciones locales.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function updateCurrency() {
        const countrySelect = document.getElementById('country_code');
        const currencySelect = document.getElementById('currency_code');
        const selectedOption = countrySelect.options[countrySelect.selectedIndex];
        const currencyCode = selectedOption.getAttribute('data-currency');

        if (currencyCode) {
            currencySelect.value = currencyCode;
        }
    }
</script>
@endsection
