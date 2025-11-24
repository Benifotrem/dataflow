@extends('layouts.app')

@section('page-title', 'Configuración del Sistema')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Configuración del Sistema</h2>
        <p class="text-gray-600 mt-1">Gestiona las configuraciones globales de Dataflow</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Límites y Precios</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Límite de Documentos IA (Base)</label>
                        <input type="number" name="settings[document_limit_base]" value="{{ $settings['document_limit_base']->value ?? 500 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-sm text-gray-500 mt-1">Número de documentos incluidos en planes base</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Plan Básico (USD/mes)</label>
                        <input type="number" step="0.01" name="settings[price_basic_plan]" value="{{ $settings['price_basic_plan']->value ?? 19.99 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Plan Avanzado (USD/mes)</label>
                        <input type="number" step="0.01" name="settings[price_advanced_plan]" value="{{ $settings['price_advanced_plan']->value ?? 49.99 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Addon 500 Docs (USD)</label>
                        <input type="number" step="0.01" name="settings[addon_price_500_docs]" value="{{ $settings['addon_price_500_docs']->value ?? 9.99 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="border-t pt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">API Keys</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OpenAI API Key</label>
                        <input type="password" name="settings[openai_api_key]" value="{{ $settings['openai_api_key']->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="sk-...">
                        <p class="text-sm text-gray-500 mt-1">Clave para procesamiento OCR con GPT-4o-mini</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brevo API Key</label>
                        <input type="password" name="settings[brevo_api_key]" value="{{ $settings['brevo_api_key']->value ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-sm text-gray-500 mt-1">Clave para envío de emails transaccionales</p>
                    </div>
                </div>
            </div>

            <div class="border-t pt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Retención de Datos</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Días de Retención de Extractos</label>
                    <input type="number" name="settings[data_retention_days]" value="{{ $settings['data_retention_days']->value ?? 60 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-sm text-gray-500 mt-1">Días desde fin de mes antes de eliminación automática</p>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
