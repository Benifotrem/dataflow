<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class DocumentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Query documents with filters
     */
    public function query()
    {
        $query = Document::query()
            ->with(['entity', 'user'])
            ->where('tenant_id', Auth::user()->tenant_id);

        // Apply filters
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (!empty($this->filters['entity_id'])) {
            $query->where('entity_id', $this->filters['entity_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('document_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('document_date', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['validated'])) {
            $query->where('validated', $this->filters['validated']);
        }

        if (!empty($this->filters['ocr_status'])) {
            $query->where('ocr_status', $this->filters['ocr_status']);
        }

        return $query->orderBy('document_date', 'desc');
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Fecha del Documento',
            'Tipo',
            'Emisor',
            'Receptor',
            'Importe',
            'Moneda',
            'Entidad Contable',
            'Usuario',
            'Estado OCR',
            'Validado',
            'Fecha de Subida',
            'Archivo Original',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($document): array
    {
        return [
            $document->id,
            $document->document_date ? $document->document_date->format('d/m/Y') : 'N/A',
            $this->getTypeLabel($document->type),
            $document->issuer ?? 'N/A',
            $document->recipient ?? 'N/A',
            $document->amount ? number_format($document->amount, 2, ',', '.') : 'N/A',
            $document->currency ?? 'N/A',
            $document->entity->name ?? 'N/A',
            $document->user->name ?? 'N/A',
            $this->getStatusLabel($document->ocr_status),
            $document->validated ? 'Sí' : 'No',
            $document->created_at->format('d/m/Y H:i'),
            $document->original_filename,
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    /**
     * Get human-readable type label
     */
    protected function getTypeLabel(string $type): string
    {
        $types = [
            'invoice' => 'Factura',
            'receipt' => 'Recibo',
            'bank_statement' => 'Extracto Bancario',
            'tax_form' => 'Formulario Fiscal',
            'other' => 'Otro',
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Get human-readable OCR status label
     */
    protected function getStatusLabel(string $status): string
    {
        $statuses = [
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'failed' => 'Fallido',
        ];

        return $statuses[$status] ?? $status;
    }
}
