<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Cambiar el idioma de la aplicación
     */
    public function switch(Request $request, string $locale)
    {
        $supportedLocales = ['es', 'pt', 'en'];

        if (!in_array($locale, $supportedLocales)) {
            abort(400, 'Idioma no soportado');
        }

        // Guardar en sesión
        Session::put('locale', $locale);

        // Si el usuario está autenticado, guardar su preferencia
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Redirigir a la página anterior
        return redirect()->back()->with('success', __('app.language') . ' actualizado');
    }
}
