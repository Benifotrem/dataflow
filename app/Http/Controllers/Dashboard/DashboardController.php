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
            'transactions_this_month' => Transaction::where('tenant_id', $tenant->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'entities_total' => Entity::where('tenant_id', $tenant->id)->count(),
        ];

        // Uso de IA del mes actual
        $aiUsage = AiUsage::where('tenant_id', $tenant->id)
            ->where('month', now()->format('Y-m'))
            ->first();

        $aiStats = [
            'used' => $aiUsage ? $aiUsage->documents_processed : 0,
            'limit' => 500, // Por ahora hardcodeado, después se obtiene del plan
            'percentage' => $aiUsage ? round(($aiUsage->documents_processed / 500) * 100, 1) : 0,
        ];

        // Documentos recientes
        $recentDocuments = Document::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Transacciones recientes
        $recentTransactions = Transaction::where('tenant_id', $tenant->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'aiStats', 'recentDocuments', 'recentTransactions'));
    }
}
