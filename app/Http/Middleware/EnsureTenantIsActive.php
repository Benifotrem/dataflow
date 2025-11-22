<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar que el tenant exista y esté activo
        if (!$user->tenant || $user->tenant->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['error' => 'Tu cuenta ha sido suspendida. Contacta con soporte.']);
        }

        return $next($request);
    }
}
