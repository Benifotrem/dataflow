<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Verificar permisos de admin
     */
    protected function checkAdminPermission()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }
    }

    /**
     * Lista de suscriptores
     */
    public function index(Request $request)
    {
        $this->checkAdminPermission();

        $query = Subscriber::query();

        // Filtros
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'unsubscribed') {
                $query->unsubscribed();
            }
        }

        if ($request->filled('source')) {
            $query->fromSource($request->source);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $subscribers = $query->orderBy('subscribed_at', 'desc')->paginate(50);

        // Estadísticas
        $stats = [
            'total' => Subscriber::count(),
            'active' => Subscriber::active()->count(),
            'unsubscribed' => Subscriber::unsubscribed()->count(),
            'blog' => Subscriber::fromSource('blog')->count(),
            'this_month' => Subscriber::whereMonth('subscribed_at', now()->month)
                ->whereYear('subscribed_at', now()->year)
                ->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'stats'));
    }

    /**
     * Exportar suscriptores a CSV
     */
    public function export(Request $request)
    {
        $this->checkAdminPermission();

        $query = Subscriber::query();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'unsubscribed') {
                $query->unsubscribed();
            }
        }

        $subscribers = $query->orderBy('subscribed_at', 'desc')->get();

        $filename = 'suscriptores-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($file, ['Email', 'Nombre', 'Estado', 'Fecha Suscripción', 'Fuente']);

            // Datos
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name ?? '',
                    $subscriber->status === 'active' ? 'Activo' : 'Desuscrito',
                    $subscriber->subscribed_at->format('Y-m-d H:i:s'),
                    ucfirst($subscriber->source),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cambiar estado de un suscriptor
     */
    public function toggleStatus(Subscriber $subscriber)
    {
        $this->checkAdminPermission();

        if ($subscriber->isActive()) {
            $subscriber->unsubscribe();
            $message = 'Suscriptor marcado como inactivo';
        } else {
            $subscriber->resubscribe();
            $message = 'Suscriptor reactivado';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Eliminar suscriptor
     */
    public function destroy(Subscriber $subscriber)
    {
        $this->checkAdminPermission();

        $email = $subscriber->email;
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')
            ->with('success', "Suscriptor {$email} eliminado correctamente");
    }

    /**
     * Eliminar múltiples suscriptores
     */
    public function bulkDelete(Request $request)
    {
        $this->checkAdminPermission();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:subscribers,id',
        ]);

        $count = Subscriber::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', "{$count} suscriptores eliminados");
    }
}
