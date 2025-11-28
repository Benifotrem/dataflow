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

    {{-- Configuración de Telegram --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Configuración de Telegram</h2>

        @if($user->hasTelegramLinked())
            {{-- Cuenta Vinculada --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-green-900 mb-1">✅ Cuenta de Telegram Vinculada</h3>
                        <p class="text-sm text-green-700 mb-2">
                            <strong>Usuario:</strong> {{ $user->telegram_username ? '@' . $user->telegram_username : 'Usuario de Telegram' }}
                        </p>
                        <p class="text-sm text-green-700 mb-3">
                            <strong>Vinculado desde:</strong> {{ $user->telegram_linked_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-sm text-green-700 mb-3">
                            Ahora puedes enviar facturas directamente desde Telegram al bot <code class="bg-green-200 px-2 py-1 rounded">@dataflow_guaraniappstore_bot</code>
                        </p>
                        <form action="{{ route('profile.telegram.unlink') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas desvincular tu cuenta de Telegram?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                                Desvincular Telegram
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Cuenta No Vinculada --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-900 mb-2">📱 Vincula tu Cuenta de Telegram</h3>
                        <p class="text-sm text-blue-700 mb-4">
                            Vincula tu cuenta para enviar facturas desde Telegram y recibir notificaciones instantáneas.
                        </p>

                        @if(session('telegram_code'))
                            {{-- Mostrar Código Generado --}}
                            <div class="bg-white border-2 border-blue-400 rounded-lg p-4 mb-4">
                                <p class="text-sm text-gray-700 mb-2"><strong>Tu código de vinculación:</strong></p>
                                <div class="flex items-center gap-3 mb-3">
                                    <code class="text-2xl font-mono font-bold text-blue-600 bg-blue-100 px-4 py-2 rounded">{{ session('telegram_code') }}</code>
                                    <button onclick="copyToClipboard('{{ session('telegram_code') }}')" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition text-sm">
                                        Copiar
                                    </button>
                                </div>
                                <p class="text-xs text-gray-600 mb-2">⏱️ Este código expira en 15 minutos</p>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mt-3">
                                    <p class="text-sm text-gray-700 font-semibold mb-2">Pasos para vincular:</p>
                                    <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                                        <li>Abre Telegram y busca <code class="bg-gray-200 px-2 py-0.5 rounded">@dataflow_guaraniappstore_bot</code></li>
                                        <li>Inicia conversación con el bot presionando <strong>START</strong></li>
                                        <li>Envía el código <code class="bg-blue-200 px-2 py-0.5 rounded font-mono">{{ session('telegram_code') }}</code> al bot</li>
                                        <li>¡Listo! Recibirás confirmación cuando se vincule</li>
                                    </ol>
                                </div>
                            </div>
                        @else
                            {{-- Generar Código --}}
                            <form action="{{ route('profile.telegram.generate-code') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Generar Código de Vinculación
                                </button>
                            </form>
                        @endif

                        <div class="mt-4 pt-4 border-t border-blue-200">
                            <p class="text-xs text-blue-700 mb-2"><strong>¿Qué puedes hacer con Telegram?</strong></p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>✅ Envía facturas en PDF o foto directamente desde tu móvil</li>
                                <li>✅ Recibe notificaciones instantáneas cuando se procesen</li>
                                <li>✅ Consulta el estado de tus documentos</li>
                                <li>✅ Gestiona tu suscripción y pagos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Código copiado al portapapeles: ' + text);
    }, function(err) {
        console.error('Error al copiar: ', err);
    });
}
</script>
@endpush

@endsection
