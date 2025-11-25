<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\PagoParService;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired
                            {--notify : Enviar notificaciones a los usuarios}
                            {--auto-generate-links : Generar enlaces de pago automáticamente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar suscripciones vencidas y enviar notificaciones por Telegram';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService, PagoParService $pagoParService): int
    {
        $this->info('🔍 Verificando suscripciones vencidas...');

        // Obtener suscripciones vencidas o próximas a vencer (7 días)
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->where('payment_status', '!=', 'completed')
            ->get();

        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->where('payment_status', '!=', 'completed')
            ->get();

        $this->line('');
        $this->line("Suscripciones vencidas: {$expiredSubscriptions->count()}");
        $this->line("Suscripciones por vencer (7 días): {$expiringSubscriptions->count()}");
        $this->line('');

        $notified = 0;
        $linksGenerated = 0;

        // Procesar suscripciones vencidas
        foreach ($expiredSubscriptions as $subscription) {
            $this->warn("⚠️  Suscripción vencida: {$subscription->tenant->name} (ID: {$subscription->id})");

            // Marcar como expirada
            if ($subscription->status !== 'expired') {
                $subscription->update(['status' => 'expired']);
            }

            if ($this->option('notify')) {
                $result = $this->notifyExpiredSubscription($subscription, $telegramService, $pagoParService);

                if ($result) {
                    $notified++;
                }
            }

            if ($this->option('auto-generate-links')) {
                $result = $this->generatePaymentLink($subscription, $pagoParService);

                if ($result) {
                    $linksGenerated++;
                }
            }
        }

        // Procesar suscripciones por vencer
        foreach ($expiringSubscriptions as $subscription) {
            $daysUntilExpiry = now()->diffInDays($subscription->expires_at);
            $this->info("📅 Suscripción por vencer en {$daysUntilExpiry} días: {$subscription->tenant->name}");

            if ($this->option('notify')) {
                $result = $this->notifyExpiringSubscription($subscription, $telegramService, $daysUntilExpiry);

                if ($result) {
                    $notified++;
                }
            }
        }

        $this->line('');
        $this->info("✅ Proceso completado");

        if ($this->option('notify')) {
            $this->line("📧 Notificaciones enviadas: {$notified}");
        }

        if ($this->option('auto-generate-links')) {
            $this->line("🔗 Enlaces de pago generados: {$linksGenerated}");
        }

        Log::info('Verificación de suscripciones completada', [
            'expired' => $expiredSubscriptions->count(),
            'expiring' => $expiringSubscriptions->count(),
            'notified' => $notified,
            'links_generated' => $linksGenerated,
        ]);

        return 0;
    }

    /**
     * Notificar suscripción vencida
     */
    protected function notifyExpiredSubscription(
        Subscription $subscription,
        TelegramService $telegramService,
        PagoParService $pagoParService
    ): bool {
        try {
            $owner = $subscription->tenant->users()->where('role', 'owner')->first();

            if (!$owner || !$owner->hasTelegramLinked()) {
                $this->line("  ⚠️  Usuario sin Telegram vinculado");
                return false;
            }

            $currency = $subscription->tenant->currency_code ?? 'USD';
            $formattedAmount = $pagoParService->formatAmount($subscription->price, $currency);

            $message = "⚠️ <b>Suscripción Vencida</b>\n\n";
            $message .= "Tu suscripción de Dataflow ha vencido.\n\n";
            $message .= "🏢 Empresa: {$subscription->tenant->name}\n";
            $message .= "📦 Plan: <b>{$subscription->plan}</b>\n";
            $message .= "💰 Monto: <b>{$formattedAmount}</b>\n";
            $message .= "📅 Venció el: {$subscription->expires_at->format('d/m/Y')}\n\n";
            $message .= "⚠️ <b>Funcionalidades limitadas</b>\n";
            $message .= "No podrás procesar nuevos documentos hasta renovar tu suscripción.\n\n";
            $message .= "Usa /pagar para renovar tu suscripción.";

            $telegramService->sendMessage($owner->telegram_chat_id, $message);

            $subscription->update(['payment_notified_at' => now()]);

            $this->line("  ✅ Notificación enviada a {$owner->name}");

            return true;

        } catch (\Exception $e) {
            $this->error("  ❌ Error al notificar: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Notificar suscripción por vencer
     */
    protected function notifyExpiringSubscription(
        Subscription $subscription,
        TelegramService $telegramService,
        int $daysUntilExpiry
    ): bool {
        try {
            $owner = $subscription->tenant->users()->where('role', 'owner')->first();

            if (!$owner || !$owner->hasTelegramLinked()) {
                return false;
            }

            // Solo notificar en días específicos (7, 3, 1)
            if (!in_array($daysUntilExpiry, [7, 3, 1])) {
                return false;
            }

            // Verificar si ya se notificó hoy
            if ($subscription->payment_notified_at && $subscription->payment_notified_at->isToday()) {
                return false;
            }

            $message = "⏰ <b>Recordatorio de Renovación</b>\n\n";
            $message .= "Tu suscripción de Dataflow vence en <b>{$daysUntilExpiry} día(s)</b>.\n\n";
            $message .= "🏢 Empresa: {$subscription->tenant->name}\n";
            $message .= "📦 Plan: <b>{$subscription->plan}</b>\n";
            $message .= "📅 Vence el: {$subscription->expires_at->format('d/m/Y')}\n\n";
            $message .= "Renueva tu suscripción para seguir disfrutando de todos los beneficios de Dataflow.\n\n";
            $message .= "Usa /pagar para renovar ahora.";

            $telegramService->sendMessage($owner->telegram_chat_id, $message);

            $subscription->update(['payment_notified_at' => now()]);

            $this->line("  ✅ Recordatorio enviado a {$owner->name}");

            return true;

        } catch (\Exception $e) {
            $this->error("  ❌ Error al enviar recordatorio: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Generar enlace de pago
     */
    protected function generatePaymentLink(Subscription $subscription, PagoParService $pagoParService): bool
    {
        try {
            if ($subscription->hasPaymentLink()) {
                $this->line("  ℹ️  Ya tiene enlace de pago generado");
                return false;
            }

            $result = $pagoParService->generatePaymentLink($subscription);

            if ($result && $result['success']) {
                $this->line("  ✅ Enlace de pago generado");
                return true;
            }

            $this->error("  ❌ Error al generar enlace de pago");
            return false;

        } catch (\Exception $e) {
            $this->error("  ❌ Error: {$e->getMessage()}");
            return false;
        }
    }
}
