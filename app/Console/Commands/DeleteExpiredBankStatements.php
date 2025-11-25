<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;

class DeleteExpiredBankStatements extends Command
{
    protected $signature = 'dataflow:delete-expired-statements';
    protected $description = 'Elimina extractos bancarios que han excedido el período de retención de 60 días';

    public function handle(DataRetentionService $service)
    {
        $this->info('Iniciando eliminación de extractos bancarios expirados...');
        
        $deleted = $service->deleteExpiredBankStatements();
        
        $this->info("✓ {$deleted} extractos eliminados exitosamente");
        
        // Enviar advertencias
        $warnings = $service->sendExpirationWarnings();
        $this->info("✓ {$warnings} advertencias enviadas");
        
        return Command::SUCCESS;
    }
}
