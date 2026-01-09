<?php

use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\PagoParController;
use App\Http\Controllers\Api\TelegramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Telegram Webhook - REACTIVADO con protecciones anti-bucle
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])
    ->middleware('throttle:60,1') // 60 requests por minuto (1 por segundo promedio)
    ->name('telegram.webhook');

// Endpoint de prueba para verificar accesibilidad
Route::get('/telegram/test', function() {
    \Illuminate\Support\Facades\Log::info('🧪 Test endpoint accessed');
    return response()->json([
        'status' => 'ok',
        'message' => 'Telegram webhook endpoint is accessible',
        'timestamp' => now()->toIso8601String()
    ]);
});

// PagoPar Webhook
Route::post('/pagopar/webhook', [PagoParController::class, 'webhook'])
    ->middleware('throttle:30,1') // 30 requests por minuto
    ->name('pagopar.webhook');

// Chatbot Web (requiere autenticación web)
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])
        ->middleware('throttle:20,1') // 20 mensajes por minuto por usuario
        ->name('chatbot.message');
});
