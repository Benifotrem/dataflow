<?php

namespace App\Console\Commands;

use App\Mail\FiscalEventNotificationMail;
use App\Models\FiscalEvent;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFiscalEventNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiscal:notify
                            {--tenant-id= : ID del tenant específico (opcional)}
                            {--force : Ejecutar sin confirmación}
                            {--dry-run : Mostrar qué se enviaría sin enviar realmente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones de eventos fiscales próximos a los tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗓️  Iniciando verificación de eventos fiscales...');

        $isDryRun = $this->option('dry-run');

        // Obtener eventos que necesitan notificación
        $events = $this->getEventsToNotify();

        if ($events->isEmpty()) {
            $this->info('✅ No hay eventos fiscales que requieran notificación en este momento.');
            return 0;
        }

        $this->info("📧 Eventos a notificar: {$events->count()}");

        if ($isDryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se enviarán emails realmente');
        }

        // Confirmar ejecución si no es --force ni --dry-run
        if (!$this->option('force') && !$isDryRun) {
            if (!$this->confirm('¿Deseas enviar las notificaciones?')) {
                $this->info('❌ Operación cancelada.');
                return 0;
            }
        }

        $progressBar = $this->output->createProgressBar($events->count());
        $progressBar->start();

        $stats = [
            'total' => $events->count(),
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($events as $event) {
            try {
                $result = $this->processEvent($event, $isDryRun);

                if ($result === 'sent') {
                    $stats['sent']++;
                } elseif ($result === 'skipped') {
                    $stats['skipped']++;
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                $this->error("\n❌ Error procesando evento {$event->id}: " . $e->getMessage());
                logger()->error("Fiscal event notification failed", [
                    'event_id' => $event->id,
                    'tenant_id' => $event->tenant_id,
                    'error' => $e->getMessage(),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar estadísticas
        $this->info($isDryRun ? '🔍 Resultado del Dry-Run:' : '✅ Proceso completado!');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total eventos', $stats['total']],
                [$isDryRun ? 'Se enviarían' : 'Notificaciones enviadas', $stats['sent']],
                ['Omitidos', $stats['skipped']],
                ['Con errores', $stats['failed']],
            ]
        );

        return 0;
    }

    /**
     * Obtener eventos que necesitan notificación
     */
    protected function getEventsToNotify()
    {
        $query = FiscalEvent::needsNotification()->with('tenant');

        if ($tenantId = $this->option('tenant-id')) {
            $query->where('tenant_id', $tenantId);
        }

        return $query->get();
    }

    /**
     * Procesar un evento fiscal
     */
    protected function processEvent(FiscalEvent $event, bool $isDryRun)
    {
        $tenant = $event->tenant;

        // Verificar que el tenant tenga email
        if (!$tenant || !$tenant->email) {
            $this->warn("\n⚠️  Evento {$event->title} - Tenant sin email. Omitiendo...");
            return 'skipped';
        }

        $daysUntil = now()->diffInDays($event->event_date, false);

        if ($isDryRun) {
            $this->info("\n📧 [DRY-RUN] Enviaría email a {$tenant->name} ({$tenant->email})");
            $this->info("   └─ Evento: {$event->title}");
            $this->info("   └─ Fecha: {$event->event_date->format('d/m/Y')}");
            $this->info("   └─ Días restantes: {$daysUntil}");
            return 'sent';
        }

        // Enviar email
        Mail::to($tenant->email)->send(
            new FiscalEventNotificationMail($event, $tenant)
        );

        // Marcar como notificado
        $event->markAsNotified();

        $this->info("\n✅ Notificación enviada: {$event->title} → {$tenant->name} ({$tenant->email})");
        $this->info("   └─ Fecha evento: {$event->event_date->format('d/m/Y')} ({$daysUntil} días)");

        // Log de éxito
        logger()->info("Fiscal event notification sent", [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'email' => $tenant->email,
            'event_date' => $event->event_date->format('Y-m-d'),
            'days_until' => $daysUntil,
        ]);

        return 'sent';
    }
}
