<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Services\BrevoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send-monthly {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar informes mensuales por email a todos los tenants activos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envío de informes mensuales...');

        $brevoService = new BrevoService();

        if (!$brevoService->isConfigured()) {
            $this->error('Brevo no está configurado. Verifica la API key.');
            return 1;
        }

        // Fecha del mes anterior
        $previousMonth = Carbon::now()->subMonth();
        $startDate = $previousMonth->copy()->startOfMonth();
        $endDate = $previousMonth->copy()->endOfMonth();

        $this->info("Generando reportes para: {$previousMonth->locale('es')->format('F Y')}");

        // Si se especifica un tenant específico
        if ($tenantId = $this->option('tenant')) {
            $tenants = Tenant::where('id', $tenantId)->where('status', 'active')->get();
        } else {
            $tenants = Tenant::where('status', 'active')->get();
        }

        $this->info("Procesando {$tenants->count()} tenants...");

        $sent = 0;
        $errors = 0;

        foreach ($tenants as $tenant) {
            try {
                // Obtener el owner del tenant
                $owner = $tenant->users()->where('role', 'owner')->first();

                if (!$owner) {
                    $this->warn("Tenant {$tenant->id} no tiene owner. Saltando...");
                    continue;
                }

                // Recopilar datos del mes
                $documentsCount = Document::where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $transactions = Transaction::where('tenant_id', $tenant->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $transactionsCount = $transactions->count();

                $totalIncome = $transactions->where('type', 'income')->sum('amount');
                $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
                $balance = $totalIncome - $totalExpenses;

                // Preparar datos del reporte
                $reportData = [
                    'month' => $previousMonth->locale('es')->format('F'),
                    'year' => $previousMonth->year,
                    'documents_count' => $documentsCount,
                    'transactions_count' => $transactionsCount,
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
                    'balance' => $balance,
                    'currency' => $tenant->currency_code ?? 'USD',
                ];

                // Enviar email
                $success = $brevoService->sendMonthlyReport(
                    $owner->email,
                    $owner->name,
                    $reportData
                );

                if ($success) {
                    $sent++;
                    $this->info("✓ Informe enviado a: {$owner->email} (Tenant: {$tenant->name})");
                } else {
                    $errors++;
                    $this->error("✗ Error al enviar a: {$owner->email}");
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Error procesando tenant {$tenant->id}: {$e->getMessage()}");
                Log::error("Error enviando informe mensual", [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("\n=== Resumen ===");
        $this->info("Informes enviados: {$sent}");
        if ($errors > 0) {
            $this->error("Errores: {$errors}");
        }

        return $errors > 0 ? 1 : 0;
    }
}
