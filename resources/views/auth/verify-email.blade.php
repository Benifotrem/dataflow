@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Verifica tu email
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Antes de continuar, revisa tu bandeja de entrada para verificar tu dirección de email.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <p class="text-sm text-gray-600 mb-4">
                Si no recibiste el email de verificación, puedes solicitar uno nuevo haciendo clic en el botón de abajo.
            </p>
            
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Reenviar email de verificación
                </button>
            </form>
        </div>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-purple-600 hover:text-purple-500">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
