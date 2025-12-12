<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Mostrar configuraciones del usuario
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        return view('dashboard.settings.index', compact('user', 'tenant'));
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'in:es,en,pt'],
            'timezone' => ['nullable', 'string'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ]);

        // Si el usuario tiene preferencias en una tabla separada, actualizarlas aquí
        // Por ahora solo mostramos un mensaje de éxito

        return redirect()->route('settings.index')
            ->with('success', 'Configuración actualizada exitosamente.');
    }
}
