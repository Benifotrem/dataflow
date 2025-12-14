<?php

use App\Http\Controllers\MiniAppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mini App API Routes
|--------------------------------------------------------------------------
|
| Rutas para Telegram Mini App
| Todas requieren autenticación mediante TelegramMiniAppAuth middleware
|
*/

Route::middleware(['telegram.miniapp'])->prefix('api/miniapp')->group(function () {
    Route::post('/upload', [MiniAppController::class, 'uploadDocument'])->name('miniapp.upload');

    // Dashboard
    Route::get('/dashboard', [MiniAppController::class, 'dashboard']);

    // Entities
    Route::get('/entities', [MiniAppController::class, 'listEntities']);

    // Documents
    Route::get('/documents', [MiniAppController::class, 'listDocuments']);
    Route::get('/documents/{id}', [MiniAppController::class, 'getDocument']);
    Route::patch('/documents/{id}', [MiniAppController::class, 'updateDocument']);
    Route::post('/upload', [MiniAppController::class, 'uploadDocument']);

    // CDC - Consulta de facturas electrónicas
    Route::post('/cdc/consult', [MiniAppController::class, 'consultCDC']);

    // Export
    Route::post('/export/vat-liquidation', [MiniAppController::class, 'exportVatLiquidation']);
});

// Descarga de archivos temporales (sin auth, pero con token firmado)
Route::get('/miniapp/download/{file}', function ($file) {
    $filePath = storage_path('app/temp/' . $file);

    if (!file_exists($filePath)) {
        abort(404, 'Archivo no encontrado o expirado');
    }

    // Verificar que no tenga más de 1 hora
    if (filemtime($filePath) < time() - 3600) {
        unlink($filePath);
        abort(404, 'Archivo expirado');
    }

    return response()->download($filePath)->deleteFileAfterSend();
})->name('miniapp.download-temp-file');
