@extends('layouts.app')

@section('page-title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Información del Perfil --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Información Personal</h2>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" disabled
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                    <input type="text" value="{{ $user->tenant->name ?? 'N/A' }}" disabled
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Cambiar Contraseña --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Cambiar Contraseña</h2>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                    <input type="password" id="current_password" name="current_password"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                    <input type="password" id="password" name="password"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Mínimo 8 caracteres</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>

    {{-- Información del Sistema --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Información del Sistema</h2>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Miembro desde:</span>
                <span class="font-medium ml-2">{{ $user->created_at->format('d/m/Y') }}</span>
            </div>
            <div>
                <span class="text-gray-600">Última actualización:</span>
                <span class="font-medium ml-2">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
