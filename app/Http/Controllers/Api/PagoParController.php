<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PagoParService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoParController extends Controller
{
    protected PagoParService $pagoParService;

    public function __construct(PagoParService $pagoParService)
    {
        $this->pagoParService = $pagoParService;
    }

    /**
     * Manejar webhook de PagoPar
     */
    public function webhook(Request $request)
    {
        try {
            $webhookData = $request->all();

            Log::info('Webhook de PagoPar recibido', ['data' => $webhookData]);

            $result = $this->pagoParService->processWebhook($webhookData);

            if ($result) {
                return response()->json(['status' => 'success'], 200);
            }

            return response()->json(['status' => 'error', 'message' => 'Failed to process webhook'], 400);

        } catch (\Exception $e) {
            Log::error('Error en webhook de PagoPar', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
