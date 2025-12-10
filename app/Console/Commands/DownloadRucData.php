<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadRucData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ruc:download
                            {--skip-download : Saltar descarga y solo procesar archivos existentes}
                            {--file= : Procesar solo un archivo específico (0-9)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Descarga y procesa los archivos de RUC oficiales de la SET Paraguay';

    /**
     * URL base para descargar archivos RUC de la SET
     */
    protected string $baseUrl = 'https://www.set.gov.py/rest/contents/download/collaboration/sites/PARAGUAY-SET/documents/informes-periodicos/ruc/';

    /**
     * Directorio temporal para almacenar archivos
     */
    protected string $tempDir;

    public function __construct()
    {
        parent::__construct();
        $this->tempDir = storage_path('app/ruc_temp');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🇵🇾 Iniciando descarga de datos RUC de la SET Paraguay');
        $this->newLine();

        // Crear directorio temporal
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        // Determinar qué archivos procesar
        $files = $this->option('file') !== null
            ? [(int) $this->option('file')]
            : range(0, 9);

        $totalRecords = 0;

        foreach ($files as $fileNumber) {
            $this->info("📦 Procesando archivo RUC{$fileNumber}...");

            try {
                // Descargar archivo ZIP
                if (!$this->option('skip-download')) {
                    $this->downloadFile($fileNumber);
                }

                // Extraer y procesar
                $records = $this->extractAndProcess($fileNumber);
                $totalRecords += $records;

                $this->info("✅ Archivo RUC{$fileNumber} procesado: {$records} registros");
                $this->newLine();

            } catch (\Exception $e) {
                $this->error("❌ Error procesando RUC{$fileNumber}: " . $e->getMessage());
                continue;
            }
        }

        $this->info("🎉 Proceso completado!");
        $this->info("Total de registros procesados: {$totalRecords}");

        // Limpiar archivos temporales
        if ($this->confirm('¿Deseas eliminar los archivos temporales?', true)) {
            $this->cleanupTempFiles();
            $this->info('🧹 Archivos temporales eliminados');
        }

        return Command::SUCCESS;
    }

    /**
     * Descarga un archivo ZIP de RUC
     */
    protected function downloadFile(int $fileNumber): void
    {
        $fileName = "ruc{$fileNumber}.zip";
        $url = $this->baseUrl . $fileName;
        $destinationPath = $this->tempDir . '/' . $fileName;

        $this->info("  ⬇️  Descargando {$fileName}...");

        try {
            $response = Http::timeout(300)->get($url);

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()}");
            }

            file_put_contents($destinationPath, $response->body());

            $size = round(filesize($destinationPath) / 1024 / 1024, 2);
            $this->info("  ✓ Descargado: {$size} MB");

        } catch (\Exception $e) {
            throw new \Exception("Error descargando {$fileName}: " . $e->getMessage());
        }
    }

    /**
     * Extrae y procesa un archivo ZIP
     */
    protected function extractAndProcess(int $fileNumber): int
    {
        $zipPath = $this->tempDir . "/ruc{$fileNumber}.zip";

        if (!file_exists($zipPath)) {
            throw new \Exception("Archivo ZIP no encontrado: {$zipPath}");
        }

        $this->info("  📂 Extrayendo archivo...");

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception("No se pudo abrir el archivo ZIP");
        }

        $zip->extractTo($this->tempDir);
        $zip->close();

        // Buscar archivo TXT extraído
        $txtFile = $this->tempDir . "/ruc{$fileNumber}.txt";

        if (!file_exists($txtFile)) {
            throw new \Exception("Archivo TXT no encontrado después de extraer");
        }

        $this->info("  💾 Procesando datos...");

        return $this->processTextFile($txtFile, $fileNumber);
    }

    /**
     * Procesa un archivo TXT y lo carga a la base de datos
     */
    protected function processTextFile(string $filePath, int $fileNumber): int
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("No se pudo abrir el archivo TXT");
        }

        $recordCount = 0;
        $batchSize = 500;
        $batch = [];

        // Leer líneas del archivo
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Parsear línea (formato típico: RUC|DV|NOMBRE|TIPO|ESTADO)
            // El formato exacto puede variar, ajustar según sea necesario
            $data = $this->parseLine($line);

            if ($data) {
                $batch[] = $data;
                $recordCount++;

                // Insertar en lotes
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch);
                    $batch = [];
                }
            }
        }

        // Insertar registros restantes
        if (!empty($batch)) {
            $this->insertBatch($batch);
        }

        fclose($handle);

        return $recordCount;
    }

    /**
     * Parsea una línea del archivo TXT
     */
    protected function parseLine(string $line): ?array
    {
        // Los archivos de la SET usan diferentes delimitadores
        // Intentar con pipe (|) primero, luego con tab
        $fields = str_getcsv($line, '|');

        if (count($fields) < 2) {
            $fields = str_getcsv($line, "\t");
        }

        if (count($fields) < 2) {
            return null;
        }

        // Extraer campos (ajustar según formato real)
        $ruc = trim($fields[0] ?? '');
        $dv = trim($fields[1] ?? '');
        $razonSocial = trim($fields[2] ?? '');
        $tipo = trim($fields[3] ?? null);
        $estado = trim($fields[4] ?? 'ACTIVO');

        // Limpiar RUC (quitar guiones)
        $ruc = str_replace(['-', ' ', '.'], '', $ruc);

        if (empty($ruc) || empty($razonSocial)) {
            return null;
        }

        return [
            'ruc' => $ruc,
            'dv' => $dv,
            'razon_social' => mb_substr($razonSocial, 0, 255),
            'tipo_contribuyente' => $tipo,
            'estado' => $estado,
            'fecha_actualizacion_set' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Inserta un lote de registros en la base de datos
     */
    protected function insertBatch(array $batch): void
    {
        try {
            DB::table('ruc_contribuyentes')->upsert(
                $batch,
                ['ruc'], // Columnas únicas
                ['razon_social', 'dv', 'tipo_contribuyente', 'estado', 'fecha_actualizacion_set', 'updated_at'] // Columnas a actualizar
            );
        } catch (\Exception $e) {
            $this->warn("Error insertando lote: " . $e->getMessage());
        }
    }

    /**
     * Limpia archivos temporales
     */
    protected function cleanupTempFiles(): void
    {
        $files = glob($this->tempDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
