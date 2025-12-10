<?php

namespace App\Console\Commands;

use App\Services\SecretVaultService;
use Illuminate\Console\Command;

class SecretAuditCommand extends Command
{
    protected $signature = 'secrets:audit {--suspicious : Solo mostrar actividad sospechosa}';
    protected $description = 'Auditar accesos a secretos y detectar amenazas';

    public function handle(SecretVaultService $vault): int
    {
        $this->info('🔍 AUDITORÍA DE SEGURIDAD - ANÁLISIS DE AMENAZAS');
        $this->newLine();

        if ($this->option('suspicious')) {
            $this->detectThreats($vault);
        } else {
            $this->fullAudit($vault);
        }

        return 0;
    }

    protected function fullAudit(SecretVaultService $vault): void
    {
        // Listar todos los secretos
        $secrets = $vault->list();
        
        $this->info("📋 SECRETOS ALMACENADOS: " . count($secrets));
        $this->newLine();

        $table = [];
        foreach ($secrets as $secret) {
            $needsRotation = $vault->needsRotation($secret->key);
            
            $table[] = [
                $secret->key,
                $secret->last_rotated_at ?? 'Nunca',
                $needsRotation ? '⚠️  SÍ' : '✅ NO',
            ];
        }

        $this->table(['Secreto', 'Última Rotación', 'Requiere Rotación'], $table);
        $this->newLine();

        // Detectar amenazas
        $this->detectThreats($vault);
    }

    protected function detectThreats(SecretVaultService $vault): void
    {
        $this->warn('🚨 ANÁLISIS DE AMENAZAS');
        $this->newLine();

        $suspicious = $vault->detectSuspiciousActivity();

        if (empty($suspicious)) {
            $this->info('✅ No se detectó actividad sospechosa.');
            return;
        }

        $this->error('⚠️  ACTIVIDAD SOSPECHOSA DETECTADA:');
        $this->newLine();

        foreach ($suspicious as $incident) {
            $this->warn("🔴 Secreto: {$incident['key']}");
            $this->line("   Razón: {$incident['reason']}");
            
            if (isset($incident['ip_count'])) {
                $this->line("   IPs diferentes: {$incident['ip_count']}");
            }
            
            if (isset($incident['access_count'])) {
                $this->line("   Accesos: {$incident['access_count']}");
            }
            
            $this->newLine();
        }

        $this->error('🔔 ACCIÓN REQUERIDA: Investiga estos accesos inmediatamente.');
    }
}
