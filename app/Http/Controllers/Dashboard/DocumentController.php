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
            ->with('entity')
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
        $this->authorize('view', $document);
        return view('dashboard.documents.show', compact('document'));
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
