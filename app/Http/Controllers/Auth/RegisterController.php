<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\BrevoService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Procesar registro
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'tenant_type' => ['required', 'in:b2c,b2b'],
            'country' => ['required', 'string', 'max:2'],
        ]);

        try {
            DB::beginTransaction();

            // Obtener moneda basada en el país
            $countryCode = strtoupper($validated['country']);
            $currency = CurrencyService::getCurrencyByCountry($countryCode);
            $currencyCode = $currency['code'] ?? 'USD';

            // Crear el tenant
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'email' => $validated['email'], // Email del tenant
                'type' => $validated['tenant_type'],
                'country_code' => $countryCode,
                'currency_code' => $currencyCode,
                'status' => 'active',
                'trial_ends_at' => now()->addDays(30), // 30 días de prueba
            ]);

            // Crear el usuario
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'owner', // El primer usuario es propietario
            ]);

            DB::commit();

            // Autenticar al usuario
            Auth::login($user);

            // Enviar email de bienvenida si está habilitado
            try {
                $emailWelcomeEnabled = Setting::get('email_welcome_enabled', true);
                if ($emailWelcomeEnabled) {
                    $brevoService = new BrevoService();
                    if ($brevoService->isConfigured()) {
                        $brevoService->sendWelcomeEmail($user->email, $user->name);
                    }
                }
            } catch (\Exception $e) {
                // No fallar el registro si el email falla
                Log::warning('No se pudo enviar email de bienvenida: ' . $e->getMessage());
            }

            return redirect('/dashboard')->with('success', '¡Bienvenido a Contaplus! Tu cuenta ha sido creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al crear tu cuenta. Por favor intenta nuevamente.'])->withInput();
        }
    }
}
