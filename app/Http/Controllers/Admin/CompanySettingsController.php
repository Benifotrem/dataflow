<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    /**
     * Mostrar configuración general de la empresa
     */
    public function index()
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        // Obtener settings de configuración general
        $settings = [];
        $settingRecords = Setting::where('group', 'company')->get();

        foreach ($settingRecords as $setting) {
            $settings[$setting->key] = $setting->value;
        }

        return view('admin.settings.company', compact('settings'));
    }

    /**
     * Actualizar configuración general de la empresa
     */
    public function update(Request $request)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'company_city' => 'nullable|string|max:100',
            'company_country' => 'nullable|string|max:100',
            'company_postal_code' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:50',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
        ]);

        try {
            foreach ($validated as $key => $value) {
                Setting::set($key, $value ?: '', 'string', 'company');
            }

            return back()->with('success', 'Configuración de empresa actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }
}
