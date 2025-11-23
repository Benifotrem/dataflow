<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AiUsage;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard principal
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        // Estadísticas generales
        $stats = [
            'documents_total' => Document::where('tenant_id', $tenant->id)->count(),
            'documents_pending' => 0, // TODO: Agregar columna status a documents
            'documents_processing' => 0, // TODO: Agregar columna status a documents
            'transactions_total' => Transaction::where('tenant_id', $tenant->id)->count(),
            'transactions_this_month' => 0, // TODO: Verificar nombre de columna de fecha
            'entities_total' => Entity::where('tenant_id', $tenant->id)->count(),
        ];

        // Uso de documentos del mes actual
        $documentsUsed = $tenant->getCurrentMonthDocumentCount();

        // Obtener límite de documentos de la suscripción activa
        $subscription = $tenant->activeSubscription();
        $documentLimit = $subscription ? $subscription->document_limit : 100; // Fallback a 100 si no hay suscripción

        // Agregar documentos de addons activos
        $addonDocuments = $tenant->addons()
            ->where('year', date('Y'))
            ->where('month', date('n'))
            ->where('status', 'active')
            ->sum('document_quantity');

        $totalLimit = $documentLimit + $addonDocuments;
        $percentage = $totalLimit > 0 ? round(($documentsUsed / $totalLimit) * 100, 1) : 0;

        $documentStats = [
            'used' => $documentsUsed,
            'limit' => $totalLimit,
            'percentage' => $percentage,
            'near_limit' => $percentage >= 80, // Alerta en 80%
            'at_limit' => $documentsUsed >= $totalLimit,
            'warning_level' => $percentage >= 95 ? 'danger' : ($percentage >= 80 ? 'warning' : 'normal'),
        ];

        // Documentos recientes
        $recentDocuments = Document::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Transacciones recientes
        $recentTransactions = Transaction::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'documentStats', 'recentDocuments', 'recentTransactions'));
    }
}
