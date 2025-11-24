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
        $countries = collect(config('contaplus.supported_countries'))
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

        $validated['tenant_id'] = auth()->user()->tenant_id;

        Entity::create($validated);

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

        $countries = collect(config('contaplus.supported_countries'))
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

        $entity->update($validated);

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
