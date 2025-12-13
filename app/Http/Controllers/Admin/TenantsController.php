<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        try {
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

            Log::info('Período de prueba extendido', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'days' => $request->days,
                'new_trial_end' => $newTrialEnd->format('Y-m-d'),
            ]);

            return back()->with('success', "Período de prueba extendido {$request->days} días hasta {$newTrialEnd->format('d/m/Y')}");
        } catch (\Exception $e) {
            Log::error('Error al extender período de prueba', [
                'tenant_id' => $tenant->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Error al extender período de prueba: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancelar/suspender cuenta
     */
    public function suspend(Tenant $tenant)
    {
        try {
            // Verificar permisos
            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
                abort(403);
            }

            $tenant->update([
                'trial_ends_at' => now()->subDay(),
                'subscription_ends_at' => null,
            ]);

            Log::info('Cuenta suspendida', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
            ]);

            return back()->with('success', 'Cuenta suspendida exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al suspender cuenta', [
                'tenant_id' => $tenant->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Error al suspender cuenta: ' . $e->getMessage()]);
        }
    }

    /**
     * Reactivar cuenta
     */
    public function reactivate(Request $request, Tenant $tenant)
    {
        try {
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

            Log::info('Cuenta reactivada', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'trial_days' => $request->trial_days,
            ]);

            return back()->with('success', "Cuenta reactivada con {$request->trial_days} días de prueba");
        } catch (\Exception $e) {
            Log::error('Error al reactivar cuenta', [
                'tenant_id' => $tenant->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Error al reactivar cuenta: ' . $e->getMessage()]);
        }
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
