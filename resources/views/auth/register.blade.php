@extends('layouts.landing')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Crea tu cuenta en Contaplus
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Prueba gratis durante 14 días, sin tarjeta de crédito
            </p>
        </div>

        <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                {{-- Información de la Empresa --}}
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Empresa</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">
                                Nombre de la Empresa *
                            </label>
                            <input id="company_name" name="company_name" type="text" required
                                   value="{{ old('company_name') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label for="tenant_type" class="block text-sm font-medium text-gray-700">
                                Tipo de Cuenta *
                            </label>
                            <select id="tenant_type" name="tenant_type" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <option value="b2c" {{ old('tenant_type') === 'b2c' ? 'selected' : '' }}>Plan Básico (PyME/Individual)</option>
                                <option value="b2b" {{ old('tenant_type') === 'b2b' ? 'selected' : '' }}>Plan Avanzado (Despacho/Contador)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Puedes cambiar de plan después</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="country" class="block text-sm font-medium text-gray-700">
                            País *
                        </label>
                        <select id="country" name="country" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Selecciona tu país</option>
                            <option value="es">España</option>
                            <option value="mx">México</option>
                            <option value="ar">Argentina</option>
                            <option value="co">Colombia</option>
                            <option value="cl">Chile</option>
                            <option value="pe">Perú</option>
                            <option value="ec">Ecuador</option>
                            <option value="ve">Venezuela</option>
                            <option value="uy">Uruguay</option>
                            <option value="py">Paraguay</option>
                            <option value="bo">Bolivia</option>
                            <option value="cr">Costa Rica</option>
                            <option value="pa">Panamá</option>
                            <option value="gt">Guatemala</option>
                            <option value="hn">Honduras</option>
                            <option value="sv">El Salvador</option>
                            <option value="ni">Nicaragua</option>
                            <option value="do">República Dominicana</option>
                            <option value="cu">Cuba</option>
                        </select>
                    </div>
                </div>

                {{-- Información del Usuario --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tu Información</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre Completo *
                            </label>
                            <input id="name" name="name" type="text" required
                                   value="{{ old('name') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email *
                            </label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   value="{{ old('email') }}"
                                   class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Contraseña *
                                </label>
                                <input id="password" name="password" type="password" autocomplete="new-password" required
                                       class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                                <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirmar Contraseña *
                                </label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                       class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">
                            Acepto los <a href="{{ route('terms') }}" target="_blank" class="text-purple-600 hover:text-purple-500">Términos y Condiciones</a>
                            y la <a href="{{ route('privacy') }}" target="_blank" class="text-purple-600 hover:text-purple-500">Política de Privacidad</a>
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white gradient-bg hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Crear Cuenta Gratis
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">¿Ya tienes cuenta?</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-500 font-medium">
                        Iniciar sesión →
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center justify-center gap-8 text-sm text-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>14 días gratis</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Sin tarjeta</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Cancela cuando quieras</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
