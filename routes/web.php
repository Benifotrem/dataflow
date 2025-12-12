<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Cambio de idioma
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

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

    // Password Reset
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// TEMPORAL: Ruta de diagnóstico sin autenticación
Route::get('/debug/document/{id}', function($id) {
    try {
        $document = \App\Models\Document::findOrFail($id);
        $document->load('entity', 'user', 'tenant');

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'filename' => $document->original_filename,
                'entity' => $document->entity ? $document->entity->name : 'NULL',
                'user' => $document->user ? $document->user->name : 'NULL',
                'tenant' => $document->tenant ? $document->tenant->name : 'NULL',
                'ocr_status' => $document->ocr_status,
            ],
            'can_render_view' => true,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// TEMPORAL: Ruta de diagnóstico CON autenticación
Route::get('/debug/auth-document/{id}', function($id) {
    try {
        $user = auth()->user();
        $document = \App\Models\Document::findOrFail($id);

        // Test authorization
        $canView = false;
        $authError = null;
        try {
            Gate::authorize('view', $document);
            $canView = true;
        } catch (\Exception $e) {
            $authError = $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'logged_in' => $user !== null,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'tenant_id' => $user->tenant_id,
            ] : null,
            'document' => [
                'id' => $document->id,
                'tenant_id' => $document->tenant_id,
            ],
            'authorization' => [
                'can_view' => $canView,
                'error' => $authError,
                'match_tenant' => $user && $user->tenant_id === $document->tenant_id,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
})->middleware('auth');

// TEMPORAL: Ruta para probar renderizado de vista (SIN tenant.active)
Route::get('/debug/render-document/{id}', function($id) {
    try {
        $document = \App\Models\Document::findOrFail($id);
        $document->load('entity', 'user');

        // Intentar renderizar la vista
        return view('dashboard.documents.show', compact('document'));
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ], 500);
    }
})->middleware('auth');

// TEMPORAL: Ruta para probar renderizado CON tenant.active middleware
Route::get('/debug/render-with-tenant/{id}', function($id) {
    try {
        $document = \App\Models\Document::findOrFail($id);
        $document->load('entity', 'user');

        // Intentar renderizar la vista
        return view('dashboard.documents.show', compact('document'));
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ], 500);
    }
})->middleware(['auth', 'tenant.active']);

// TEMPORAL: Simular exactamente el route model binding del controlador
Route::get('/debug/exact-controller/{document}', function(\App\Models\Document $document) {
    try {
        // Simular exactamente lo que hace el controlador
        Gate::authorize('view', $document);
        $document->load('entity', 'user');

        return view('dashboard.documents.show', compact('document'));
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ], 500);
    }
})->middleware(['auth', 'tenant.active']);

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth', 'tenant.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard.index');

    // Documentos
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\DocumentController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\DocumentExportController::class, 'export'])->name('export');
        Route::get('/create', [\App\Http\Controllers\Dashboard\DocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\DocumentController::class, 'store'])->name('store');

        // TEMPORAL: Ruta inline mientras OPcache actualiza el controlador
        // TODO: Restaurar cuando OPcache se actualice: Route::get('/{document}', [\App\Http\Controllers\Dashboard\DocumentController::class, 'show'])->name('show');
        Route::get('/{document}', function(\App\Models\Document $document) {
            try {
                Gate::authorize('view', $document);
                $document->load('entity', 'user');
                return view('dashboard.documents.show', compact('document'));
            } catch (\Exception $e) {
                logger()->error('Error showing document: ' . $e->getMessage(), [
                    'document_id' => $document->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->route('documents.index')
                    ->withErrors(['error' => 'Error al mostrar el documento: ' . $e->getMessage()]);
            }
        })->name('show');

        Route::delete('/{document}', [\App\Http\Controllers\Dashboard\DocumentController::class, 'destroy'])->name('destroy');
    });

    // Liquidación de IVA
    Route::prefix('vat-liquidation')->name('vat-liquidation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VatLiquidationController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\VatLiquidationController::class, 'export'])->name('export');
    });

    // Calendario Fiscal
    Route::prefix('fiscal-events')->name('fiscal-events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'store'])->name('store');
        Route::get('/{fiscalEvent}/edit', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'edit'])->name('edit');
        Route::put('/{fiscalEvent}', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'update'])->name('update');
        Route::delete('/{fiscalEvent}', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'destroy'])->name('destroy');
        Route::patch('/{fiscalEvent}/toggle-active', [\App\Http\Controllers\Dashboard\FiscalEventController::class, 'toggleActive'])->name('toggle-active');
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

    // Settings - Email Configuration (Brevo)
    Route::get('/settings/email', [\App\Http\Controllers\Admin\SettingsController::class, 'email'])->name('settings.email');
    Route::put('/settings/email', [\App\Http\Controllers\Admin\SettingsController::class, 'updateEmail'])->name('settings.email.update');

    // Tenants Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TenantsController::class, 'index'])->name('index');
        Route::get('/{tenant}', [\App\Http\Controllers\Admin\TenantsController::class, 'show'])->name('show');
        Route::post('/{tenant}/extend-trial', [\App\Http\Controllers\Admin\TenantsController::class, 'extendTrial'])->name('extend-trial');
        Route::post('/{tenant}/suspend', [\App\Http\Controllers\Admin\TenantsController::class, 'suspend'])->name('suspend');
        Route::post('/{tenant}/reactivate', [\App\Http\Controllers\Admin\TenantsController::class, 'reactivate'])->name('reactivate');
    });
});
