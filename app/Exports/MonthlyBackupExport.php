<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class MonthlyBackupExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $tenantId;
    protected $year;
    protected $month;
    protected $documents;
    protected $totals;

    public function __construct(int $tenantId, int $year, int $month)
    {
        $this->tenantId = $tenantId;
        $this->year = $year;
        $this->month = $month;
        $this->documents = $this->getDocuments();
        $this->calculateTotals();
    }

    /**
     * Get all documents for the specified month
     */
    protected function getDocuments()
    {
        return Document::query()
            ->with(['entity', 'user'])
            ->where('tenant_id', $this->tenantId)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Calculate totals
     */
    protected function calculateTotals()
    {
        $this->totals = [
            'total_documents' => $this->documents->count(),
            'total_invoices' => $this->documents->where('is_invoice', true)->count(),
            'total_validated' => $this->documents->where('validated', true)->count(),
            'total_base' => $this->documents->sum('tax_base') ?? 0,
            'total_vat' => $this->documents->sum('tax_amount') ?? 0,
            'total_amount' => $this->documents->sum('total_with_tax') ?? 0,
        ];
    }

    /**
     * Return collection for export
     */
    public function collection()
    {
        $collection = $this->documents;

        // Agregar filas vacías como separador
        $collection->push(new \stdClass());
        $collection->push(new \stdClass());

        // Agregar resumen
        $summary = new \stdClass();
        $summary->is_summary = true;
        $summary->summary_label = 'RESUMEN DEL PERÍODO';
        $collection->push($summary);

        $totals = new \stdClass();
        $totals->is_totals = true;
        $totals->total_documents = $this->totals['total_documents'];
        $totals->total_invoices = $this->totals['total_invoices'];
        $totals->total_validated = $this->totals['total_validated'];
        $totals->total_base = $this->totals['total_base'];
        $totals->total_vat = $this->totals['total_vat'];
        $totals->total_amount = $this->totals['total_amount'];
        $collection->push($totals);

        return $collection;
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Fecha Documento',
            'Fecha Subida',
            'Entidad',
            'Usuario',
            'Tipo',
            'Es Factura',
            'Nº Factura',
            'Serie',
            'Emisor',
            'Receptor',
            'Base Imponible',
            'Tipo IVA (%)',
            'Importe IVA',
            'Total con IVA',
            'Moneda',
            'Estado OCR',
            'Validado',
            'Archivo Original',
            'Calidad',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($document): array
    {
        // Fila de resumen label
        if (isset($document->is_summary)) {
            return [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $document->summary_label,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        // Fila de totales
        if (isset($document->is_totals)) {
            return [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Total Documentos:',
                $document->total_documents,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        // Filas vacías
        if (!isset($document->id)) {
            return array_fill(0, 20, '');
        }

        // Datos normales de documento
        return [
            $document->id,
            $document->document_date ? $document->document_date->format('d/m/Y') : 'N/A',
            $document->created_at->format('d/m/Y H:i'),
            $document->entity->name ?? 'N/A',
            $document->user->name ?? 'N/A',
            $document->type ?? 'document',
            $document->is_invoice ? 'Sí' : 'No',
            $document->invoice_number ?? '',
            $document->invoice_series ?? '',
            $document->issuer ?? '',
            $document->recipient ?? '',
            $document->tax_base ? number_format($document->tax_base, 2, ',', '.') : '',
            $document->tax_rate ? number_format($document->tax_rate, 2, ',', '.') : '',
            $document->tax_amount ? number_format($document->tax_amount, 2, ',', '.') : '',
            $document->total_with_tax ? number_format($document->total_with_tax, 2, ',', '.') : '',
            $document->currency ?? '',
            ucfirst($document->ocr_status),
            $document->validated ? 'Sí' : 'No',
            $document->original_filename,
            $document->quality_status ? ucfirst($document->quality_status) : '',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            // Encabezados
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']], // Indigo
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
            // Fila de resumen
            ($lastRow - 1) => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']], // Light blue
            ],
            // Fila de totales
            $lastRow => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']], // Light blue
            ],
        ];
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $dataRows = $this->documents->count();

                // Aplicar bordes a toda la tabla de datos
                if ($dataRows > 0) {
                    $sheet->getStyle("A1:T" . ($dataRows + 1))
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Aplicar bordes a las filas de resumen
                $sheet->getStyle("A{$lastRow}:T{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                // Congelar primera fila (encabezados)
                $sheet->freezePane('A2');

                // Ajustar altura de la primera fila
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Wrap text en encabezados
                $sheet->getStyle('A1:T1')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
