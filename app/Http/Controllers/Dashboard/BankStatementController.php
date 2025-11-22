<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankStatementController extends Controller
{
    public function index(Request $request)
    {
        $bankStatements = BankStatement::where('tenant_id', $request->user()->tenant_id)
            ->with('entity')
            ->orderBy('period_start', 'desc')
            ->paginate(20);

        return view('dashboard.bank-statements.index', compact('bankStatements'));
    }

    public function create()
    {
        $entities = Entity::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('dashboard.bank-statements.create', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'file' => 'required|file|max:10240|mimes:pdf,xlsx,xls,csv',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('bank_statements', 'local');

            BankStatement::create([
                'tenant_id' => auth()->user()->tenant_id,
                'entity_id' => $request->entity_id,
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
            ]);

            return redirect()->route('bank-statements.index')
                ->with('success', 'Extracto bancario cargado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar extracto: ' . $e->getMessage()]);
        }
    }

    public function destroy(BankStatement $bankStatement)
    {
        if ($bankStatement->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($bankStatement->file_path) {
            Storage::disk('local')->delete($bankStatement->file_path);
        }

        $bankStatement->delete();

        return redirect()->route('bank-statements.index')
            ->with('success', 'Extracto bancario eliminado exitosamente.');
    }
}
