@extends('layouts.app')

@section('page-title', 'Editar Documento')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('documents.show', $document) }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al Documento
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Editar Factura</h2>
            <p class="text-gray-600 mt-1">{{ $document->original_filename }}</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Errores de validación:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $invoiceType = $document->ocr_data['invoice_type'] ?? null;
            $ocrData = $document->ocr_data ?? [];
        @endphp

        @if($invoiceType === 'foreign')
            <form action="{{ route('documents.update', $document) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información del Proveedor -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            📍 Información del Proveedor
                        </h3>
                    </div>

                    <div>
                        <label for="vendor_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Proveedor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vendor_name" id="vendor_name"
                            value="{{ old('vendor_name', $ocrData['vendor_name'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="vendor_country" class="block text-sm font-medium text-gray-700 mb-2">
                            País del Proveedor
                        </label>
                        <input type="text" name="vendor_country" id="vendor_country"
                            value="{{ old('vendor_country', $ocrData['vendor_country'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ej: United States">
                    </div>

                    <!-- Información de la Factura -->
                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            📄 Información de la Factura
                        </h3>
                    </div>

                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Factura <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number"
                            value="{{ old('invoice_number', $ocrData['invoice_number'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Moneda <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" id="currency"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                            <option value="USD" {{ old('currency', $ocrData['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - Dólar Estadounidense</option>
                            <option value="EUR" {{ old('currency', $ocrData['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="BRL" {{ old('currency', $ocrData['currency'] ?? '') === 'BRL' ? 'selected' : '' }}>BRL - Real Brasileño</option>
                            <option value="ARS" {{ old('currency', $ocrData['currency'] ?? '') === 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                            <option value="PYG" {{ old('currency', $ocrData['currency'] ?? '') === 'PYG' ? 'selected' : '' }}>PYG - Guaraní</option>
                        </select>
                    </div>

                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Emisión <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date"
                            value="{{ old('invoice_date', $ocrData['invoice_date'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Vencimiento
                        </label>
                        <input type="date" name="due_date" id="due_date"
                            value="{{ old('due_date', $ocrData['due_date'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Montos -->
                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            💰 Montos
                        </h3>
                    </div>

                    <div>
                        <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">
                            Subtotal (antes de impuestos)
                        </label>
                        <input type="number" step="0.01" name="subtotal" id="subtotal"
                            value="{{ old('subtotal', $ocrData['subtotal'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="0.00">
                    </div>

                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Impuesto (Total)
                        </label>
                        <input type="number" step="0.01" name="tax_amount" id="tax_amount"
                            value="{{ old('tax_amount', $ocrData['tax_amount'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="0.00">
                    </div>

                    <div>
                        <label for="tax_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Porcentaje de Impuesto (%)
                        </label>
                        <input type="number" step="0.01" name="tax_percentage" id="tax_percentage"
                            value="{{ old('tax_percentage', $ocrData['tax_percentage'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ej: 21">
                    </div>

                    <div>
                        <label for="monto_total" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto Total <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="monto_total" id="monto_total"
                            value="{{ old('monto_total', $ocrData['monto_total'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="0.00"
                            required>
                    </div>

                    <!-- Descripción y Detalles -->
                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            📝 Descripción y Detalles
                        </h3>
                    </div>

                    <div class="md:col-span-2">
                        <label for="service_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción del Servicio/Producto
                        </label>
                        <textarea name="service_description" id="service_description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Descripción del servicio o producto...">{{ old('service_description', $ocrData['service_description'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Método de Pago
                        </label>
                        <input type="text" name="payment_method" id="payment_method"
                            value="{{ old('payment_method', $ocrData['payment_method'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ej: Credit Card, Wire Transfer">
                    </div>

                    <div class="md:col-span-2">
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                            Observaciones
                        </label>
                        <textarea name="observations" id="observations" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Notas u observaciones adicionales...">{{ old('observations', $ocrData['observations'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        💾 Guardar Cambios
                    </button>
                    <a href="{{ route('documents.show', $document) }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        @else
            <div class="text-center py-8">
                <p class="text-gray-600 mb-4">Solo se pueden editar facturas extranjeras por ahora.</p>
                <a href="{{ route('documents.show', $document) }}" class="text-purple-600 hover:text-purple-700">
                    Volver al documento
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
