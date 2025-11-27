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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class VatLiquidationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $documents;
    protected $totals;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->documents = $this->getDocuments();
        $this->calculateTotals();
    }

    /**
     * Get documents with filters
     */
    protected function getDocuments()
    {
        $query = Document::query()
            ->with(['entity', 'user'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('is_invoice', true) // Solo facturas válidas
            ->where('ocr_status', 'completed'); // Solo procesadas exitosamente

        // Filtro de entidad
        if (!empty($this->filters['entity_id'])) {
            $query->where('entity_id', $this->filters['entity_id']);
        }

        // Filtros de fecha
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('document_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('document_date', '<=', $this->filters['date_to']);
        }

        // Filtro especial para mes actual
        if (!empty($this->filters['current_month']) && $this->filters['current_month'] === 'true') {
            $query->whereYear('document_date', now()->year)
                  ->whereMonth('document_date', now()->month);
        }

        return $query->orderBy('document_date', 'asc')->get();
    }

    /**
     * Calculate totals by tax rate
     */
    protected function calculateTotals()
    {
        $this->totals = [
            'total_base' => 0,
            'total_vat' => 0,
            'total_amount' => 0,
            'by_rate' => [],
        ];

        foreach ($this->documents as $document) {
            $this->totals['total_base'] += $document->tax_base ?? 0;
            $this->totals['total_vat'] += $document->tax_amount ?? 0;
            $this->totals['total_amount'] += $document->total_with_tax ?? 0;

            // Agrupar por tasa de IVA
            $rate = $document->tax_rate ?? 0;
            if (!isset($this->totals['by_rate'][$rate])) {
                $this->totals['by_rate'][$rate] = [
                    'base' => 0,
                    'vat' => 0,
                    'total' => 0,
                    'count' => 0,
                ];
            }

            $this->totals['by_rate'][$rate]['base'] += $document->tax_base ?? 0;
            $this->totals['by_rate'][$rate]['vat'] += $document->tax_amount ?? 0;
            $this->totals['by_rate'][$rate]['total'] += $document->total_with_tax ?? 0;
            $this->totals['by_rate'][$rate]['count']++;
        }
    }

    /**
     * Return collection for export
     */
    public function collection()
    {
        // Agregar filas de totales al final
        $collection = $this->documents;

        // Agregar filas vacías como separador
        $collection->push(new \stdClass());
        $collection->push(new \stdClass());

        // Agregar resumen por tasa de IVA
        foreach ($this->totals['by_rate'] as $rate => $data) {
            $summary = new \stdClass();
            $summary->is_summary = true;
            $summary->summary_label = "SUBTOTAL IVA {$rate}%";
            $summary->summary_count = $data['count'];
            $summary->summary_base = $data['base'];
            $summary->summary_vat = $data['vat'];
            $summary->summary_total = $data['total'];
            $collection->push($summary);
        }

        // Agregar fila vacía
        $collection->push(new \stdClass());

        // Agregar totales generales
        $total = new \stdClass();
        $total->is_total = true;
        $total->total_label = 'TOTAL GENERAL';
        $total->total_base = $this->totals['total_base'];
        $total->total_vat = $this->totals['total_vat'];
        $total->total_amount = $this->totals['total_amount'];
        $collection->push($total);

        return $collection;
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Nº Factura',
            'Serie',
            'Emisor',
            'Receptor',
            'Entidad',
            'Base Imponible',
            'Tipo IVA (%)',
            'Importe IVA',
            'Total con IVA',
            'Moneda',
            'Estado',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($document): array
    {
        // Filas de resumen por tasa
        if (isset($document->is_summary)) {
            return [
                '',
                '',
                '',
                '',
                '',
                '',
                $document->summary_label,
                number_format($document->summary_base, 2, ',', '.'),
                '',
                number_format($document->summary_vat, 2, ',', '.'),
                number_format($document->summary_total, 2, ',', '.'),
                $document->summary_count . ' facturas',
                '',
            ];
        }

        // Fila de total general
        if (isset($document->is_total)) {
            return [
                '',
                '',
                '',
                '',
                '',
                '',
                $document->total_label,
                number_format($document->total_base, 2, ',', '.'),
                '',
                number_format($document->total_vat, 2, ',', '.'),
                number_format($document->total_amount, 2, ',', '.'),
                '',
                '',
            ];
        }

        // Filas vacías
        if (!isset($document->id)) {
            return ['', '', '', '', '', '', '', '', '', '', '', '', ''];
        }

        // Datos normales de factura
        return [
            $document->id,
            $document->document_date ? $document->document_date->format('d/m/Y') : 'N/A',
            $document->invoice_number ?? 'N/A',
            $document->invoice_series ?? '',
            $document->issuer ?? 'N/A',
            $document->recipient ?? 'N/A',
            $document->entity->name ?? 'N/A',
            $document->tax_base ? number_format($document->tax_base, 2, ',', '.') : '0,00',
            $document->tax_rate ? number_format($document->tax_rate, 2, ',', '.') : '0,00',
            $document->tax_amount ? number_format($document->tax_amount, 2, ',', '.') : '0,00',
            $document->total_with_tax ? number_format($document->total_with_tax, 2, ',', '.') : '0,00',
            $document->currency ?? 'EUR',
            $document->validated ? 'Validado' : 'Pendiente',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $dataRows = $this->documents->count() - count($this->totals['by_rate']) - 3; // Rows without summaries

        return [
            // Encabezados
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9333EA']], // Purple
                'alignment' => ['horizontal' => 'center'],
            ],
            // Filas de resumen
            ($lastRow - count($this->totals['by_rate']) - 1) . ':' . ($lastRow - 2) => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']], // Gray
            ],
            // Fila de total general
            $lastRow => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']], // Green
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

                // Aplicar bordes a toda la tabla de datos
                $dataRows = $this->documents->count() - count($this->totals['by_rate']) - 3;

                $sheet->getStyle("A1:M{$dataRows}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Aplicar bordes a las filas de resumen
                $summaryStart = $lastRow - count($this->totals['by_rate']) - 1;
                $summaryEnd = $lastRow - 2;

                if ($summaryStart <= $summaryEnd) {
                    $sheet->getStyle("A{$summaryStart}:M{$summaryEnd}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Aplicar bordes a la fila de total
                $sheet->getStyle("A{$lastRow}:M{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                // Congelar primera fila (encabezados)
                $sheet->freezePane('A2');
            },
        ];
    }
}
