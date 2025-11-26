<?php

namespace App\Console\Commands;

use App\Exports\MonthlyBackupExport;
use App\Mail\MonthlyBackupMail;
use App\Models\Tenant;
use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class MonthlyBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:monthly
                            {--tenant-id= : ID del tenant específico (opcional, si no se especifica procesa todos)}
                            {--force : Ejecutar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía backup mensual de documentos por email a todos los tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando proceso de backup mensual...');

        // Obtener mes y año anterior
        $previousMonth = Carbon::now()->subMonth();
        $year = $previousMonth->year;
        $month = $previousMonth->month;
        $monthName = $this->getSpanishMonthName($month);

        $this->info("📅 Generando backups para: {$monthName} {$year}");

        // Obtener tenants a procesar
        $tenants = $this->getTenants();

        if ($tenants->isEmpty()) {
            $this->warn('⚠️  No se encontraron tenants para procesar.');
            return 0;
        }

        $this->info("👥 Tenants a procesar: {$tenants->count()}");

        // Confirmar ejecución si no es --force
        if (!$this->option('force')) {
            if (!$this->confirm('¿Deseas continuar?')) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        $stats = [
            'total' => $tenants->count(),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($tenants as $tenant) {
            try {
                $result = $this->processTenant($tenant, $year, $month, $monthName);

                if ($result === 'skipped') {
                    $stats['skipped']++;
                } else {
                    $stats['success']++;
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                $this->error("\n❌ Error procesando tenant {$tenant->name}: " . $e->getMessage());
                logger()->error("Monthly backup failed for tenant {$tenant->id}", [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar estadísticas
        $this->info('✅ Proceso completado!');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total tenants', $stats['total']],
                ['Enviados exitosamente', $stats['success']],
                ['Sin documentos (omitidos)', $stats['skipped']],
                ['Con errores', $stats['failed']],
            ]
        );

        return 0;
    }

    /**
     * Obtener tenants a procesar
     */
    protected function getTenants()
    {
        if ($tenantId = $this->option('tenant-id')) {
            return Tenant::where('id', $tenantId)->get();
        }

        return Tenant::whereNotNull('email')->get();
    }

    /**
     * Procesar backup para un tenant específico
     */
    protected function processTenant(Tenant $tenant, int $year, int $month, string $monthName)
    {
        // Verificar que el tenant tenga email
        if (!$tenant->email) {
            $this->warn("\n⚠️  Tenant {$tenant->name} no tiene email configurado. Omitiendo...");
            return 'skipped';
        }

        // Verificar que haya documentos del mes
        $documentCount = Document::where('tenant_id', $tenant->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        if ($documentCount === 0) {
            $this->warn("\n⚠️  Tenant {$tenant->name} no tiene documentos en {$monthName} {$year}. Omitiendo...");
            return 'skipped';
        }

        // Generar Excel
        $export = new MonthlyBackupExport($tenant->id, $year, $month);
        $filename = "backup_dataflow_{$tenant->id}_{$year}_{$month}.xlsx";
        $tempPath = storage_path("app/temp/{$filename}");

        // Crear directorio temporal si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Exportar a archivo temporal
        Excel::store($export, "temp/{$filename}", 'local');

        // Enviar email
        Mail::to($tenant->email)->send(
            new MonthlyBackupMail($tenant, $monthName, $year, $tempPath)
        );

        // Eliminar archivo temporal
        Storage::disk('local')->delete("temp/{$filename}");

        $this->info("\n✅ Backup enviado a {$tenant->name} ({$tenant->email}) - {$documentCount} documentos");

        // Log de éxito
        logger()->info("Monthly backup sent successfully", [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'email' => $tenant->email,
            'document_count' => $documentCount,
            'year' => $year,
            'month' => $month,
        ]);

        return 'success';
    }

    /**
     * Obtener nombre del mes en español
     */
    protected function getSpanishMonthName(int $month): string
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $months[$month] ?? 'Desconocido';
    }
}
