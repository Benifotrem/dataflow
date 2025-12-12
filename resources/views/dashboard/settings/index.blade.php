@extends('layouts.app')

@section('page-title', 'Configuración')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Información de la Cuenta --}}
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Información de la Cuenta</h2>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-600">Usuario</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Email</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Organización</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $tenant->name ?? 'No asignada' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Plan</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($tenant->subscription_plan ?? 'Free') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Estado</dt>
                <dd class="mt-1">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tenant->status === 'active' ? 'Activa' : 'Inactiva' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Miembro desde</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Configuración de Bot de Telegram --}}
    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Bot de Telegram</h2>
                <p class="text-sm text-gray-600 mt-1">Gestiona la integración con Telegram para recibir facturas</p>
            </div>
            @if($user->hasTelegramLinked())
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    ✓ Vinculado
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    No vinculado
                </span>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-900 mb-2">📱 Cómo vincular el Bot de Telegram</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-purple-800">
                    <li>Abre Telegram y busca el bot: <code class="bg-purple-100 px-2 py-1 rounded">{{ config('services.telegram.bot_username', '@DataflowBot') }}</code></li>
                    <li>Envía el comando <code class="bg-purple-100 px-2 py-1 rounded">/start</code> al bot</li>
                    <li>Envía el comando <code class="bg-purple-100 px-2 py-1 rounded">/link</code> para iniciar la vinculación</li>
                    <li>El bot te proporcionará tu Telegram ID. Contáctalo con el administrador para completar la vinculación</li>
                    <li>Una vez vinculado, envía fotos o PDFs de tus facturas y el sistema las procesará automáticamente</li>
                </ol>
            </div>

            @if($user->hasTelegramLinked())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">✅ Cuenta vinculada correctamente</h3>
                    <div class="space-y-1 text-sm text-green-800">
                        <p>📱 <b>Telegram ID:</b> <code class="bg-green-100 px-2 py-1 rounded">{{ $user->telegram_id }}</code></p>
                        @if($user->telegram_username)
                            <p>👤 <b>Username:</b> <code class="bg-green-100 px-2 py-1 rounded">@{{ $user->telegram_username }}</code></p>
                        @endif
                        @if($user->telegram_linked_at)
                            <p>📅 <b>Vinculado desde:</b> {{ $user->telegram_linked_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-200 flex items-center justify-between">
                        <p class="text-sm text-green-800">
                            💬 Ahora puedes enviar facturas directamente al bot y se procesarán automáticamente.
                        </p>
                        <form action="{{ route('settings.telegram.unlink') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas desvincular tu cuenta de Telegram?');">
                            @csrf
                            <button
                                type="submit"
                                class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition text-sm font-medium"
                            >
                                Desvincular
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-semibold text-yellow-900 mb-3">⚠️ Bot no vinculado</h3>
                    <p class="text-sm text-yellow-800 mb-4">
                        Sigue los pasos anteriores para obtener tu Telegram ID, luego ingrésalo aquí:
                    </p>

                    <form action="{{ route('settings.telegram.link') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="telegram_id" class="block text-sm font-medium text-yellow-900 mb-2">
                                    Tu Telegram ID
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        name="telegram_id"
                                        id="telegram_id"
                                        placeholder="Ejemplo: 123456789"
                                        class="flex-1 px-4 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white"
                                        required
                                    >
                                    <button
                                        type="submit"
                                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition whitespace-nowrap"
                                    >
                                        Vincular
                                    </button>
                                </div>
                                @error('telegram_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-yellow-700">
                                💡 El bot te proporcionará este ID cuando envíes /link. También puedes usar @userinfobot en Telegram para obtenerlo.
                            </p>
                        </div>
                    </form>
                </div>
            @endif

            <div class="pt-4">
                <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Preferencias --}}
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Preferencias</h2>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Idioma --}}
                <div>
                    <label for="locale" class="block text-sm font-medium text-gray-700 mb-2">
                        Idioma
                    </label>
                    <select
                        name="locale"
                        id="locale"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="es" selected>Español</option>
                        <option value="en">English</option>
                        <option value="pt">Português</option>
                    </select>
                </div>

                {{-- Notificaciones --}}
                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="notifications_enabled"
                        id="notifications_enabled"
                        class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500"
                        checked
                    >
                    <label for="notifications_enabled" class="text-sm font-medium text-gray-700">
                        Recibir notificaciones por email
                    </label>
                </div>

                {{-- Botón Guardar --}}
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition"
                    >
                        Guardar Preferencias
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
