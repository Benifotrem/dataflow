<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class CsvMapperService
{
    public function importTransactions(Entity $entity, $filePath, array $mapping)
    {
        $csv = Storage::get($filePath);
        $rows = array_map('str_getcsv', explode("\n", $csv));
        $header = array_shift($rows);
        
        $imported = 0;

        foreach ($rows as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            $data = array_combine($header, $row);
            
            Transaction::create([
                'tenant_id' => $entity->tenant_id,
                'entity_id' => $entity->id,
                'type' => $this->mapValue($data, $mapping['type']) ?? 'expense',
                'transaction_date' => $this->mapValue($data, $mapping['date']),
                'description' => $this->mapValue($data, $mapping['description']),
                'amount' => $this->mapValue($data, $mapping['amount']),
                'currency' => $entity->currency_code,
                'reference' => $this->mapValue($data, $mapping['reference'] ?? null),
            ]);

            $imported++;
        }

        return $imported;
    }

    protected function mapValue($data, $columnName)
    {
        return $data[$columnName] ?? null;
    }

    public function exportTransactions(Entity $entity)
    {
        $transactions = Transaction::where('entity_id', $entity->id)->get();
        
        $csv = "Fecha,Descripción,Importe,Moneda,Tipo,Categoría\n";
        
        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $transaction->transaction_date->format('Y-m-d'),
                $this->escapeCsv($transaction->description),
                $transaction->amount,
                $transaction->currency,
                $transaction->type,
                $transaction->category ?? ''
            );
        }

        return $csv;
    }

    protected function escapeCsv($value)
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }
}
