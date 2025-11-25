<?php

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

// Telegram Webhook
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');

// PagoPar Webhook
Route::post('/pagopar/webhook', [PagoParController::class, 'webhook'])->name('pagopar.webhook');
