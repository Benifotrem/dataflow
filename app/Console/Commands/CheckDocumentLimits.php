<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CheckDocumentLimits extends Command
{
    protected $signature = 'contaplus:check-limits';
    protected $description = 'Verifica límites de documentos y notifica a tenants que han excedido';

    public function handle()
    {
        $tenants = Tenant::where('status', 'active')->get();
        $exceeded = 0;

        foreach ($tenants as $tenant) {
            if ($tenant->hasExceededDocumentLimit()) {
                $exceeded++;
                // TODO: Enviar notificación al tenant
                $this->warn("Tenant #{$tenant->id} ({$tenant->name}) ha excedido el límite de documentos");
            }
        }

        $this->info("✓ Verificación completada. {$exceeded} tenants han excedido límites");

        return Command::SUCCESS;
    }
}
