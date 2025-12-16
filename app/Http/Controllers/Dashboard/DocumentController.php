<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Entity;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected $ocrService;

    public function __construct(OcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    public function index(Request $request)
    {
        $documents = Document::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.documents.index', compact('documents'));
    }

    public function create()
    {
        $entities = Entity::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('dashboard.documents.create', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,xlsx,xls,csv',
            'entity_id' => 'required|exists:entities,id',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('documents', 'local');

            $document = Document::create([
                'tenant_id' => auth()->user()->tenant_id,
                'entity_id' => $request->entity_id,
                'user_id' => auth()->id(),
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'ocr_status' => 'pending',
            ]);

            // Procesar con OCR en segundo plano (aquí simplificado)
            // En producción esto debería ser un Job
            try {
                $this->ocrService->processDocument($document);
            } catch (\Exception $e) {
                // Log error pero no fallar
                logger()->error('OCR processing failed: ' . $e->getMessage());
            }

            return redirect()->route('documents.index')
                ->with('success', 'Documento subido exitosamente. Procesándose...');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al subir documento: ' . $e->getMessage()]);
        }
    }

    public function show(Document $document)
    {
        try {
            $this->authorize('view', $document);

            // Eager load relationships
            $document->load('entity', 'user');

            return view('dashboard.documents.show', compact('document'));
        } catch (\Exception $e) {
            logger()->error('Error showing document: ' . $e->getMessage(), [
                'document_id' => $document->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('documents.index')
                ->withErrors(['error' => 'Error al mostrar el documento: ' . $e->getMessage()]);
        }
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);

        $document->load('entity', 'user');

        return view('dashboard.documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        // Validar según el tipo de factura
        $invoiceType = $document->ocr_data['invoice_type'] ?? null;

        if ($invoiceType === 'foreign') {
            $validated = $request->validate([
                'vendor_name' => 'required|string|max:255',
                'vendor_country' => 'nullable|string|max:100',
                'invoice_number' => 'required|string|max:100',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date',
                'currency' => 'required|string|max:10',
                'subtotal' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
                'monto_total' => 'required|numeric|min:0',
                'service_description' => 'nullable|string|max:500',
                'payment_method' => 'nullable|string|max:100',
                'observations' => 'nullable|string|max:1000',
            ]);

            // Actualizar ocr_data
            $ocrData = $document->ocr_data;
            $ocrData['vendor_name'] = $validated['vendor_name'];
            $ocrData['vendor_country'] = $validated['vendor_country'] ?? null;
            $ocrData['invoice_number'] = $validated['invoice_number'];
            $ocrData['invoice_date'] = $validated['invoice_date'];
            $ocrData['due_date'] = $validated['due_date'] ?? null;
            $ocrData['currency'] = $validated['currency'];
            $ocrData['subtotal'] = $validated['subtotal'] ?? null;
            $ocrData['tax_amount'] = $validated['tax_amount'] ?? null;
            $ocrData['tax_percentage'] = $validated['tax_percentage'] ?? null;
            $ocrData['monto_total'] = $validated['monto_total'];
            $ocrData['service_description'] = $validated['service_description'] ?? null;
            $ocrData['payment_method'] = $validated['payment_method'] ?? null;
            $ocrData['observations'] = $validated['observations'] ?? null;

            $document->ocr_data = $ocrData;
            $document->save();

            return redirect()->route('documents.show', $document)
                ->with('success', 'Factura actualizada exitosamente.');
        }

        // Si no es factura extranjera, por ahora no permitimos editar
        return back()->withErrors(['error' => 'Solo se pueden editar facturas extranjeras por ahora.']);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        if ($document->file_path) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Documento eliminado exitosamente.');
    }
}
