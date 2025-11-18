<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Landing Page y Páginas Públicas
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/terms', [LandingController::class, 'terms'])->name('terms');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');
Route::get('/sitemap.xml', [LandingController::class, 'sitemap'])->name('sitemap');

// TODO: Rutas de autenticación
// TODO: Rutas del dashboard
// TODO: Rutas del admin panel
