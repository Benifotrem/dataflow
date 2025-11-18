<?php

namespace App\Services;

use App\Models\BankStatement;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DataRetentionService
{
    public function deleteExpiredBankStatements()
    {
        $retentionDays = SystemSetting::get('data_retention_days', 60);
        $deleted = 0;

        $expiredStatements = BankStatement::where('retention_expires_at', '<=', now())
            ->where('file_deleted', false)
            ->get();

        foreach ($expiredStatements as $statement) {
            try {
                // Eliminar archivo físico
                if (Storage::exists($statement->file_path)) {
                    Storage::delete($statement->file_path);
                }

                // Marcar como eliminado
                $statement->update([
                    'file_deleted' => true,
                    'file_deleted_at' => now(),
                ]);

                $deleted++;

                Log::info("Extracto bancario #{$statement->id} eliminado por política de retención");

            } catch (\Exception $e) {
                Log::error("Error eliminando extracto #{$statement->id}: " . $e->getMessage());
            }
        }

        return $deleted;
    }

    public function sendExpirationWarnings()
    {
        // Enviar notificaciones 7 días antes de la expiración
        $warningDate = now()->addDays(7);

        $statements = BankStatement::where('retention_expires_at', '<=', $warningDate)
            ->where('retention_expires_at', '>', now())
            ->where('file_deleted', false)
            ->get();

        foreach ($statements as $statement) {
            // TODO: Enviar notificación al usuario
            Log::info("Advertencia: Extracto #{$statement->id} expirará en " . 
                      now()->diffInDays($statement->retention_expires_at) . " días");
        }

        return $statements->count();
    }
}
