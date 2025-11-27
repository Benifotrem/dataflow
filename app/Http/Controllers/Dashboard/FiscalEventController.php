<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\FiscalEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiscalEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FiscalEvent::where('tenant_id', Auth::user()->tenant_id)
            ->with('tenant');

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('event_type', $request->type);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('event_date', '>=', now());
            } elseif ($request->status === 'past') {
                $query->where('event_date', '<', now());
            }
        }

        // Filtro por activo/inactivo
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === '1');
        }

        // Ordenar por fecha
        $events = $query->orderBy('event_date', 'asc')->paginate(20);

        // Eventos próximos (próximos 30 días)
        $upcomingEvents = FiscalEvent::where('tenant_id', Auth::user()->tenant_id)
            ->active()
            ->upcoming(30)
            ->get();

        return view('dashboard.fiscal-events.index', compact('events', 'upcomingEvents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenant = Auth::user()->tenant;
        $eventTypes = $this->getEventTypes();

        return view('dashboard.fiscal-events.create', compact('tenant', 'eventTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:vat_liquidation,income_tax,tax_declaration,social_security,annual_accounts,quarterly_declaration,monthly_declaration,custom',
            'event_date' => 'required|date|after_or_equal:today',
            'notification_days_before' => 'required|integer|min:1|max:90',
            'is_recurring' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['country_code'] = Auth::user()->tenant->country_code ?? 'PY';
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_default'] = false; // Eventos creados manualmente no son default

        FiscalEvent::create($validated);

        return redirect()->route('fiscal-events.index')
            ->with('success', 'Evento fiscal creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FiscalEvent $fiscalEvent)
    {
        // Verificar que el evento pertenece al tenant del usuario
        if ($fiscalEvent->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return view('dashboard.fiscal-events.show', compact('fiscalEvent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FiscalEvent $fiscalEvent)
    {
        // Verificar que el evento pertenece al tenant del usuario
        if ($fiscalEvent->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $eventTypes = $this->getEventTypes();

        return view('dashboard.fiscal-events.edit', compact('fiscalEvent', 'eventTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FiscalEvent $fiscalEvent)
    {
        // Verificar que el evento pertenece al tenant del usuario
        if ($fiscalEvent->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:vat_liquidation,income_tax,tax_declaration,social_security,annual_accounts,quarterly_declaration,monthly_declaration,custom',
            'event_date' => 'required|date',
            'notification_days_before' => 'required|integer|min:1|max:90',
            'is_recurring' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $fiscalEvent->update($validated);

        return redirect()->route('fiscal-events.index')
            ->with('success', 'Evento fiscal actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FiscalEvent $fiscalEvent)
    {
        // Verificar que el evento pertenece al tenant del usuario
        if ($fiscalEvent->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        // No permitir eliminar eventos por defecto, solo desactivarlos
        if ($fiscalEvent->is_default) {
            $fiscalEvent->update(['is_active' => false]);
            return redirect()->route('fiscal-events.index')
                ->with('warning', 'Los eventos por defecto no se pueden eliminar, solo desactivar. Evento desactivado.');
        }

        $fiscalEvent->delete();

        return redirect()->route('fiscal-events.index')
            ->with('success', 'Evento fiscal eliminado exitosamente.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(FiscalEvent $fiscalEvent)
    {
        // Verificar que el evento pertenece al tenant del usuario
        if ($fiscalEvent->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $fiscalEvent->update(['is_active' => !$fiscalEvent->is_active]);

        $status = $fiscalEvent->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Evento {$status} exitosamente.");
    }

    /**
     * Get event types for dropdown
     */
    protected function getEventTypes(): array
    {
        return [
            'vat_liquidation' => 'Liquidación de IVA',
            'income_tax' => 'Impuesto a la Renta',
            'tax_declaration' => 'Declaración de Impuestos',
            'social_security' => 'Seguridad Social',
            'annual_accounts' => 'Cuentas Anuales',
            'quarterly_declaration' => 'Declaración Trimestral',
            'monthly_declaration' => 'Declaración Mensual',
            'custom' => 'Evento Personalizado',
        ];
    }
}
