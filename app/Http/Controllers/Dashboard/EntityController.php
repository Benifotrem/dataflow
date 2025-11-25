<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        $entities = Entity::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.entities.index', compact('entities'));
    }

    public function create()
    {
        $countries = collect(config('dataflow.supported_countries'))
            ->mapWithKeys(fn($data, $code) => [$code => $data['name']])
            ->toArray();
        return view('dashboard.entities.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:50',
            'country' => 'required|string|max:2',
            'fiscal_year_end' => 'nullable|string|max:5',
        ]);

        $countryData = config('dataflow.supported_countries')[$validated['country']] ?? null;

        if (!$countryData) {
            return back()->withErrors(['country' => 'País no soportado'])->withInput();
        }

        Entity::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'tax_id' => $validated['tax_id'],
            'country_code' => $validated['country'],
            'currency_code' => $countryData['currency'],
            'fiscal_config' => [
                'fiscal_year_end' => $validated['fiscal_year_end'] ?? '12-31',
                'vat_rate' => $countryData['vat_rate'],
            ],
        ]);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad fiscal creada exitosamente.');
    }

    public function show(Entity $entity)
    {
        if ($entity->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $documentsCount = $entity->documents()->count();
        $transactionsCount = $entity->transactions()->count();

        return view('dashboard.entities.show', compact('entity', 'documentsCount', 'transactionsCount'));
    }

    public function edit(Entity $entity)
    {
        if ($entity->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $countries = collect(config('dataflow.supported_countries'))
            ->mapWithKeys(fn($data, $code) => [$code => $data['name']])
            ->toArray();
        return view('dashboard.entities.edit', compact('entity', 'countries'));
    }

    public function update(Request $request, Entity $entity)
    {
        if ($entity->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:50',
            'country' => 'required|string|max:2',
            'fiscal_year_end' => 'nullable|string|max:5',
        ]);

        $countryData = config('dataflow.supported_countries')[$validated['country']] ?? null;

        if (!$countryData) {
            return back()->withErrors(['country' => 'País no soportado'])->withInput();
        }

        $fiscalConfig = $entity->fiscal_config ?? [];
        $fiscalConfig['fiscal_year_end'] = $validated['fiscal_year_end'] ?? '12-31';
        $fiscalConfig['vat_rate'] = $countryData['vat_rate'];

        $entity->update([
            'name' => $validated['name'],
            'tax_id' => $validated['tax_id'],
            'country_code' => $validated['country'],
            'currency_code' => $countryData['currency'],
            'fiscal_config' => $fiscalConfig,
        ]);

        return redirect()->route('entities.index')
            ->with('success', 'Entidad fiscal actualizada exitosamente.');
    }

    public function destroy(Entity $entity)
    {
        if ($entity->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Verificar que no tenga documentos o transacciones
        if ($entity->documents()->count() > 0 || $entity->transactions()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una entidad con documentos o transacciones asociados.']);
        }

        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidad fiscal eliminada exitosamente.');
    }
}
