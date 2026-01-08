<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AccountCancellationRequest;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Mostrar configuraciones del usuario
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        return view('dashboard.settings.index', compact('user', 'tenant'));
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'in:es,en,pt'],
            'timezone' => ['nullable', 'string'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ]);

        // Si el usuario tiene preferencias en una tabla separada, actualizarlas aquí
        // Por ahora solo mostramos un mensaje de éxito

        return redirect()->route('settings.index')
            ->with('success', 'Configuración actualizada exitosamente.');
    }

    /**
     * Vincular cuenta de Telegram
     */
    public function linkTelegram(Request $request)
    {
        $validated = $request->validate([
            'telegram_id' => ['required', 'numeric'],
        ]);

        $user = $request->user();
        $telegramId = $validated['telegram_id'];

        // Verificar que el Telegram ID no esté ya vinculado a otra cuenta
        $existingUser = $this->telegramService->findUserByTelegramId($telegramId);

        if ($existingUser && $existingUser->id !== $user->id) {
            return redirect()->route('settings.index')
                ->withErrors(['telegram_id' => 'Este Telegram ID ya está vinculado a otra cuenta.']);
        }

        // Vincular la cuenta
        $user->linkTelegram($telegramId, null, null);

        Log::info('Usuario vinculó Telegram desde dashboard', [
            'user_id' => $user->id,
            'telegram_id' => $telegramId,
        ]);

        // Intentar enviar mensaje de confirmación si es posible
        try {
            $this->telegramService->sendMessage(
                $telegramId,
                "✅ <b>¡Cuenta vinculada exitosamente!</b>\n\n" .
                "👤 Usuario: {$user->name}\n" .
                "📧 Email: {$user->email}\n\n" .
                "Ahora puedes enviar facturas directamente por Telegram.\n" .
                "Simplemente envía el PDF o foto de la factura."
            );
        } catch (\Exception $e) {
            // Si no se puede enviar el mensaje, no es crítico
            Log::warning('No se pudo enviar mensaje de confirmación de vinculación', [
                'telegram_id' => $telegramId,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', '✅ Cuenta de Telegram vinculada exitosamente. Ya puedes enviar facturas al bot.');
    }

    /**
     * Desvincular cuenta de Telegram
     */
    public function unlinkTelegram(Request $request)
    {
        $user = $request->user();

        if (!$user->hasTelegramLinked()) {
            return redirect()->route('settings.index')
                ->withErrors(['error' => 'No tienes ninguna cuenta de Telegram vinculada.']);
        }

        $user->unlinkTelegram();

        Log::info('Usuario desvinculó Telegram desde dashboard', [
            'user_id' => $user->id,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Cuenta de Telegram desvinculada exitosamente.');
    }

    /**
     * Solicitar cancelación de cuenta
     */
    public function requestCancellation(Request $request)
    {
        $validated = $request->validate([
            'reasons' => ['required', 'array', 'min:1'],
            'reasons.*' => ['string'],
            'other_reason' => ['nullable', 'string', 'max:1000'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'accepted_offer' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $tenant = $user->tenant;

        // Si el usuario aceptó una oferta de retención
        if (!empty($validated['accepted_offer'])) {
            $cancellationRequest = AccountCancellationRequest::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'reasons' => $validated['reasons'],
                'other_reason' => $validated['other_reason'] ?? null,
                'feedback' => $validated['feedback'] ?? null,
                'retention_offer' => $validated['accepted_offer'],
                'accepted_offer' => true,
                'status' => 'retained',
            ]);

            Log::info('Usuario aceptó oferta de retención', [
                'user_id' => $user->id,
                'offer' => $validated['accepted_offer'],
                'reasons' => $validated['reasons'],
            ]);

            // Notificar al equipo sobre la retención exitosa
            // TODO: Enviar email al equipo de soporte

            return redirect()->route('settings.index')
                ->with('success', '🎉 ¡Genial! Nos alegra que decidas quedarte. Tu oferta se activará en breve y nos pondremos en contacto contigo.');
        }

        // Si no aceptó ninguna oferta, proceder con la cancelación
        return $this->processCancellation($request, $user, $tenant, $validated);
    }

    /**
     * Procesar la cancelación definitiva de la cuenta
     */
    protected function processCancellation(Request $request, $user, $tenant, array $data)
    {
        DB::beginTransaction();

        try {
            // Crear registro de cancelación
            $cancellationRequest = AccountCancellationRequest::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'reasons' => $data['reasons'],
                'other_reason' => $data['other_reason'] ?? null,
                'feedback' => $data['feedback'] ?? null,
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            Log::warning('Usuario canceló su cuenta', [
                'user_id' => $user->id,
                'email' => $user->email,
                'tenant_id' => $tenant->id,
                'reasons' => $data['reasons'],
            ]);

            // Eliminar datos del usuario
            // 1. Soft delete de documentos
            $user->documents()->delete();

            // 2. Eliminar notificaciones
            $user->notifications()->delete();

            // 3. Eliminar conversaciones de Telegram
            DB::table('telegram_conversations')->where('user_id', $user->id)->delete();

            // 4. Si es el único usuario del tenant, desactivar el tenant
            $tenantUsersCount = $tenant->users()->count();
            if ($tenantUsersCount === 1) {
                $tenant->update(['status' => 'cancelled']);
                Log::info('Tenant desactivado (último usuario)', ['tenant_id' => $tenant->id]);
            }

            // 5. Soft delete del usuario
            $user->delete();

            DB::commit();

            // Cerrar sesión
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Notificar al equipo sobre la cancelación
            // TODO: Enviar email al equipo con los motivos

            return redirect()->route('login')
                ->with('info', 'Tu cuenta ha sido cancelada. Lamentamos verte partir. Si cambias de opinión, contáctanos en soporte@guaraniappstore.com');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al procesar cancelación de cuenta', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('settings.index')
                ->withErrors(['error' => 'Hubo un error al procesar la cancelación. Por favor, contacta a soporte.']);
        }
    }
}
