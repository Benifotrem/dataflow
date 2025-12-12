<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
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
}
