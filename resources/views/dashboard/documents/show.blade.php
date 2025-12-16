@extends('layouts.app')

@section('page-title', 'Detalle de Documento')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('documents.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Documentos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $document->original_filename }}</h2>
                <p class="text-gray-600 mt-1">Subido el {{ $document->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $document->ocr_status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $document->ocr_status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $document->ocr_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $document->ocr_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($document->ocr_status) }}
            </span>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-600">Entidad</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $document->entity->name ?? 'No asignada' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Tamaño</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Tipo</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $document->mime_type }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-600">Estado OCR</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($document->ocr_status) }}</dd>
            </div>
        </dl>

        @if($document->ocr_data)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Extraída (IA)</h3>

            @php
                $invoiceType = $document->ocr_data['invoice_type'] ?? null;
                $ocrData = $document->ocr_data;
            @endphp

            @if($invoiceType === 'foreign')
                <!-- Vista formateada para facturas extranjeras -->
                <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">📍 Proveedor</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-600">Nombre</dt>
                                <dd class="text-base text-gray-900 font-medium">{{ $ocrData['vendor_name'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">País</dt>
                                <dd class="text-base text-gray-900">{{ $ocrData['vendor_country'] ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3">📄 Factura</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-600">Número</dt>
                                <dd class="text-base text-gray-900 font-medium">{{ $ocrData['invoice_number'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Moneda</dt>
                                <dd class="text-base text-gray-900 font-medium">{{ $ocrData['currency'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Fecha de Emisión</dt>
                                <dd class="text-base text-gray-900">{{ $ocrData['invoice_date'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Vencimiento</dt>
                                <dd class="text-base text-gray-900">{{ $ocrData['due_date'] ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3">💰 Montos</h4>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($ocrData['subtotal']) && $ocrData['subtotal'])
                            <div>
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-base text-gray-900">{{ $ocrData['currency'] ?? '' }} {{ number_format($ocrData['subtotal'], 2) }}</dd>
                            </div>
                            @endif
                            @if(isset($ocrData['tax_amount']) && $ocrData['tax_amount'])
                            <div>
                                <dt class="text-sm text-gray-600">Impuesto</dt>
                                <dd class="text-base text-gray-900">{{ $ocrData['currency'] ?? '' }} {{ number_format($ocrData['tax_amount'], 2) }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm text-gray-600">Monto Total</dt>
                                <dd class="text-lg text-gray-900 font-bold">{{ $ocrData['currency'] ?? '' }} {{ number_format($ocrData['monto_total'] ?? 0, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if(isset($ocrData['service_description']) && $ocrData['service_description'])
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3">📝 Descripción</h4>
                        <p class="text-gray-700">{{ $ocrData['service_description'] }}</p>
                    </div>
                    @endif

                    @if(isset($ocrData['observations']) && $ocrData['observations'])
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3">💬 Observaciones</h4>
                        <p class="text-gray-700">{{ $ocrData['observations'] }}</p>
                    </div>
                    @endif

                    <!-- JSON crudo colapsable -->
                    <div class="pt-4 border-t border-gray-200">
                        <details class="cursor-pointer">
                            <summary class="text-sm text-gray-600 hover:text-gray-900">Ver JSON completo</summary>
                            <pre class="mt-3 text-xs text-gray-700 whitespace-pre-wrap bg-white p-4 rounded border border-gray-200">{{ json_encode($ocrData, JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    </div>
                </div>
            @else
                <!-- Vista JSON para otros tipos de facturas -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($document->ocr_data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
        @endif

        <div class="mt-8 flex gap-4">
            @if(isset($document->ocr_data['invoice_type']) && $document->ocr_data['invoice_type'] === 'foreign')
                <a href="{{ route('documents.edit', $document) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition inline-block">
                    Editar Factura
                </a>
            @endif
            <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('¿Eliminar este documento?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                    Eliminar Documento
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
