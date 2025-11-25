<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Services\OcrService;
use Illuminate\Console\Command;

class ProcessPendingDocuments extends Command
{
    protected $signature = 'dataflow:process-documents {--limit=10}';
    protected $description = 'Procesa documentos pendientes con OCR/IA';

    public function handle(OcrService $service)
    {
        $limit = $this->option('limit');
        
        $documents = Document::where('ocr_status', 'pending')
            ->limit($limit)
            ->get();

        if ($documents->isEmpty()) {
            $this->info('No hay documentos pendientes');
            return Command::SUCCESS;
        }

        $this->info("Procesando {$documents->count()} documentos...");
        
        $bar = $this->output->createProgressBar($documents->count());
        $processed = 0;
        $failed = 0;

        foreach ($documents as $document) {
            try {
                $service->processDocument($document);
                $processed++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nError procesando documento #{$document->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Procesados: {$processed}");
        if ($failed > 0) {
            $this->warn("✗ Fallidos: {$failed}");
        }

        return Command::SUCCESS;
    }
}
