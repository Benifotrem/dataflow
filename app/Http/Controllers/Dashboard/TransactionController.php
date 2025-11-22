<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::where('tenant_id', $request->user()->tenant_id)
            ->with('entity')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('dashboard.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $entities = Entity::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('dashboard.transactions.create', compact('entities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transacción creada exitosamente.');
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        return view('dashboard.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $entities = Entity::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('dashboard.transactions.edit', compact('transaction', 'entities'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transacción actualizada exitosamente.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transacción eliminada exitosamente.');
    }
}
