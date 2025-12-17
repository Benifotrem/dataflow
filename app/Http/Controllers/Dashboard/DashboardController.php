<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AiUsage;
use App\Models\Document;
use App\Models\Entity;
use App\Models\Notification;
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

    /**
     * Obtener notificaciones recientes
     * GET /notifications/recent
     */
    public function recentNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::forTenant($user->tenant_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Notification::forTenant($user->tenant_id)->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Obtener contador de no leídas
     * GET /notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = Notification::forTenant($user->tenant_id)->unread()->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Marcar notificación como leída
     * POST /notifications/{id}/mark-read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = Notification::forTenant($user->tenant_id)->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída',
        ]);
    }

    /**
     * Marcar todas como leídas
     * POST /notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $updated = Notification::forTenant($user->tenant_id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'count' => $updated,
        ]);
    }

    /**
     * Ver todas las notificaciones
     * GET /notifications
     */
    public function allNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::forTenant($user->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.notifications.index', compact('notifications'));
    }
}
