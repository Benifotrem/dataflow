<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Landing Page y Páginas Públicas
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/terms', [LandingController::class, 'terms'])->name('terms');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/sitemap.xml', [LandingController::class, 'sitemap'])->name('sitemap');

// Rutas de Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth', 'tenant.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard.index');

    // Documentos
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\DocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Dashboard\DocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\DocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [\App\Http\Controllers\Dashboard\DocumentController::class, 'show'])->name('show');
        Route::delete('/{document}', [\App\Http\Controllers\Dashboard\DocumentController::class, 'destroy'])->name('destroy');
    });

    // Transacciones
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\TransactionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Dashboard\TransactionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [\App\Http\Controllers\Dashboard\TransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [\App\Http\Controllers\Dashboard\TransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [\App\Http\Controllers\Dashboard\TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [\App\Http\Controllers\Dashboard\TransactionController::class, 'destroy'])->name('destroy');
    });

    // Extractos Bancarios
    Route::prefix('bank-statements')->name('bank-statements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\BankStatementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Dashboard\BankStatementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\BankStatementController::class, 'store'])->name('store');
        Route::delete('/{bankStatement}', [\App\Http\Controllers\Dashboard\BankStatementController::class, 'destroy'])->name('destroy');
    });

    // Entidades Fiscales
    Route::prefix('entities')->name('entities.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\EntityController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Dashboard\EntityController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\EntityController::class, 'store'])->name('store');
        Route::get('/{entity}', [\App\Http\Controllers\Dashboard\EntityController::class, 'show'])->name('show');
        Route::get('/{entity}/edit', [\App\Http\Controllers\Dashboard\EntityController::class, 'edit'])->name('edit');
        Route::put('/{entity}', [\App\Http\Controllers\Dashboard\EntityController::class, 'update'])->name('update');
        Route::delete('/{entity}', [\App\Http\Controllers\Dashboard\EntityController::class, 'destroy'])->name('destroy');
    });
});

// Rutas de Administración (solo para admin)
Route::middleware(['auth', 'tenant.active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('settings.update');
});
