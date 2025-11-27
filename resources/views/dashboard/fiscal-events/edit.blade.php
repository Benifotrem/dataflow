@extends('layouts.app')

@section('page-title', 'Editar Evento Fiscal')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('fiscal-events.index') }}" class="text-purple-600 hover:text-purple-700 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al Calendario
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Evento Fiscal</h1>
                @if($fiscalEvent->is_default)
                <p class="text-sm text-gray-600 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        Evento por defecto
                    </span>
                    - Puedes modificar sus parámetros pero no eliminarlo
                </p>
                @endif
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('fiscal-events.update', $fiscalEvent) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Título del Evento <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $fiscalEvent->title) }}" required
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>

            {{-- Event Type --}}
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Evento <span class="text-red-500">*</span>
                </label>
                <select name="event_type" id="event_type" required
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    @foreach($eventTypes as $value => $label)
                    <option value="{{ $value }}" {{ old('event_type', $fiscalEvent->event_type) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ old('description', $fiscalEvent->description) }}</textarea>
            </div>

            {{-- Event Date --}}
            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha del Evento <span class="text-red-500">*</span>
                </label>
                <input type="date" name="event_date" id="event_date"
                    value="{{ old('event_date', $fiscalEvent->event_date->format('Y-m-d')) }}" required
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                @if($fiscalEvent->is_past)
                <p class="text-xs text-red-600 mt-1">Este evento ya pasó. Considera desactivarlo o actualizar la fecha.</p>
                @endif
            </div>

            {{-- Notification Days Before --}}
            <div>
                <label for="notification_days_before" class="block text-sm font-medium text-gray-700 mb-2">
                    Notificar con cuántos días de anticipación <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-4">
                    <input type="number" name="notification_days_before" id="notification_days_before"
                        value="{{ old('notification_days_before', $fiscalEvent->notification_days_before) }}"
                        required min="1" max="90"
                        class="w-32 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <span class="text-sm text-gray-600">días antes del evento</span>
                </div>
            </div>

            {{-- Checkboxes --}}
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                            {{ old('is_recurring', $fiscalEvent->is_recurring) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_recurring" class="font-medium text-gray-700">
                            Evento recurrente
                        </label>
                        <p class="text-sm text-gray-500">El evento se repetirá automáticamente cada año</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $fiscalEvent->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    </div>
                    <div class="ml-3">
                        <label for="is_active" class="font-medium text-gray-700">
                            Evento activo
                        </label>
                        <p class="text-sm text-gray-500">Solo los eventos activos enviarán notificaciones</p>
                    </div>
                </div>
            </div>

            {{-- Last Notified --}}
            @if($fiscalEvent->last_notified_at)
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    <strong>Última notificación enviada:</strong> {{ $fiscalEvent->last_notified_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Actualizar Evento
                </button>
                <a href="{{ route('fiscal-events.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
