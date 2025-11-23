#!/bin/bash
# EMERGENCY FIX - Restaurar Contaplus a estado funcional

echo "🚨 EMERGENCY FIX - Restaurando estado funcional"
cd /home/u489458217/domains/dataflow.guaraniappstore.com/

# Crear vista simple que funciona
echo "📝 Creando vista simple funcional..."

cat > resources/views/layouts/app.blade.php << 'EOFBLADE'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contaplus - Automatización Contable con IA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <header class="fixed top-0 w-full bg-white shadow-sm z-50">
        <nav class="container mx-auto px-6 py-4">
            <a href="/" class="text-2xl font-bold text-purple-600">Contaplus</a>
        </nav>
    </header>
    <main class="pt-16">
        @yield('content')
    </main>
</body>
</html>
EOFBLADE

cat > resources/views/welcome.blade.php << 'EOFBLADE'
@extends('layouts.app')

@section('content')
<section class="gradient-bg text-white py-32">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">
            Automatiza tu Contabilidad con <span class="text-yellow-300">IA</span>
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-purple-100">
            Contaplus procesa facturas, concilia extractos y gestiona tu fiscalidad automáticamente
        </p>
        <a href="#" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition">
            Prueba Gratis 14 Días
        </a>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Características Principales</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-purple-50 p-8 rounded-xl">
                <h3 class="text-xl font-bold mb-3">OCR Inteligente</h3>
                <p class="text-gray-600">Extrae datos de facturas automáticamente</p>
            </div>
            <div class="bg-purple-50 p-8 rounded-xl">
                <h3 class="text-xl font-bold mb-3">Conciliación Automática</h3>
                <p class="text-gray-600">Concilia extractos bancarios sin esfuerzo</p>
            </div>
            <div class="bg-purple-50 p-8 rounded-xl">
                <h3 class="text-xl font-bold mb-3">Clasificación Fiscal</h3>
                <p class="text-gray-600">IA que clasifica según reglas fiscales</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 gradient-bg text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-6">Comienza Hoy</h2>
        <a href="#" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-lg font-bold text-lg">
            Prueba Gratis
        </a>
    </div>
</section>
@endsection
EOFBLADE

# Actualizar routes para usar welcome
cat > routes/web.php << 'EOFROUTES'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');
EOFROUTES

# Limpiar todo el cache
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Verificar permisos
chmod -R 775 storage bootstrap/cache

echo ""
echo "✅ FIX APLICADO"
echo "🔗 Verifica: https://dataflow.guaraniappstore.com/"
echo ""
echo "Estado: Vista simple funcional restaurada"
echo "Siguiente paso: Reconstruir landing completa con método heredoc"
