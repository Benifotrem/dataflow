<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Idiomas soportados
        $supportedLocales = ['es', 'pt', 'en'];

        // 1. Prioridad: Parámetro en la URL (para cambiar idioma)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if (in_array($locale, $supportedLocales)) {
                Session::put('locale', $locale);
                App::setLocale($locale);
                return $next($request);
            }
        }

        // 2. Prioridad: Sesión del usuario
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
                return $next($request);
            }
        }

        // 3. Prioridad: Usuario autenticado (si tiene preferencia guardada)
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
                Session::put('locale', $locale);
                return $next($request);
            }
        }

        // 4. Prioridad: Detectar del navegador
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE', ''), 0, 2);
        if (in_array($browserLocale, $supportedLocales)) {
            App::setLocale($browserLocale);
            Session::put('locale', $browserLocale);
            return $next($request);
        }

        // 5. Fallback: Usar el idioma por defecto de la configuración (es)
        App::setLocale(config('app.locale', 'es'));

        return $next($request);
    }
}
