@extends('layouts.app')

@section('page-title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Información del Perfil --}}
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Información del Perfil</h2>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tenant --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Organización
                    </label>
                    <input
                        type="text"
                        value="{{ $user->tenant->name ?? 'No asignada' }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                        disabled
                    >
                    <p class="mt-1 text-sm text-gray-500">Este campo no se puede modificar</p>
                </div>

                {{-- Rol --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rol
                    </label>
                    <input
                        type="text"
                        value="{{ ucfirst($user->role ?? 'Usuario') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                        disabled
                    >
                    <p class="mt-1 text-sm text-gray-500">Este campo no se puede modificar</p>
                </div>

                {{-- Botón Guardar --}}
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition"
                    >
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Cambiar Contraseña --}}
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cambiar Contraseña</h2>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Contraseña Actual --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña Actual
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nueva Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva Contraseña
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nueva Contraseña
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        required
                    >
                </div>

                {{-- Botón Cambiar Contraseña --}}
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition"
                    >
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
