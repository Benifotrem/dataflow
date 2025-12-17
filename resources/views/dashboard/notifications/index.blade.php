@extends('layouts.app')

@section('title', 'Notificaciones')
@section('page-title', 'Notificaciones')

@section('content')
<div class="max-w-4xl">
    {{-- Stats --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-gray-600">
                Total: <span class="font-bold">{{ $notifications->total() }}</span> notificaciones
            </p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                ✓ Marcar todas como leídas
            </button>
        </form>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div class="p-6 {{ !$notification->read_at ? 'bg-purple-50' : 'hover:bg-gray-50' }} transition">
                <div class="flex gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 text-3xl">
                        @switch($notification->type)
                            @case('duplicate_detected')
                                ⚠️
                                @break
                            @case('limit_exceeded')
                                🚨
                                @break
                            @case('document_processed')
                                ✅
                                @break
                            @case('document_failed')
                                ❌
                                @break
                            @case('warning')
                                ⚠️
                                @break
                            @case('info')
                                ℹ️
                                @break
                            @default
                                🔔
                        @endswitch
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 {{ !$notification->read_at ? 'font-bold' : '' }}">
                                    {{ $notification->title }}
                                </h3>
                                <p class="text-gray-700 mt-2 whitespace-pre-line">
                                    {{ $notification->message }}
                                </p>

                                {{-- Additional Data --}}
                                @if($notification->data && isset($notification->data['original_document_id']))
                                    <div class="mt-3">
                                        <a href="{{ route('documents.show', $notification->data['original_document_id']) }}"
                                           class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Ver documento original
                                        </a>
                                    </div>
                                @endif

                                {{-- Metadata --}}
                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $notification->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Unread indicator --}}
                            @if(!$notification->read_at)
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                                </div>
                            @endif
                        </div>

                        {{-- Mark as read action --}}
                        @if(!$notification->read_at)
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                    Marcar como leída
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-500 mt-4">No tienes notificaciones</p>
                <p class="text-sm text-gray-400 mt-2">Las notificaciones aparecerán aquí cuando haya actividad en tu cuenta</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
