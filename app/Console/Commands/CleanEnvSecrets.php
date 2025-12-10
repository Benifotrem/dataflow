<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanEnvSecrets extends Command
{
    protected $signature = 'secrets:clean-env {--backup : Crear backup del .env antes de limpiar}';
    protected $description = 'Limpiar secretos del .env después de migrarlos a la bóveda';

    protected array $secretsToClean = [
        'OPENAI_API_KEY',
        'DNIT_USERNAME',
        'DNIT_PASSWORD',
        'TELEGRAM_BOT_TOKEN',
        'PAGOPAR_PUBLIC_KEY',
        'PAGOPAR_PRIVATE_KEY',
        'DB_PASSWORD',
        'MAIL_PASSWORD',
        'BREVO_API_KEY',
    ];

    public function handle(): int
    {
        $this->warn('🔥 LIMPIEZA DE SECRETOS DEL .ENV');
        $this->newLine();

        if (!$this->confirm('ADVERTENCIA: Esto reemplazará los valores reales con [VAULT]. ¿Continuar?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env no encontrado');
            return 1;
        }

        // Backup si se solicita
        if ($this->option('backup')) {
            $backupPath = base_path('.env.backup.' . now()->format('Y-m-d_His'));
            File::copy($envPath, $backupPath);
            $this->info("✅ Backup creado: {$backupPath}");
        }

        $envContent = File::get($envPath);
        $cleaned = 0;

        foreach ($this->secretsToClean as $key) {
            // Reemplazar cualquier valor después del = con [VAULT]
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=[VAULT]";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
                $this->info("🧹 Limpiado: {$key}");
                $cleaned++;
            }
        }

        File::put($envPath, $envContent);

        $this->newLine();
        $this->info("✅ {$cleaned} secretos reemplazados con [VAULT]");
        $this->info('🔐 Los valores reales ahora solo existen en la bóveda encriptada.');

        return 0;
    }
}
