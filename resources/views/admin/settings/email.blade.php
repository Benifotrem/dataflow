@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Configuración de Email (Brevo)</h1>
            <p class="text-gray-600 mt-2">Configura Brevo para enviar emails transaccionales y notificaciones</p>
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
                        <h3 class="text-red-800 font-medium">Error al guardar</h3>
                        <ul class="mt-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Info sobre Brevo --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-blue-800 font-medium">¿Qué es Brevo?</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Brevo (anteriormente Sendinblue) es una plataforma de email marketing y transaccional.
                        Necesitas crear una cuenta en <a href="https://www.brevo.com" target="_blank" class="underline font-medium">brevo.com</a>
                        y obtener tu API key desde el panel de administración.
                    </p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.settings.email.update') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- API Configuration --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Configuración de API</h2>

                {{-- Brevo API Key --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Brevo API Key *
                    </label>
                    <input
                        type="password"
                        name="brevo_api_key"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="{{ ($settings['brevo_api_key'] ?? false) ? '••••••••••••••••' : 'xkeysib-...' }}"
                    >
                    @if($settings['brevo_api_key'] ?? false)
                        <p class="text-sm text-green-600 mt-1">✓ API key configurada</p>
                    @else
                        <p class="text-sm text-gray-500 mt-1">Obtén tu API key en: Brevo → Account → SMTP & API</p>
                    @endif
                </div>
            </div>

            {{-- Email Sender Configuration --}}
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Remitente de Emails</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- From Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Remitente *
                        </label>
                        <input
                            type="text"
                            name="email_from_name"
                            value="{{ old('email_from_name', $settings['email_from_name'] ?? 'Dataflow') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Dataflow"
                        >
                    </div>

                    {{-- From Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email del Remitente *
                        </label>
                        <input
                            type="email"
                            name="email_from_address"
                            value="{{ old('email_from_address', $settings['email_from_address'] ?? 'no-reply@dataflow.guaraniappstore.com') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="no-reply@dataflow.guaraniappstore.com"
                        >
                        <p class="text-sm text-gray-500 mt-1">Este email debe estar verificado en Brevo</p>
                    </div>
                </div>
            </div>

            {{-- Email Notifications --}}
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notificaciones Automáticas</h2>

                <div class="space-y-4">
                    {{-- Welcome Email --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                type="checkbox"
                                name="email_welcome_enabled"
                                id="email_welcome_enabled"
                                {{ ($settings['email_welcome_enabled'] ?? true) ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                        </div>
                        <div class="ml-3">
                            <label for="email_welcome_enabled" class="font-medium text-gray-900">Email de Bienvenida</label>
                            <p class="text-sm text-gray-600">Enviar email de bienvenida cuando un nuevo usuario se registra</p>
                        </div>
                    </div>

                    {{-- Document Limit Notifications --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                type="checkbox"
                                name="email_document_limit_enabled"
                                id="email_document_limit_enabled"
                                {{ ($settings['email_document_limit_enabled'] ?? true) ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                        </div>
                        <div class="ml-3">
                            <label for="email_document_limit_enabled" class="font-medium text-gray-900">Alertas de Límite de Documentos</label>
                            <p class="text-sm text-gray-600">Enviar notificación cuando el usuario se acerque al límite de documentos</p>
                        </div>
                    </div>

                    {{-- Threshold --}}
                    <div class="ml-7 pl-3 border-l-2 border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Umbral de Alerta (%) *
                        </label>
                        <div class="flex items-center gap-4">
                            <input
                                type="range"
                                name="email_document_limit_threshold"
                                id="threshold_range"
                                min="50"
                                max="100"
                                step="5"
                                value="{{ old('email_document_limit_threshold', $settings['email_document_limit_threshold'] ?? 80) }}"
                                class="flex-1"
                                oninput="document.getElementById('threshold_value').textContent = this.value"
                            >
                            <span id="threshold_value" class="text-lg font-semibold text-purple-600 w-12">{{ $settings['email_document_limit_threshold'] ?? 80 }}</span>
                            <span class="text-gray-600">%</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Enviar notificación cuando el uso alcance este porcentaje</p>
                    </div>
                </div>
            </div>

            {{-- Test Email --}}
            <div class="border-t pt-6 bg-yellow-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-yellow-900 mb-2">⚠️ Importante</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Asegúrate de verificar tu dominio/email en Brevo antes de enviar emails</li>
                    <li>• Los emails transaccionales tienen límites según tu plan de Brevo</li>
                    <li>• Guarda la configuración y prueba enviando un email de prueba desde tu cuenta</li>
                </ul>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    ← Volver al Panel
                </a>
                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
