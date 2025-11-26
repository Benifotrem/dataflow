<?php

namespace App\Http\Controllers;

use App\Exports\DocumentsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocumentExportController extends Controller
{
    /**
     * Export documents to Excel
     */
    public function export(Request $request)
    {
        $filters = $request->validate([
            'type' => 'nullable|in:invoice,receipt,bank_statement,tax_form,other',
            'entity_id' => 'nullable|exists:entities,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'validated' => 'nullable|boolean',
            'ocr_status' => 'nullable|in:pending,processing,completed,failed',
        ]);

        $filename = 'documentos_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new DocumentsExport($filters),
            $filename
        );
    }
}
