<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToImage\Pdf;

/**
 * Servicio para convertir PDFs a imágenes
 */
class PdfConverterService
{
    /**
     * Convertir PDF a imagen (primera página)
     *
     * @param string $pdfPath Ruta del PDF
     * @return array ['success' => bool, 'image_path' => string|null, 'error' => string|null]
     */
    public function convertToImage(string $pdfPath): array
    {
        try {
            if (!extension_loaded('imagick')) {
                throw new \Exception('Imagick extension no está instalada. Por favor instala php-imagick en el servidor.');
            }

            // Crear directorio temporal si no existe
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Nombre único para la imagen (PNG para evitar pérdida de calidad)
            $imageName = 'pdf_' . uniqid() . '.png';
            $imagePath = $tempDir . '/' . $imageName;

            // Convertir PDF a imagen (solo primera página)
            $pdf = new Pdf($pdfPath);
            $pdf->setPage(1)
                ->setResolution(600) // Resolución muy alta para máxima legibilidad
                ->setOutputFormat('png') // PNG para evitar compresión con pérdida
                ->saveImage($imagePath);

            // Obtener tamaño de la imagen generada
            $imageSize = file_exists($imagePath) ? filesize($imagePath) : 0;

            Log::info('PDF convertido a imagen exitosamente', [
                'pdf_path' => $pdfPath,
                'image_path' => $imagePath,
                'image_size_kb' => round($imageSize / 1024, 2),
                'resolution' => 600,
                'format' => 'png',
            ]);

            return [
                'success' => true,
                'image_path' => $imagePath,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('Error al convertir PDF a imagen', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'image_path' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Limpiar archivos temporales
     */
    public function cleanupTempFiles(): void
    {
        $tempDir = storage_path('app/temp');
        if (file_exists($tempDir)) {
            $files = glob($tempDir . '/*');
            $now = time();

            foreach ($files as $file) {
                if (is_file($file)) {
                    // Eliminar archivos más antiguos de 1 hora
                    if ($now - filemtime($file) >= 3600) {
                        unlink($file);
                    }
                }
            }
        }
    }
}
