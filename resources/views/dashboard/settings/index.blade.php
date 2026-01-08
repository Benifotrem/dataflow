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
                <h3 class="font-semibold text-purple-900 mb-2">📱 Cómo obtener tu Telegram ID</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-purple-800">
                    <li>Abre Telegram y busca el bot: <code class="bg-purple-100 px-2 py-1 rounded">{{ config('services.telegram.bot_username', '@DataflowBot') }}</code></li>
                    <li>Envía el comando <code class="bg-purple-100 px-2 py-1 rounded">/start</code> al bot</li>
                    <li>Envía el comando <code class="bg-purple-100 px-2 py-1 rounded">/link</code> - el bot te mostrará tu Telegram ID</li>
                    <li>Copia el número y pégalo en el formulario de abajo</li>
                    <li><b>Alternativa:</b> Busca <code class="bg-purple-100 px-2 py-1 rounded">@userinfobot</code> en Telegram y envía cualquier mensaje para obtener tu ID</li>
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

    {{-- Zona Peligrosa --}}
    <div class="bg-white rounded-lg shadow p-8 border-2 border-red-200">
        <h2 class="text-2xl font-bold text-red-900 mb-2">⚠️ Zona Peligrosa</h2>
        <p class="text-sm text-gray-600 mb-6">Esta acción es irreversible</p>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Cancelar mi cuenta</h3>
                    <p class="text-sm text-red-800 mb-4">
                        Una vez que canceles tu cuenta, <strong>toda tu información será eliminada permanentemente</strong>.
                        Esto incluye tus documentos, facturas procesadas, reportes y toda la información asociada a tu cuenta.
                    </p>
                    <button
                        type="button"
                        onclick="openCancellationModal()"
                        class="bg-red-600 text-white px-6 py-2.5 rounded-lg hover:bg-red-700 transition font-medium"
                    >
                        Cancelar mi cuenta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Cancelación --}}
<div id="cancellationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900">Nos entristece verte partir 😔</h3>
            <p class="text-sm text-gray-600 mt-2">Antes de continuar, nos gustaría entender qué podemos mejorar</p>
        </div>

        <form id="cancellationForm" action="{{ route('account.cancel.request') }}" method="POST" class="p-6">
            @csrf

            {{-- Paso 1: Motivos --}}
            <div id="step1" class="space-y-6">
                <div>
                    <label class="block text-base font-semibold text-gray-900 mb-4">
                        ¿Cuál es el motivo principal de tu decisión? (puedes seleccionar varios)
                    </label>
                    <div class="space-y-3">
                        @foreach(\App\Models\AccountCancellationRequest::getAvailableReasons() as $key => $reason)
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="reasons[]"
                                    value="{{ $key }}"
                                    class="mt-1 w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500"
                                    onchange="handleReasonChange(this)"
                                >
                                <span class="text-gray-800">{{ $reason }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Campo "Otro" condicional --}}
                <div id="otherReasonField" class="hidden">
                    <label for="other_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Por favor, cuéntanos más:
                    </label>
                    <textarea
                        name="other_reason"
                        id="other_reason"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Cuéntanos qué te llevó a esta decisión..."
                    ></textarea>
                </div>

                {{-- Comentarios adicionales --}}
                <div>
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Hay algo más que te gustaría compartir? (opcional)
                    </label>
                    <textarea
                        name="feedback"
                        id="feedback"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Tu opinión es muy valiosa para nosotros..."
                    ></textarea>
                </div>

                <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="closeCancellationModal()"
                        class="px-6 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        onclick="showRetentionOffers()"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-medium"
                    >
                        Continuar
                    </button>
                </div>
            </div>

            {{-- Paso 2: Ofertas de Retención --}}
            <div id="step2" class="hidden space-y-6">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-purple-900 mb-3">💜 Espera, tenemos algo para ti</h4>
                    <p class="text-purple-800 text-sm mb-4">
                        Valoramos mucho tu permanencia. Nos gustaría ofrecerte algo especial antes de que te vayas:
                    </p>
                    <div id="retentionOffers" class="space-y-3">
                        {{-- Se llenará dinámicamente con JavaScript --}}
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="showStep1()"
                        class="px-6 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
                    >
                        Volver
                    </button>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            onclick="proceedWithCancellation()"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition"
                        >
                            No gracias, quiero cancelar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Paso 3: Confirmación Final --}}
            <div id="step3" class="hidden space-y-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-red-900 mb-3">⚠️ Confirmación Final</h4>
                    <p class="text-red-800 mb-4">
                        Estás a punto de cancelar permanentemente tu cuenta. Esta acción <strong>NO SE PUEDE DESHACER</strong>.
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-sm text-red-700 mb-4">
                        <li>Se eliminarán todos tus documentos y facturas</li>
                        <li>Se eliminarán todos tus reportes y estadísticas</li>
                        <li>Perderás acceso inmediato a la plataforma</li>
                        <li>No podrás recuperar esta información</li>
                    </ul>
                    <div class="bg-white border border-red-300 rounded p-4">
                        <label class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                id="confirmCancellation"
                                class="mt-1 w-5 h-5 text-red-600 rounded focus:ring-2 focus:ring-red-500"
                                required
                            >
                            <span class="text-sm text-gray-900">
                                Entiendo que esta acción es irreversible y acepto la eliminación permanente de todos mis datos
                            </span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="closeCancellationModal()"
                        class="px-6 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition"
                    >
                        No, mantener mi cuenta
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium"
                    >
                        Sí, cancelar mi cuenta definitivamente
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openCancellationModal() {
    document.getElementById('cancellationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCancellationModal() {
    document.getElementById('cancellationModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('cancellationForm').reset();
    showStep1();
}

function handleReasonChange(checkbox) {
    const otherField = document.getElementById('otherReasonField');
    const otherCheckbox = document.querySelector('input[name="reasons[]"][value="other"]');

    if (otherCheckbox && otherCheckbox.checked) {
        otherField.classList.remove('hidden');
        document.getElementById('other_reason').required = true;
    } else {
        otherField.classList.add('hidden');
        document.getElementById('other_reason').required = false;
    }
}

function showStep1() {
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.add('hidden');
}

function showRetentionOffers() {
    // Validar que al menos un motivo esté seleccionado
    const selectedReasons = document.querySelectorAll('input[name="reasons[]"]:checked');
    if (selectedReasons.length === 0) {
        alert('Por favor, selecciona al menos un motivo');
        return;
    }

    // Obtener motivos seleccionados
    const reasons = Array.from(selectedReasons).map(cb => cb.value);

    // Obtener ofertas basadas en los motivos
    const offers = getRetentionOffersForReasons(reasons);

    // Renderizar ofertas
    const offersContainer = document.getElementById('retentionOffers');
    offersContainer.innerHTML = offers.map(offer => `
        <label class="flex items-start gap-3 p-4 border-2 border-purple-300 rounded-lg hover:bg-purple-100 cursor-pointer">
            <input
                type="radio"
                name="accepted_offer"
                value="${offer.type}"
                class="mt-1 w-5 h-5 text-purple-600 focus:ring-2 focus:ring-purple-500"
                onchange="acceptOffer()"
            >
            <div>
                <div class="font-semibold text-purple-900">${offer.title}</div>
                <div class="text-sm text-purple-700 mt-1">${offer.description}</div>
            </div>
        </label>
    `).join('');

    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
}

function getRetentionOffersForReasons(reasons) {
    const allOffers = {
        'too_expensive': {
            type: 'discount_3_months',
            title: '🎁 50% de descuento por 3 meses',
            description: 'Te ofrecemos 3 meses con 50% de descuento para que sigas aprovechando Dataflow'
        },
        'not_using': {
            type: 'training_session',
            title: '📚 Sesión de capacitación gratuita',
            description: 'Te ayudamos a sacar el máximo provecho con una sesión personalizada de 1 hora'
        },
        'missing_features': {
            type: 'priority_features',
            title: '⭐ Prioridad en desarrollo de funcionalidades',
            description: 'Tu feedback será prioritario y trabajaremos en las funcionalidades que necesitas'
        },
        'difficult_to_use': {
            type: 'onboarding_help',
            title: '🤝 Asistencia personalizada',
            description: 'Te asignamos un asesor personal durante 2 semanas para ayudarte con cualquier duda'
        },
        'technical_issues': {
            type: 'priority_support',
            title: '🔧 Soporte técnico prioritario',
            description: 'Resolvemos tus problemas con máxima prioridad y te damos 1 mes gratis'
        }
    };

    const offers = [];
    for (const reason of reasons) {
        if (allOffers[reason]) {
            offers.push(allOffers[reason]);
        }
    }

    // Si no hay ofertas específicas, agregar oferta genérica
    if (offers.length === 0) {
        offers.push({
            type: 'discount_1_month',
            title: '🎁 1 mes gratis',
            description: 'Antes de irte, te regalamos 1 mes para que reconsideres tu decisión'
        });
    }

    return offers;
}

function acceptOffer() {
    // El usuario seleccionó una oferta, guardar y cerrar modal
    setTimeout(() => {
        document.getElementById('cancellationForm').submit();
    }, 500);
}

function proceedWithCancellation() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.remove('hidden');
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancellationModal();
    }
});
</script>

@endsection
