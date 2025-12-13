<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TelegramMiniAppAuth
{
    /**
     * Validar autenticación de Telegram Mini App
     * Verifica HMAC-SHA256 según documentación oficial de Telegram
     */
    public function handle(Request $request, Closure $next)
    {
        $initData = $request->header('X-Telegram-Init-Data');

        if (!$initData) {
            $initData = $request->input('_auth');
        }

        if (!$initData) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Telegram init data not provided'
            ], 401);
        }

        // Validar hash HMAC-SHA256
        if (!$this->validateTelegramData($initData)) {
            Log::warning('Invalid Telegram Mini App authentication', [
                'init_data' => $initData
            ]);

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid Telegram authentication'
            ], 401);
        }

        // Extraer user_id de Telegram
        $telegramUserId = $this->extractUserId($initData);

        if (!$telegramUserId) {
            return response()->json([
                'error' => 'Bad Request',
                'message' => 'Could not extract Telegram user ID'
            ], 400);
        }

        // Buscar usuario vinculado
        $user = User::where('telegram_id', $telegramUserId)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Telegram account not linked to any user. Please link your account first.',
                'telegram_id' => $telegramUserId
            ], 403);
        }

        // Autenticar usuario
        auth()->login($user);

        // Agregar datos de Telegram al request
        $request->merge([
            'telegram_user' => $this->extractUserData($initData)
        ]);

        return $next($request);
    }

    /**
     * Validar hash HMAC-SHA256 de Telegram
     */
    protected function validateTelegramData(string $initData): bool
    {
        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            Log::error('Telegram bot token not configured');
            return false;
        }

        parse_str($initData, $params);

        if (!isset($params['hash'])) {
            return false;
        }

        $hash = $params['hash'];
        unset($params['hash']);

        // Ordenar parámetros alfabéticamente
        ksort($params);

        // Crear string de verificación
        $dataCheckString = [];
        foreach ($params as $key => $value) {
            $dataCheckString[] = $key . '=' . $value;
        }
        $dataCheckString = implode("\n", $dataCheckString);

        // Calcular secret key
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

        // Calcular hash esperado
        $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($expectedHash, $hash);
    }

    /**
     * Extraer ID de usuario de Telegram
     */
    protected function extractUserId(string $initData): ?string
    {
        parse_str($initData, $params);

        if (isset($params['user'])) {
            $userData = json_decode($params['user'], true);
            return $userData['id'] ?? null;
        }

        return null;
    }

    /**
     * Extraer datos completos del usuario
     */
    protected function extractUserData(string $initData): ?array
    {
        parse_str($initData, $params);

        if (isset($params['user'])) {
            return json_decode($params['user'], true);
        }

        return null;
    }
}
