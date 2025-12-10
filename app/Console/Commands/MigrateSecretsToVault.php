<?php

namespace App\Console\Commands;

use App\Services\SecretVaultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateSecretsToVault extends Command
{
    protected $signature = 'secrets:migrate {--force : Forzar migración sin confirmación}';
    protected $description = 'Migrar secretos del .env a la bóveda encriptada (Seguridad Militar)';

    protected array $criticalSecrets = [
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

    public function handle(SecretVaultService $vault): int
    {
        $this->info('🔐 SISTEMA DE MIGRACIÓN DE SECRETOS - NIVEL MILITAR');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('¿Migrar secretos a la bóveda encriptada?')) {
                $this->warn('Operación cancelada.');
                return 0;
            }
        }

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($this->criticalSecrets as $key) {
            $value = env($key);

            if (empty($value) || $value === 'your_openai_api_key_here') {
                $this->warn("⏭️  Omitido: {$key} (vacío o placeholder)");
                $skipped++;
                continue;
            }

            try {
                $vault->store($key, $value, [
                    'migrated_at' => now()->toDateTimeString(),
                    'original_source' => '.env',
                ]);

                $this->info("✅ Migrado: {$key}");
                $migrated++;

            } catch (\Exception $e) {
                $this->error("❌ Error en {$key}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info("📊 RESUMEN:");
        $this->info("   ✅ Migrados: {$migrated}");
        $this->info("   ⏭️  Omitidos: {$skipped}");
        $this->info("   ❌ Errores: {$errors}");

        if ($migrated > 0) {
            $this->newLine();
            $this->warn('⚠️  IMPORTANTE:');
            $this->warn('   Los secretos ahora están en la bóveda encriptada.');
            $this->warn('   Considera eliminar los valores del .env por seguridad.');
            $this->newLine();
            $this->info('   Comando para limpiar .env:');
            $this->info('   php artisan secrets:clean-env');
        }

        return 0;
    }
}
