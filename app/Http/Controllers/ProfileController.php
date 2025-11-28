<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    /**
     * Mostrar el perfil del usuario
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Contraseña actualizada exitosamente');
    }

    /**
     * Generar código de vinculación de Telegram
     */
    public function generateTelegramCode()
    {
        $user = Auth::user();
        $code = $this->telegramService->generateLinkCode($user);

        return back()->with('telegram_code', $code);
    }

    /**
     * Desvincular cuenta de Telegram
     */
    public function unlinkTelegram()
    {
        $user = Auth::user();

        if (!$user->hasTelegramLinked()) {
            return back()->withErrors(['telegram' => 'No tienes una cuenta de Telegram vinculada']);
        }

        $user->unlinkTelegram();

        return back()->with('success', 'Cuenta de Telegram desvinculada exitosamente');
    }
}
