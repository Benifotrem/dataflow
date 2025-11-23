<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantProfileController extends Controller
{
    /**
     * Mostrar configuración del perfil del tenant
     */
    public function index()
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $tenant = Auth::user()->tenant;
        $countries = CurrencyService::getCountriesWithCurrencies();
        $currencies = CurrencyService::getUniqueCurrencies();

        return view('admin.settings.tenant-profile', compact('tenant', 'countries', 'currencies'));
    }

    /**
     * Actualizar configuración del perfil del tenant
     */
    public function update(Request $request)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country_code' => 'required|string|size:2',
            'currency_code' => 'required|string|size:3',
        ]);

        try {
            // Validar que el país y la moneda sean válidos
            if (!CurrencyService::isValidCountry($validated['country_code'])) {
                return back()->withErrors(['country_code' => 'Código de país inválido']);
            }

            if (!CurrencyService::isValidCurrency($validated['currency_code'])) {
                return back()->withErrors(['currency_code' => 'Código de moneda inválido']);
            }

            $tenant = Auth::user()->tenant;

            // Actualizar tenant
            $tenant->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'country_code' => $validated['country_code'],
                'currency_code' => $validated['currency_code'],
            ]);

            return back()->with('success', 'Perfil actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }
}
