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
Route::get('/blog', [LandingController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [LandingController::class, 'blogShow'])->name('blog.show');
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

    // Blog Management
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('create');
        Route::post('/generate', [\App\Http\Controllers\Admin\BlogController::class, 'generate'])->name('generate');
        Route::get('/{post}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('edit');
        Route::put('/{post}', [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('update');
        Route::post('/{post}/publish', [\App\Http\Controllers\Admin\BlogController::class, 'publish'])->name('publish');
        Route::post('/{post}/archive', [\App\Http\Controllers\Admin\BlogController::class, 'archive'])->name('archive');
        Route::delete('/{post}', [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('destroy');
    });

    // Settings - Blog Configuration
    Route::get('/settings/blog', [\App\Http\Controllers\Admin\SettingsController::class, 'blog'])->name('settings.blog');
    Route::put('/settings/blog', [\App\Http\Controllers\Admin\SettingsController::class, 'updateBlog'])->name('settings.blog.update');

    // Settings - Company Configuration
    Route::get('/settings/company', [\App\Http\Controllers\Admin\CompanySettingsController::class, 'index'])->name('settings.company');
    Route::put('/settings/company', [\App\Http\Controllers\Admin\CompanySettingsController::class, 'update'])->name('settings.company.update');

    // Settings - Tenant Profile Configuration
    Route::get('/settings/tenant-profile', [\App\Http\Controllers\Admin\TenantProfileController::class, 'index'])->name('settings.tenant-profile');
    Route::put('/settings/tenant-profile', [\App\Http\Controllers\Admin\TenantProfileController::class, 'update'])->name('settings.tenant-profile.update');

    // Tenants Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TenantsController::class, 'index'])->name('index');
        Route::get('/{tenant}', [\App\Http\Controllers\Admin\TenantsController::class, 'show'])->name('show');
        Route::post('/{tenant}/extend-trial', [\App\Http\Controllers\Admin\TenantsController::class, 'extendTrial'])->name('extend-trial');
        Route::post('/{tenant}/suspend', [\App\Http\Controllers\Admin\TenantsController::class, 'suspend'])->name('suspend');
        Route::post('/{tenant}/reactivate', [\App\Http\Controllers\Admin\TenantsController::class, 'reactivate'])->name('reactivate');
    });
});
