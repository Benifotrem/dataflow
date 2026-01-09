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

Route::middleware(['telegram.miniapp', 'throttle:120,1'])->prefix('api/miniapp')->group(function () {

    // Dashboard
    Route::get('/dashboard', [MiniAppController::class, 'dashboard']);

    // Entities
    Route::get('/entities', [MiniAppController::class, 'listEntities']);

    // Documents
    Route::get('/documents', [MiniAppController::class, 'listDocuments']);
    Route::get('/documents/{id}', [MiniAppController::class, 'getDocument']);
    Route::patch('/documents/{id}', [MiniAppController::class, 'updateDocument']);
    Route::post('/upload', [MiniAppController::class, 'uploadDocument'])
        ->middleware('throttle:10,1') // 10 uploads por minuto máximo
        ->name('miniapp.upload');

    // Notifications
    Route::get('/notifications', [MiniAppController::class, 'getNotifications']);
    Route::post('/notifications/{id}/mark-read', [MiniAppController::class, 'markNotificationAsRead']);
    Route::post('/notifications/mark-all-read', [MiniAppController::class, 'markAllNotificationsAsRead']);

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
