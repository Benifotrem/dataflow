<?php

namespace App\Exports;

use App\Models\Document;
use App\Services\FiscalValidationService;
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
     * Calculate totals using ocr_data breakdown
     */
    protected function calculateTotals()
    {
        $this->totals = [
            'gravado_10' => 0,
            'iva_10' => 0,
            'gravado_5' => 0,
            'iva_5' => 0,
            'exentas' => 0,
            'total_iva' => 0,
            'total' => 0,
            'count' => 0,
        ];

        foreach ($this->documents as $document) {
            $ocrData = $document->ocr_data ?? [];

            $this->totals['gravado_10'] += $ocrData['subtotal_gravado_10'] ?? 0;
            $this->totals['iva_10'] += $ocrData['iva_10'] ?? 0;
            $this->totals['gravado_5'] += $ocrData['subtotal_gravado_5'] ?? 0;
            $this->totals['iva_5'] += $ocrData['iva_5'] ?? 0;
            $this->totals['exentas'] += $ocrData['subtotal_exentas'] ?? 0;
            $this->totals['total_iva'] += $ocrData['total_iva'] ?? 0;
            $this->totals['total'] += $ocrData['monto_total'] ?? 0;
            $this->totals['count']++;
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

        // Agregar totales generales
        $total = new \stdClass();
        $total->is_total = true;
        $total->total_label = 'TOTAL GENERAL';
        $total->total_count = $this->totals['count'];
        $total->total_gravado_10 = $this->totals['gravado_10'];
        $total->total_iva_10 = $this->totals['iva_10'];
        $total->total_gravado_5 = $this->totals['gravado_5'];
        $total->total_iva_5 = $this->totals['iva_5'];
        $total->total_exentas = $this->totals['exentas'];
        $total->total_total_iva = $this->totals['total_iva'];
        $total->total_amount = $this->totals['total'];
        $collection->push($total);

        return $collection;
    }

    /**
     * Define column headings según normativa paraguaya
     */
    public function headings(): array
    {
        return [
            'Fecha',
            'Tipo',
            'Nº Factura',
            'RUC Emisor',
            'Razón Social',
            'Descripción',
            'Base Gravada 10%',
            'IVA 10%',
            'Base Gravada 5%',
            'IVA 5%',
            'Exentas',
            'Total IVA',
            'Monto Total',
            'Moneda',
            'Validación',
            'Observaciones',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($document): array
    {
        // Fila de total general
        if (isset($document->is_total)) {
            return [
                '',
                '',
                '',
                '',
                '',
                $document->total_label,
                number_format($document->total_gravado_10, 0, ',', '.'),
                number_format($document->total_iva_10, 0, ',', '.'),
                number_format($document->total_gravado_5, 0, ',', '.'),
                number_format($document->total_iva_5, 0, ',', '.'),
                number_format($document->total_exentas, 0, ',', '.'),
                number_format($document->total_total_iva, 0, ',', '.'),
                number_format($document->total_amount, 0, ',', '.'),
                '',
                '',
                $document->total_count . ' facturas',
            ];
        }

        // Filas vacías
        if (!isset($document->id)) {
            return ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        }

        // Datos normales de factura desde ocr_data
        $ocrData = $document->ocr_data ?? [];

        // Validar coherencia matemática
        $fiscalValidation = app(FiscalValidationService::class);
        $mathValidation = $fiscalValidation->validateInvoiceAmounts([
            'total_amount' => $ocrData['monto_total'] ?? 0,
            'iva_10_base' => $ocrData['subtotal_gravado_10'] ?? 0,
            'iva_10' => $ocrData['iva_10'] ?? 0,
            'iva_5_base' => $ocrData['subtotal_gravado_5'] ?? 0,
            'iva_5' => $ocrData['iva_5'] ?? 0,
            'exentas' => $ocrData['subtotal_exentas'] ?? 0,
        ]);

        // Determinar estado de validación
        $validationStatus = '✅ OK';
        $observations = $document->validated ? 'Validado' : 'Revisar';

        if (!$mathValidation['valid']) {
            $validationStatus = '⚠️ REVISAR';
            $errorsSummary = implode('; ', array_map(function($error) {
                // Abreviar errores para Excel
                if (str_contains($error, 'IVA 10%')) return 'Error IVA 10%';
                if (str_contains($error, 'IVA 5%')) return 'Error IVA 5%';
                if (str_contains($error, 'Total incoherente')) return 'Total incoherente';
                return 'Error matemático';
            }, $mathValidation['errors']));
            $observations = $errorsSummary;
        }

        return [
            $document->document_date ? $document->document_date->format('d/m/Y') : '',
            $ocrData['tipo_factura'] ?? 'FACTURA',
            $ocrData['numero_factura'] ?? $document->invoice_number ?? '',
            $ocrData['ruc_emisor'] ?? '',
            $ocrData['razon_social_emisor'] ?? $document->issuer ?? '',
            $document->entity->name ?? '',
            isset($ocrData['subtotal_gravado_10']) ? number_format($ocrData['subtotal_gravado_10'], 0, ',', '.') : '',
            isset($ocrData['iva_10']) ? number_format($ocrData['iva_10'], 0, ',', '.') : '',
            isset($ocrData['subtotal_gravado_5']) ? number_format($ocrData['subtotal_gravado_5'], 0, ',', '.') : '',
            isset($ocrData['iva_5']) ? number_format($ocrData['iva_5'], 0, ',', '.') : '',
            isset($ocrData['subtotal_exentas']) ? number_format($ocrData['subtotal_exentas'], 0, ',', '.') : '',
            isset($ocrData['total_iva']) ? number_format($ocrData['total_iva'], 0, ',', '.') : '',
            isset($ocrData['monto_total']) ? number_format($ocrData['monto_total'], 0, ',', '.') : '',
            $ocrData['moneda'] ?? 'PYG',
            $validationStatus,
            $observations,
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
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9333EA']], // Purple
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
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
                $dataRows = $this->documents->count();

                // Aplicar bordes a toda la tabla de datos (hasta la columna P = 16)
                $sheet->getStyle("A1:P{$dataRows}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Aplicar bordes a la fila de total
                $sheet->getStyle("A{$lastRow}:P{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                // Congelar primera fila (encabezados)
                $sheet->freezePane('A2');

                // Ajustar ancho de columnas específicas
                $sheet->getColumnDimension('A')->setWidth(12); // Fecha
                $sheet->getColumnDimension('C')->setWidth(18); // Nº Factura
                $sheet->getColumnDimension('D')->setWidth(15); // RUC
                $sheet->getColumnDimension('E')->setWidth(30); // Razón Social
            },
        ];
    }
}
