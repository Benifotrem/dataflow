@extends('layouts.app')

@section('page-title', 'Liquidación de IVA')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Liquidación de IVA</h2>
            <p class="text-sm text-gray-600 mt-1">Genera reportes de liquidación de IVA con filtros personalizados</p>
        </div>
        <a href="{{ route('documents.index') }}" class="text-purple-600 hover:text-purple-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Documentos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('vat-liquidation.export') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Entidad -->
                <div>
                    <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entidad Contable (Opcional)
                    </label>
                    <select name="entity_id" id="entity_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Todas las entidades</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Si no seleccionas ninguna, se incluirán todas las entidades</p>
                </div>

                <!-- Modo de filtro de fecha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Período
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="date_mode" value="current_month" checked class="text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Mes actual ({{ now()->format('F Y') }})</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="date_mode" value="custom" class="text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Rango de fechas personalizado</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Rango de fechas personalizado (se muestra al seleccionar esa opción) -->
            <div id="custom-dates" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha desde
                    </label>
                    <input type="date" name="date_from" id="date_from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha hasta
                    </label>
                    <input type="date" name="date_to" id="date_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            <!-- Campo oculto para enviar el modo de mes actual -->
            <input type="hidden" name="current_month" id="current_month_input" value="true">

            <!-- Información del reporte -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>El reporte incluye:</strong> Solo facturas válidas procesadas exitosamente, con desglose de IVA por tasa, totales y subtotales agrupados.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botón de exportar -->
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-lg font-semibold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar Liquidación IVA (Excel)
                </button>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Facturas Validadas</h3>
            </div>
            <p class="text-sm text-gray-600">El reporte solo incluye facturas que han sido procesadas exitosamente por el sistema OCR y validadas como facturas reales.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Desglose Completo</h3>
            </div>
            <p class="text-sm text-gray-600">El Excel incluye base imponible, tipo de IVA, importe de IVA y total, con subtotales agrupados por cada tasa de IVA aplicada.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Totales Automáticos</h3>
            </div>
            <p class="text-sm text-gray-600">El reporte calcula automáticamente los totales generales de base imponible, IVA y total, listos para tu declaración fiscal.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentMonthRadio = document.querySelector('input[value="current_month"]');
    const customRadio = document.querySelector('input[value="custom"]');
    const customDatesDiv = document.getElementById('custom-dates');
    const currentMonthInput = document.getElementById('current_month_input');

    function updateDateMode() {
        if (customRadio.checked) {
            customDatesDiv.classList.remove('hidden');
            currentMonthInput.value = 'false';
        } else {
            customDatesDiv.classList.add('hidden');
            currentMonthInput.value = 'true';
        }
    }

    currentMonthRadio.addEventListener('change', updateDateMode);
    customRadio.addEventListener('change', updateDateMode);
});
</script>
@endsection
