<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

            // Crear el tenant
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'type' => $validated['tenant_type'],
                'country' => strtoupper($validated['country']),
                'currency' => $this->getCurrencyByCountry($validated['country']),
                'status' => 'active',
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

            return redirect('/dashboard')->with('success', '¡Bienvenido a Contaplus! Tu cuenta ha sido creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al crear tu cuenta. Por favor intenta nuevamente.'])->withInput();
        }
    }

    /**
     * Obtener moneda por país
     */
    private function getCurrencyByCountry(string $country): string
    {
        $currencies = [
            'es' => 'EUR', // España
            'mx' => 'MXN', // México
            'ar' => 'ARS', // Argentina
            'co' => 'COP', // Colombia
            'cl' => 'CLP', // Chile
            'pe' => 'PEN', // Perú
            'ec' => 'USD', // Ecuador
            've' => 'VES', // Venezuela
            'uy' => 'UYU', // Uruguay
            'py' => 'PYG', // Paraguay
            'bo' => 'BOB', // Bolivia
            'cr' => 'CRC', // Costa Rica
            'pa' => 'PAB', // Panamá
            'gt' => 'GTQ', // Guatemala
            'hn' => 'HNL', // Honduras
            'sv' => 'USD', // El Salvador
            'ni' => 'NIO', // Nicaragua
            'do' => 'DOP', // República Dominicana
            'cu' => 'CUP', // Cuba
        ];

        return $currencies[strtolower($country)] ?? 'USD';
    }
}
