<?php

namespace App\Http\Controllers;

use App\Jobs\OcrInvoiceProcessingJob;
use App\Models\Document;
use App\Models\Entity;
use App\Services\DnitConnector;
use App\Services\FiscalValidationService;
use App\Exports\VatLiquidationExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class MiniAppController extends Controller
{
    protected $dnitConnector;

    public function __construct(DnitConnector $dnitConnector)
    {
        $this->dnitConnector = $dnitConnector;
    }

    /**
     * Dashboard principal con métricas
     * GET /api/miniapp/dashboard?entity_id={id}
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        // Obtener entity_id si se proporciona
        $entityId = $request->input('entity_id');

        // Validar que la entidad pertenezca al tenant si se especifica
        if ($entityId) {
            $entity = Entity::where('id', $entityId)
                ->where('tenant_id', $tenant->id)
                ->first();

            if (!$entity) {
                return response()->json([
                    'success' => false,
                    'error' => 'Entidad no encontrada o no pertenece a su cuenta'
                ], 404);
            }
        }

        // Período actual
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        // Métricas del mes actual
        $currentMonthStats = $this->getMonthStats($tenant->id, $currentMonth, $currentMonthEnd, $entityId);

        // Métricas del mes anterior para comparación
        $previousMonth = now()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->subMonth()->endOfMonth();
        $previousMonthStats = $this->getMonthStats($tenant->id, $previousMonth, $previousMonthEnd, $entityId);

        // Evolución diaria (últimos 30 días)
        $dailyEvolution = $this->getDailyEvolution($tenant->id, $entityId);

        // Top proveedores
        $topSuppliers = $this->getTopSuppliers($tenant->id, $currentMonth, $currentMonthEnd, 10, $entityId);

        // Distribución por tipo de IVA
        $ivaDistribution = $this->getIvaDistribution($tenant->id, $currentMonth, $currentMonthEnd, $entityId);

        return response()->json([
            'success' => true,
            'data' => [
                'current_month' => [
                    'name' => $currentMonth->translatedFormat('F Y'),
                    'total_invoices' => $currentMonthStats['total'],
                    'validated_set' => $currentMonthStats['validated'],
                    'pending_validation' => $currentMonthStats['pending'],
                    'rejected' => $currentMonthStats['rejected'],
                    'total_iva_credito' => $currentMonthStats['total_iva'],
                    'breakdown' => [
                        'iva_10' => [
                            'base' => $currentMonthStats['base_10'],
                            'iva' => $currentMonthStats['iva_10'],
                        ],
                        'iva_5' => [
                            'base' => $currentMonthStats['base_5'],
                            'iva' => $currentMonthStats['iva_5'],
                        ],
                        'exentas' => $currentMonthStats['exentas'],
                    ],
                    'total_amount' => $currentMonthStats['total_amount'],
                ],
                'previous_month' => [
                    'name' => $previousMonth->translatedFormat('F Y'),
                    'total_invoices' => $previousMonthStats['total'],
                    'total_iva' => $previousMonthStats['total_iva'],
                ],
                'comparison' => [
                    'invoices_diff' => $currentMonthStats['total'] - $previousMonthStats['total'],
                    'invoices_percent' => $previousMonthStats['total'] > 0
                        ? round((($currentMonthStats['total'] - $previousMonthStats['total']) / $previousMonthStats['total']) * 100, 1)
                        : 0,
                    'iva_diff' => $currentMonthStats['total_iva'] - $previousMonthStats['total_iva'],
                    'iva_percent' => $previousMonthStats['total_iva'] > 0
                        ? round((($currentMonthStats['total_iva'] - $previousMonthStats['total_iva']) / $previousMonthStats['total_iva']) * 100, 1)
                        : 0,
                ],
                'charts' => [
                    'daily_evolution' => $dailyEvolution,
                    'top_suppliers' => $topSuppliers,
                    'iva_distribution' => $ivaDistribution,
                ],
                'alerts' => $this->getAlerts($tenant->id, $currentMonthStats),
            ],
        ]);
    }

    /**
     * Listar documentos/facturas
     * GET /api/miniapp/documents
     */
    public function listDocuments(Request $request)
    {
        $user = auth()->user();
        $tenant = $user->tenant;

        $query = Document::where('tenant_id', $tenant->id)
            ->with('entity')
            ->orderBy('document_date', 'desc');

        // Filtros
        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->filled('status')) {
            $query->where('validated', $request->status === 'validated');
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('document_date', $date->year)
                  ->whereMonth('document_date', $date->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('issuer', 'like', "%{$search}%")
                  ->orWhereHas('entity', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $documents->map(function($doc) {
                $ocrData = $doc->ocr_data ?? [];
                return [
                    'id' => $doc->id,
                    'invoice_number' => $doc->invoice_number ?? $ocrData['numero_factura'] ?? 'N/A',
                    'issuer' => $doc->issuer ?? $ocrData['razon_social_emisor'] ?? 'N/A',
                    'ruc_emisor' => $ocrData['ruc_emisor'] ?? null,
                    'document_date' => $doc->document_date?->format('d/m/Y'),
                    'total_amount' => $ocrData['monto_total'] ?? $doc->amount ?? 0,
                    'total_iva' => $ocrData['total_iva'] ?? 0,
                    'validated' => $doc->validated,
                    'entity' => [
                        'id' => $doc->entity->id,
                        'name' => $doc->entity->name,
                    ],
                    'thumbnail_url' => $doc->file_path ? asset('storage/' . $doc->file_path) : null,
                    'created_at' => $doc->created_at?->format('d/m/Y H:i'),
                ];
            }),
            'pagination' => [
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    /**
     * Obtener detalles de un documento
     * GET /api/miniapp/documents/{id}
     */
    public function getDocument($id)
    {
        $user = auth()->user();
        $document = Document::where('tenant_id', $user->tenant->id)
            ->with('entity')
            ->findOrFail($id);

        $ocrData = $document->ocr_data ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $document->id,
                'invoice_number' => $document->invoice_number ?? $ocrData['numero_factura'] ?? null,
                'issuer' => $document->issuer ?? $ocrData['razon_social_emisor'] ?? null,
                'ruc_emisor' => $ocrData['ruc_emisor'] ?? null,
                'timbrado' => $ocrData['timbrado'] ?? null,
                'document_date' => $document->document_date?->format('Y-m-d'),
                'tipo_factura' => $ocrData['tipo_factura'] ?? 'FACTURA',
                'moneda' => $ocrData['moneda'] ?? 'PYG',
                'subtotal_gravado_10' => $ocrData['subtotal_gravado_10'] ?? 0,
                'iva_10' => $ocrData['iva_10'] ?? 0,
                'subtotal_gravado_5' => $ocrData['subtotal_gravado_5'] ?? 0,
                'iva_5' => $ocrData['iva_5'] ?? 0,
                'subtotal_exentas' => $ocrData['subtotal_exentas'] ?? 0,
                'monto_total' => $ocrData['monto_total'] ?? $document->amount ?? 0,
                'total_iva' => $ocrData['total_iva'] ?? 0,
                'validated' => $document->validated,
                'entity' => [
                    'id' => $document->entity->id,
                    'name' => $document->entity->name,
                ],
                'file_url' => $document->file_path ? asset('storage/' . $document->file_path) : null,
                'created_at' => $document->created_at?->format('d/m/Y H:i'),
                'updated_at' => $document->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Actualizar documento
     * PATCH /api/miniapp/documents/{id}
     */
    public function updateDocument(Request $request, $id)
    {
        $user = auth()->user();
        $document = Document::where('tenant_id', $user->tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'invoice_number' => 'nullable|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'ruc_emisor' => 'nullable|string|max:20',
            'timbrado' => 'nullable|string|max:20',
            'document_date' => 'nullable|date',
            'subtotal_gravado_10' => 'nullable|numeric|min:0',
            'subtotal_gravado_5' => 'nullable|numeric|min:0',
            'subtotal_exentas' => 'nullable|numeric|min:0',
        ]);

        // Actualizar OCR data
        $ocrData = $document->ocr_data ?? [];

        if ($request->filled('invoice_number')) {
            $ocrData['numero_factura'] = $request->invoice_number;
            $document->invoice_number = $request->invoice_number;
        }

        if ($request->filled('issuer')) {
            $ocrData['razon_social_emisor'] = $request->issuer;
            $document->issuer = $request->issuer;
        }

        if ($request->filled('ruc_emisor')) {
            $ocrData['ruc_emisor'] = $request->ruc_emisor;
        }

        if ($request->filled('timbrado')) {
            $ocrData['timbrado'] = $request->timbrado;
        }

        if ($request->filled('document_date')) {
            $document->document_date = $request->document_date;
        }

        // Recalcular IVAs automáticamente
        if ($request->filled('subtotal_gravado_10')) {
            $ocrData['subtotal_gravado_10'] = floatval($request->subtotal_gravado_10);
            $ocrData['iva_10'] = round($ocrData['subtotal_gravado_10'] * 0.1, 0);
        }

        if ($request->filled('subtotal_gravado_5')) {
            $ocrData['subtotal_gravado_5'] = floatval($request->subtotal_gravado_5);
            $ocrData['iva_5'] = round($ocrData['subtotal_gravado_5'] * 0.05, 0);
        }

        if ($request->filled('subtotal_exentas')) {
            $ocrData['subtotal_exentas'] = floatval($request->subtotal_exentas);
        }

        // Recalcular totales
        $totalIva = ($ocrData['iva_10'] ?? 0) + ($ocrData['iva_5'] ?? 0);
        $totalAmount = ($ocrData['subtotal_gravado_10'] ?? 0) +
                      ($ocrData['iva_10'] ?? 0) +
                      ($ocrData['subtotal_gravado_5'] ?? 0) +
                      ($ocrData['iva_5'] ?? 0) +
                      ($ocrData['subtotal_exentas'] ?? 0);

        $ocrData['total_iva'] = $totalIva;
        $ocrData['monto_total'] = $totalAmount;

        // Validar coherencia matemática después de la actualización
        $fiscalValidation = app(FiscalValidationService::class);
        $mathValidation = $fiscalValidation->validateInvoiceAmounts([
            'total_amount' => $totalAmount,
            'iva_10_base' => $ocrData['subtotal_gravado_10'] ?? 0,
            'iva_10' => $ocrData['iva_10'] ?? 0,
            'iva_5_base' => $ocrData['subtotal_gravado_5'] ?? 0,
            'iva_5' => $ocrData['iva_5'] ?? 0,
            'exentas' => $ocrData['subtotal_exentas'] ?? 0,
        ]);

        // Si hay errores matemáticos, marcar documento para revisión
        if (!$mathValidation['valid']) {
            $validationSummary = $fiscalValidation->getValidationSummary($mathValidation);
            $document->rejection_reason = $validationSummary;
            $document->validated = false;

            Log::warning('Document has mathematical errors after manual update', [
                'document_id' => $document->id,
                'errors' => $mathValidation['errors'],
            ]);
        } else {
            // Si la validación es correcta, limpiar rejection_reason si existía
            if ($document->rejection_reason && str_contains($document->rejection_reason, 'incoherente')) {
                $document->rejection_reason = null;
            }
        }

        $document->ocr_data = $ocrData;
        $document->amount = $totalAmount;
        $document->save();

        Log::info('Document updated from Mini App', [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'changes' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento actualizado exitosamente',
            'data' => [
                'id' => $document->id,
                'total_iva' => $totalIva,
                'monto_total' => $totalAmount,
            ],
        ]);
    }

    /**
     * Consultar factura electrónica por CDC
     * POST /api/miniapp/cdc/consult
     */
    public function consultCDC(Request $request)
    {
        $validated = $request->validate([
            'cdc' => 'required|string|size:44',
            'entity_id' => 'required|exists:entities,id',
        ]);

        $user = auth()->user();

        // Verificar que la entidad pertenezca al tenant
        $entity = Entity::where('id', $validated['entity_id'])
            ->where('tenant_id', $user->tenant->id)
            ->firstOrFail();

        // Consultar CDC en la SET
        $result = $this->dnitConnector->consultarCDC($validated['cdc']);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);
        }

        $cdcData = $result['data'];

        // Validar coherencia matemática de los datos recibidos
        $fiscalValidation = app(FiscalValidationService::class);
        $mathValidation = $fiscalValidation->validateInvoiceAmounts([
            'total_amount' => $cdcData['monto_total'] ?? 0,
            'iva_10_base' => $cdcData['subtotal_gravado_10'] ?? 0,
            'iva_10' => $cdcData['iva_10'] ?? 0,
            'iva_5_base' => $cdcData['subtotal_gravado_5'] ?? 0,
            'iva_5' => $cdcData['iva_5'] ?? 0,
            'exentas' => $cdcData['subtotal_exentas'] ?? 0,
        ]);

        // Agregar resultado de validación matemática a la respuesta
        $cdcData['math_validation'] = [
            'valid' => $mathValidation['valid'],
            'errors' => $mathValidation['errors'],
            'warnings' => $mathValidation['warnings'],
        ];

        // Si hay errores matemáticos, intentar auto-corrección
        if (!$mathValidation['valid']) {
            $correctedData = $fiscalValidation->autoCorrectAmounts([
                'total_amount' => $cdcData['monto_total'] ?? 0,
                'iva_10_base' => $cdcData['subtotal_gravado_10'] ?? 0,
                'iva_10' => $cdcData['iva_10'] ?? 0,
                'iva_5_base' => $cdcData['subtotal_gravado_5'] ?? 0,
                'iva_5' => $cdcData['iva_5'] ?? 0,
                'exentas' => $cdcData['subtotal_exentas'] ?? 0,
            ]);

            if ($correctedData['corrected']) {
                // Actualizar con datos corregidos
                $cdcData['subtotal_gravado_10'] = $correctedData['iva_10_base'];
                $cdcData['iva_10'] = $correctedData['iva_10'];
                $cdcData['subtotal_gravado_5'] = $correctedData['iva_5_base'];
                $cdcData['iva_5'] = $correctedData['iva_5'];
                $cdcData['subtotal_exentas'] = $correctedData['exentas'];
                $cdcData['total_iva'] = $fiscalValidation->calculateTotalIvaCredito($correctedData);

                $cdcData['math_validation']['auto_corrected'] = true;
            }
        }

        // Guardar automáticamente si el usuario lo solicita
        if ($request->boolean('auto_save', false)) {
            $document = new Document();
            $document->tenant_id = $user->tenant->id;
            $document->entity_id = $entity->id;
            $document->invoice_number = $cdcData['numero_factura'];
            $document->issuer = $cdcData['razon_social_emisor'];
            $document->document_date = $cdcData['fecha_emision'];
            $document->amount = $cdcData['monto_total'];
            $document->validated = true; // Ya viene validado de la SET
            $document->ocr_data = $cdcData;
            $document->save();

            Log::info('Electronic invoice imported from CDC', [
                'cdc' => $validated['cdc'],
                'document_id' => $document->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura electrónica guardada exitosamente',
                'data' => [
                    'saved' => true,
                    'document_id' => $document->id,
                    'invoice_data' => $cdcData,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'saved' => false,
                'invoice_data' => $cdcData,
            ],
        ]);
    }

    /**
     * Exportar liquidación de IVA
     * POST /api/miniapp/export/vat-liquidation
     */
    public function exportVatLiquidation(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'nullable|exists:entities,id',
            'date_mode' => 'required|in:current_month,custom',
            'date_from' => 'required_if:date_mode,custom|date',
            'date_to' => 'required_if:date_mode,custom|date',
        ]);

        $user = auth()->user();

        if ($validated['date_mode'] === 'current_month') {
            $dateFrom = now()->startOfMonth();
            $dateTo = now()->endOfMonth();
        } else {
            $dateFrom = Carbon::parse($validated['date_from']);
            $dateTo = Carbon::parse($validated['date_to']);
        }

        $entityId = $validated['entity_id'] ?? null;

        // Generar Excel
        $fileName = 'liquidacion_iva_' . $dateFrom->format('Y-m') . '.xlsx';
        $export = new VatLiquidationExport($user->tenant->id, $entityId, $dateFrom, $dateTo);

        $filePath = storage_path('app/temp/' . $fileName);
        Excel::store($export, 'temp/' . $fileName);

        // Generar URL temporal (válida por 1 hora)
        $url = route('miniapp.download-temp-file', ['file' => $fileName]);

        return response()->json([
            'success' => true,
            'message' => 'Excel generado exitosamente',
            'data' => [
                'file_name' => $fileName,
                'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
                'download_url' => $url,
                'expires_in' => 3600, // 1 hora
            ],
        ]);
    }

    /**
     * Listar entidades fiscales del usuario
     * GET /api/miniapp/entities
     */
    public function listEntities()
    {
        $user = auth()->user();
        $entities = Entity::where('tenant_id', $user->tenant->id)->get();

        return response()->json([
            'success' => true,
            'data' => $entities->map(function($entity) {
                return [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'tax_id' => $entity->tax_id,
                    'country' => $entity->country_code,
                ];
            }),
        ]);
    }

    // ========== MÉTODOS PRIVADOS ==========

    private function getMonthStats($tenantId, $from, $to, $entityId = null)
    {
        $query = Document::where('tenant_id', $tenantId)
            ->whereBetween('document_date', [$from, $to]);

        // Filtrar por entidad si se especifica
        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        $documents = $query->get();

        $stats = [
            'total' => $documents->count(),
            'validated' => $documents->where('validated', true)->count(),
            'pending' => $documents->where('validated', false)->count(),
            'rejected' => 0, // Implementar si hay campo de rechazo
            'base_10' => 0,
            'iva_10' => 0,
            'base_5' => 0,
            'iva_5' => 0,
            'exentas' => 0,
            'total_iva' => 0,
            'total_amount' => 0,
        ];

        foreach ($documents as $doc) {
            $ocr = $doc->ocr_data ?? [];
            $stats['base_10'] += $ocr['subtotal_gravado_10'] ?? 0;
            $stats['iva_10'] += $ocr['iva_10'] ?? 0;
            $stats['base_5'] += $ocr['subtotal_gravado_5'] ?? 0;
            $stats['iva_5'] += $ocr['iva_5'] ?? 0;
            $stats['exentas'] += $ocr['subtotal_exentas'] ?? 0;
            $stats['total_iva'] += $ocr['total_iva'] ?? 0;
            $stats['total_amount'] += $ocr['monto_total'] ?? $doc->amount ?? 0;
        }

        return $stats;
    }

    private function getDailyEvolution($tenantId, $entityId = null)
    {
        $query = Document::where('tenant_id', $tenantId)
            ->where('document_date', '>=', now()->subDays(30));

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        $last30Days = $query->select(
                DB::raw('DATE(document_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return $last30Days->map(function($item) {
            return [
                'date' => Carbon::parse($item->date)->format('d/m'),
                'count' => $item->count,
            ];
        });
    }

    private function getTopSuppliers($tenantId, $from, $to, $limit = 10, $entityId = null)
    {
        $query = Document::where('tenant_id', $tenantId)
            ->whereBetween('document_date', [$from, $to]);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        $documents = $query->get();

        $suppliers = [];
        foreach ($documents as $doc) {
            $ocr = $doc->ocr_data ?? [];
            $issuer = $doc->issuer ?? $ocr['razon_social_emisor'] ?? 'Desconocido';
            $amount = $ocr['monto_total'] ?? $doc->amount ?? 0;

            if (!isset($suppliers[$issuer])) {
                $suppliers[$issuer] = [
                    'name' => $issuer,
                    'total' => 0,
                    'count' => 0,
                ];
            }

            $suppliers[$issuer]['total'] += $amount;
            $suppliers[$issuer]['count']++;
        }

        usort($suppliers, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return array_slice($suppliers, 0, $limit);
    }

    private function getIvaDistribution($tenantId, $from, $to, $entityId = null)
    {
        $stats = $this->getMonthStats($tenantId, $from, $to, $entityId);

        $total = $stats['iva_10'] + $stats['iva_5'] + $stats['exentas'];

        if ($total == 0) {
            return [
                ['label' => 'IVA 10%', 'value' => 0, 'percentage' => 0],
                ['label' => 'IVA 5%', 'value' => 0, 'percentage' => 0],
                ['label' => 'Exentas', 'value' => 0, 'percentage' => 0],
            ];
        }

        return [
            [
                'label' => 'IVA 10%',
                'value' => $stats['iva_10'],
                'percentage' => round(($stats['iva_10'] / $total) * 100, 1),
            ],
            [
                'label' => 'IVA 5%',
                'value' => $stats['iva_5'],
                'percentage' => round(($stats['iva_5'] / $total) * 100, 1),
            ],
            [
                'label' => 'Exentas',
                'value' => $stats['exentas'],
                'percentage' => round(($stats['exentas'] / $total) * 100, 1),
            ],
        ];
    }

    private function getAlerts($tenantId, $currentMonthStats)
    {
        $alerts = [];

        // Alerta de facturas pendientes
        if ($currentMonthStats['pending'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Tienes {$currentMonthStats['pending']} factura(s) sin validar en la SET",
                'action' => 'validate',
            ];
        }

        // Alerta de vencimiento (15 del mes siguiente)
        $deadline = now()->startOfMonth()->addMonth()->day(15);
        $daysToDeadline = now()->diffInDays($deadline, false);

        if ($daysToDeadline <= 5 && $daysToDeadline > 0) {
            $alerts[] = [
                'type' => 'urgent',
                'message' => "Faltan {$daysToDeadline} días para el vencimiento de IVA ({$deadline->format('d/m')})",
                'action' => 'export',
            ];
        }

        return $alerts;
    }
    /**
     * Subir documento desde la miniapp
     * POST /api/miniapp/upload
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ]);

        try {
            $user = auth()->user();
            $file = $request->file('document');

            // Obtener información del archivo
            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileContent = file_get_contents($file->getRealPath());
            $mimeType = $file->getMimeType();

            // Procesar con el job (parámetros actualizados)
            OcrInvoiceProcessingJob::dispatch(
                $user,
                null,                           // fileId (null para miniapp)
                $fileName,                      // fileName
                $mimeType,                      // mimeType
                null,                           // chatId (null para miniapp)
                null,                           // promptContext
                base64_encode($fileContent)     // fileContent (base64 para miniapp)
            );

            Log::info('Documento subido desde miniapp', [
                'user_id' => $user->id,
                'file_name' => $fileName,
                'file_size' => strlen($fileContent),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Documento encolado para procesamiento',
                'data' => [
                    'file_name' => $fileName,
                    'file_size' => strlen($fileContent),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error al subir documento desde miniapp', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar el documento'
            ], 500);
        }
    }

}
