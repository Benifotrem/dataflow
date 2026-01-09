<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        // Validar campos sin validar unique en email
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'tenant_type' => ['required', 'in:b2c,b2b'],
            'country' => ['required', 'string', 'max:2'],
        ]);

        // Verificar si el email ya existe
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // Email ya existe - no revelar esta información
            // Solo registrar en log y mostrar mensaje genérico
            Log::info('Intento de registro con email existente', [
                'email' => $validated['email'],
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')->with('info', 'Si el email está registrado recibirá un correo en los próximos minutos.');
        }

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

            // Enviar email de bienvenida
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
                Log::info('Email de bienvenida enviado', ['user_id' => $user->id, 'email' => $user->email]);
            } catch (\Exception $e) {
                // No fallar el registro si el email falla
                Log::warning('No se pudo enviar email de bienvenida', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect('/dashboard')->with('success', '¡Bienvenido a Dataflow! Tu cuenta ha sido creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registro de usuario', [
                'email' => $validated['email'],
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => 'Ocurrió un error al procesar tu solicitud. Por favor intenta nuevamente.'])->withInput();
        }
    }
}
