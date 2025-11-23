<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TenantsController extends Controller
{
    /**
     * Lista de todos los tenants/clientes
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $query = Tenant::with(['users']);

        // Filtros
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'trial') {
                $query->where('trial_ends_at', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('trial_ends_at', '<=', now())
                      ->whereNull('subscription_ends_at');
            } elseif ($request->status === 'subscribed') {
                $query->whereNotNull('subscription_ends_at')
                      ->where('subscription_ends_at', '>', now());
            }
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Extender período de prueba
     */
    public function extendTrial(Request $request, Tenant $tenant)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403);
        }

        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $currentTrialEnd = $tenant->trial_ends_at ? Carbon::parse($tenant->trial_ends_at) : now();
        $newTrialEnd = $currentTrialEnd->addDays($request->days);

        $tenant->update([
            'trial_ends_at' => $newTrialEnd,
        ]);

        return back()->with('success', "Período de prueba extendido {$request->days} días hasta {$newTrialEnd->format('d/m/Y')}");
    }

    /**
     * Cancelar/suspender cuenta
     */
    public function suspend(Tenant $tenant)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403);
        }

        $tenant->update([
            'trial_ends_at' => now()->subDay(),
            'subscription_ends_at' => null,
        ]);

        return back()->with('success', 'Cuenta suspendida exitosamente');
    }

    /**
     * Reactivar cuenta
     */
    public function reactivate(Request $request, Tenant $tenant)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403);
        }

        $request->validate([
            'trial_days' => 'required|integer|min:1|max:365',
        ]);

        $tenant->update([
            'trial_ends_at' => now()->addDays($request->trial_days),
        ]);

        return back()->with('success', "Cuenta reactivada con {$request->trial_days} días de prueba");
    }

    /**
     * Ver detalles de un tenant
     */
    public function show(Tenant $tenant)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403);
        }

        $tenant->load(['users', 'documents', 'transactions']);

        $stats = [
            'users_count' => $tenant->users()->count(),
            'documents_count' => $tenant->documents()->count(),
            'transactions_count' => $tenant->transactions()->count(),
            'days_remaining' => $tenant->trial_ends_at ? now()->diffInDays($tenant->trial_ends_at, false) : 0,
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }
}
