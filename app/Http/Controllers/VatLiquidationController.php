<?php

namespace App\Http\Controllers;

use App\Exports\VatLiquidationExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VatLiquidationController extends Controller
{
    /**
     * Export VAT liquidation report to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'current_month' => 'nullable|in:true,false',
        ]);

        // Generar nombre de archivo descriptivo
        $filename = 'liquidacion_iva_';

        if (!empty($filters['current_month']) && $filters['current_month'] === 'true') {
            $filename .= now()->format('Y-m');
        } elseif (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $filename .= $filters['date_from'] . '_a_' . $filters['date_to'];
        } else {
            $filename .= now()->format('Y-m-d');
        }

        $filename .= '.xlsx';

        return Excel::download(
            new VatLiquidationExport($filters),
            $filename
        );
    }

    /**
     * Show VAT liquidation page
     */
    public function index()
    {
        $entities = auth()->user()->tenant->entities;

        return view('dashboard.vat-liquidation.index', compact('entities'));
    }
}
