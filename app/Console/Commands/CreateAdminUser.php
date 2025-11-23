<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email} {password} {name=Administrador}';
    protected $description = 'Crear un usuario super administrador';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');

        // Verificar si el usuario ya existe
        if (User::where('email', $email)->exists()) {
            $this->error("El usuario con email {$email} ya existe.");
            return 1;
        }

        // Crear o buscar tenant admin
        $tenant = Tenant::where('slug', 'admin')->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Administración',
                'email' => $email,
                'slug' => 'admin',
                'type' => 'b2b',
                'country_code' => 'PY',
                'currency_code' => 'PYG',
                'status' => 'active'
            ]);
            $this->info("✓ Tenant 'admin' creado con ID: {$tenant->id}");
        }

        // Crear el usuario super admin
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        $this->info("✓ Usuario administrador creado exitosamente");
        $this->info("  ID: {$user->id}");
        $this->info("  Nombre: {$user->name}");
        $this->info("  Email: {$user->email}");
        $this->info("  Tenant ID: {$user->tenant_id}");

        return 0;
    }
}
