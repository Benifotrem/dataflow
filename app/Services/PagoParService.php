<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoParService
{
    protected string $publicKey;
    protected string $privateKey;
    protected bool $sandbox;
    protected string $baseUrl;

    public function __construct()
    {
        $this->publicKey = config('services.pagopar.public_key');
        $this->privateKey = config('services.pagopar.private_key');
        $this->sandbox = config('services.pagopar.sandbox', true);

        // URL base según el ambiente
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.pagopar.com/api/v1'
            : 'https://api.pagopar.com/api/v1';
    }

    /**
     * Generar enlace de pago para una suscripción
     */
    public function generatePaymentLink(Subscription $subscription): ?array
    {
        try {
            $reference = $subscription->generatePaymentReference();
            $tenant = $subscription->tenant;

            // Datos del pago
            $paymentData = [
                'public_key' => $this->publicKey,
                'operation' => [
                    'token' => $this->privateKey,
                ],
                'process' => [
                    'public' => true,
                    'deferred_capture' => false,
                ],
                'request' => [
                    'currency' => $tenant->currency_code ?? 'PYG',
                    'amount' => $this->convertToPagoParAmount($subscription->price, $tenant->currency_code ?? 'PYG'),
                    'description' => "Suscripción {$subscription->plan} - {$tenant->name}",
                    'additional_data' => json_encode([
                        'subscription_id' => $subscription->id,
                        'tenant_id' => $tenant->id,
                        'plan' => $subscription->plan,
                    ]),
                ],
                'redirects' => [
                    'success' => config('app.url') . '/payment/success',
                    'fail' => config('app.url') . '/payment/fail',
                ],
                'notifications' => [
                    'webhook_url' => config('services.pagopar.webhook_url'),
                ],
            ];

            Log::info('Generando enlace de pago PagoPar', [
                'subscription_id' => $subscription->id,
                'reference' => $reference,
                'amount' => $paymentData['request']['amount'],
            ]);

            // Llamada a la API de PagoPar
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/payments/create", $paymentData);

            if (!$response->successful()) {
                Log::error('Error al generar enlace de pago PagoPar', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return null;
            }

            $data = $response->json();

            // Actualizar suscripción con el enlace de pago
            $subscription->update([
                'payment_link' => $data['payment_url'] ?? null,
                'payment_status' => 'pending',
                'payment_metadata' => [
                    'pagopar_transaction_id' => $data['transaction_id'] ?? null,
                    'pagopar_reference' => $reference,
                    'created_at' => now()->toIso8601String(),
                ],
            ]);

            Log::info('Enlace de pago generado exitosamente', [
                'subscription_id' => $subscription->id,
                'payment_url' => $data['payment_url'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
            ]);

            return [
                'success' => true,
                'payment_url' => $data['payment_url'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'reference' => $reference,
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al generar enlace de pago PagoPar', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Verificar estado de un pago
     */
    public function checkPaymentStatus(string $transactionId): ?array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->privateKey}",
                ])
                ->get("{$this->baseUrl}/payments/{$transactionId}/status");

            if (!$response->successful()) {
                Log::error('Error al verificar estado de pago PagoPar', [
                    'transaction_id' => $transactionId,
                    'status' => $response->status(),
                ]);

                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Excepción al verificar estado de pago', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Procesar webhook de PagoPar
     */
    public function processWebhook(array $webhookData): bool
    {
        try {
            Log::info('Procesando webhook de PagoPar', ['data' => $webhookData]);

            // Validar firma del webhook (si PagoPar lo soporta)
            if (!$this->validateWebhookSignature($webhookData)) {
                Log::warning('Firma de webhook inválida');
                return false;
            }

            $transactionId = $webhookData['transaction_id'] ?? null;
            $status = $webhookData['status'] ?? null;

            if (!$transactionId || !$status) {
                Log::error('Webhook incompleto', ['data' => $webhookData]);
                return false;
            }

            // Buscar suscripción por transaction_id
            $subscription = Subscription::where('payment_metadata->pagopar_transaction_id', $transactionId)->first();

            if (!$subscription) {
                Log::warning('Suscripción no encontrada para transaction_id', [
                    'transaction_id' => $transactionId,
                ]);
                return false;
            }

            // Actualizar estado del pago según respuesta
            switch ($status) {
                case 'approved':
                case 'completed':
                    $subscription->update([
                        'payment_status' => 'completed',
                        'payment_completed_at' => now(),
                        'payment_transaction_id' => $transactionId,
                        'status' => 'active',
                        'starts_at' => now(),
                        'expires_at' => now()->addMonth(),
                    ]);

                    Log::info('Pago completado y suscripción activada', [
                        'subscription_id' => $subscription->id,
                        'transaction_id' => $transactionId,
                    ]);

                    // Notificar al usuario por Telegram
                    $this->notifyPaymentSuccess($subscription);
                    break;

                case 'failed':
                case 'rejected':
                    $subscription->update([
                        'payment_status' => 'failed',
                    ]);

                    Log::info('Pago rechazado', [
                        'subscription_id' => $subscription->id,
                        'transaction_id' => $transactionId,
                    ]);

                    // Notificar al usuario por Telegram
                    $this->notifyPaymentFailure($subscription);
                    break;

                case 'pending':
                case 'processing':
                    $subscription->update([
                        'payment_status' => 'processing',
                    ]);
                    break;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error al procesar webhook de PagoPar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Validar firma del webhook
     */
    protected function validateWebhookSignature(array $webhookData): bool
    {
        // Implementar validación de firma según documentación de PagoPar
        // Por ahora retornamos true
        return true;
    }

    /**
     * Notificar pago exitoso por Telegram
     */
    protected function notifyPaymentSuccess(Subscription $subscription): void
    {
        try {
            $telegramService = app(TelegramService::class);
            $owner = $subscription->tenant->users()->where('role', 'owner')->first();

            if ($owner && $owner->hasTelegramLinked()) {
                $message = "✅ <b>¡Pago recibido!</b>\n\n";
                $message .= "Tu suscripción <b>{$subscription->plan}</b> ha sido activada.\n\n";
                $message .= "📅 <b>Válida hasta:</b> {$subscription->expires_at->format('d/m/Y')}\n";
                $message .= "💰 <b>Monto:</b> " . number_format($subscription->price, 0) . " {$subscription->tenant->currency_code}\n\n";
                $message .= "¡Gracias por confiar en Dataflow!";

                $telegramService->sendMessage($owner->telegram_chat_id, $message);

                $subscription->update(['payment_notified_at' => now()]);
            }
        } catch (\Exception $e) {
            Log::error('Error al notificar pago exitoso por Telegram', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notificar pago fallido por Telegram
     */
    protected function notifyPaymentFailure(Subscription $subscription): void
    {
        try {
            $telegramService = app(TelegramService::class);
            $owner = $subscription->tenant->users()->where('role', 'owner')->first();

            if ($owner && $owner->hasTelegramLinked()) {
                $message = "❌ <b>Pago rechazado</b>\n\n";
                $message .= "Tu pago no pudo ser procesado.\n\n";
                $message .= "Por favor, intenta nuevamente o usa otro método de pago.\n";
                $message .= "Usa /pagar para generar un nuevo enlace de pago.";

                $telegramService->sendMessage($owner->telegram_chat_id, $message);
            }
        } catch (\Exception $e) {
            Log::error('Error al notificar pago fallido por Telegram', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Convertir monto según la moneda para PagoPar
     */
    protected function convertToPagoParAmount(float $amount, string $currency): int
    {
        // PagoPar generalmente requiere montos en centavos
        // Para PYG (guaraníes), no hay centavos
        if ($currency === 'PYG') {
            return (int) $amount;
        }

        // Para otras monedas, multiplicar por 100
        return (int) ($amount * 100);
    }

    /**
     * Formatear monto para mostrar
     */
    public function formatAmount(float $amount, string $currency): string
    {
        if ($currency === 'PYG') {
            return number_format($amount, 0, ',', '.') . ' Gs.';
        }

        return number_format($amount, 2, ',', '.') . ' ' . $currency;
    }
}
