<?php

namespace App\Services;

use App\Models\BankStatement;
use App\Models\Transaction;
use Carbon\Carbon;

class BankReconciliationService
{
    public function reconcileStatement(BankStatement $statement)
    {
        $statement->update(['status' => 'processing']);

        try {
            // Extraer transacciones del extracto (simplificado)
            $extractedTransactions = $this->extractTransactionsFromStatement($statement);

            // Buscar transacciones coincidentes en la entidad
            $reconciled = 0;
            foreach ($extractedTransactions as $extracted) {
                $transaction = Transaction::where('entity_id', $statement->entity_id)
                    ->where('amount', $extracted['amount'])
                    ->where('transaction_date', $extracted['date'])
                    ->where('reconciled', false)
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'reconciled' => true,
                        'reconciled_at' => now(),
                        'reference' => $extracted['reference'] ?? null,
                    ]);
                    $reconciled++;
                }
            }

            $statement->update([
                'status' => 'processed',
                'metadata' => array_merge($statement->metadata ?? [], [
                    'transactions_reconciled' => $reconciled,
                    'processed_at' => now()->toDateTimeString(),
                ])
            ]);

            return $reconciled;

        } catch (\Exception $e) {
            $statement->update(['status' => 'failed']);
            throw $e;
        }
    }

    protected function extractTransactionsFromStatement(BankStatement $statement)
    {
        // Simplificado: en producción, usarías OCR o parsing de CSV/Excel
        return [];
    }
}
