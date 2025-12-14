<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Servicio de Validación Fiscal
 *
 * Valida la coherencia matemática de importes fiscales según normativa paraguaya:
 * - IVA 10%: Total incluye IVA, Base = Total / 1.1, IVA = Base * 0.1
 * - IVA 5%: Total incluye IVA, Base = Total / 1.05, IVA = Base * 0.05
 * - Exentas: Sin IVA
 */
class FiscalValidationService
{
    /**
     * Margen de error permitido en Guaraníes (debido a redondeos)
     */
    protected const TOLERANCE = 5;

    /**
     * Tasas de IVA en Paraguay
     */
    protected const IVA_10_RATE = 0.10;
    protected const IVA_5_RATE = 0.05;
    protected const IVA_10_DIVISOR = 1.10;
    protected const IVA_5_DIVISOR = 1.05;

    /**
     * Validar coherencia matemática de una factura
     *
     * @param array $data Datos de la factura con campos:
     *   - total_amount: Monto total
     *   - iva_10_base: Base imponible IVA 10%
     *   - iva_10: IVA 10%
     *   - iva_5_base: Base imponible IVA 5%
     *   - iva_5: IVA 5%
     *   - exentas: Monto exento
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validateInvoiceAmounts(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Convertir a float y manejar valores null
        $totalAmount = (float) ($data['total_amount'] ?? 0);
        $iva10Base = (float) ($data['iva_10_base'] ?? 0);
        $iva10 = (float) ($data['iva_10'] ?? 0);
        $iva5Base = (float) ($data['iva_5_base'] ?? 0);
        $iva5 = (float) ($data['iva_5'] ?? 0);
        $exentas = (float) ($data['exentas'] ?? 0);

        // Validación 1: IVA 10% coherente con su base
        if ($iva10Base > 0 || $iva10 > 0) {
            $expectedIva10 = round($iva10Base * self::IVA_10_RATE);
            $difference = abs($iva10 - $expectedIva10);

            if ($difference > self::TOLERANCE) {
                $errors[] = sprintf(
                    'IVA 10%% incoherente: Base=%s, IVA declarado=%s, IVA esperado=%s (diferencia: %s)',
                    number_format($iva10Base, 0, ',', '.'),
                    number_format($iva10, 0, ',', '.'),
                    number_format($expectedIva10, 0, ',', '.'),
                    number_format($difference, 0, ',', '.')
                );
            }
        }

        // Validación 2: IVA 5% coherente con su base
        if ($iva5Base > 0 || $iva5 > 0) {
            $expectedIva5 = round($iva5Base * self::IVA_5_RATE);
            $difference = abs($iva5 - $expectedIva5);

            if ($difference > self::TOLERANCE) {
                $errors[] = sprintf(
                    'IVA 5%% incoherente: Base=%s, IVA declarado=%s, IVA esperado=%s (diferencia: %s)',
                    number_format($iva5Base, 0, ',', '.'),
                    number_format($iva5, 0, ',', '.'),
                    number_format($expectedIva5, 0, ',', '.'),
                    number_format($difference, 0, ',', '.')
                );
            }
        }

        // Validación 3: Total coherente con suma de componentes
        $calculatedTotal = ($iva10Base + $iva10) + ($iva5Base + $iva5) + $exentas;
        $totalDifference = abs($totalAmount - $calculatedTotal);

        if ($totalDifference > self::TOLERANCE) {
            $errors[] = sprintf(
                'Total incoherente: Total declarado=%s, Suma de componentes=%s (diferencia: %s)',
                number_format($totalAmount, 0, ',', '.'),
                number_format($calculatedTotal, 0, ',', '.'),
                number_format($totalDifference, 0, ',', '.')
            );
        }

        // Validación 4: Si hay total pero no hay desglose, intentar inferir
        if ($totalAmount > 0 && $calculatedTotal == 0) {
            $warnings[] = 'Factura sin desglose de IVA. Se requiere revisión manual para clasificar correctamente.';
        }

        // Validación 5: Verificar que al menos haya algún monto
        if ($totalAmount == 0 && $calculatedTotal == 0) {
            $errors[] = 'Factura sin montos. Verificar que la extracción OCR fue correcta.';
        }

        // Log de validación
        if (!empty($errors)) {
            Log::warning('Validación fiscal encontró errores matemáticos', [
                'data' => $data,
                'errors' => $errors,
                'warnings' => $warnings,
            ]);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Intentar corregir automáticamente importes basándose en el total
     *
     * @param array $data Datos de factura
     * @return array Datos corregidos con 'corrected' => bool
     */
    public function autoCorrectAmounts(array $data): array
    {
        $corrected = false;
        $totalAmount = (float) ($data['total_amount'] ?? 0);

        // Si tenemos total pero no desglose, no podemos auto-corregir
        // Se necesita intervención manual
        if ($totalAmount == 0) {
            return array_merge($data, ['corrected' => false]);
        }

        // Auto-corrección de IVA 10% si tenemos base pero IVA incorrecto
        if (isset($data['iva_10_base']) && $data['iva_10_base'] > 0) {
            $expectedIva10 = round($data['iva_10_base'] * self::IVA_10_RATE);
            $currentIva10 = (float) ($data['iva_10'] ?? 0);

            if (abs($currentIva10 - $expectedIva10) > self::TOLERANCE) {
                $data['iva_10'] = $expectedIva10;
                $corrected = true;
                Log::info('Auto-corrección IVA 10%', [
                    'original' => $currentIva10,
                    'corrected' => $expectedIva10,
                ]);
            }
        }

        // Auto-corrección de IVA 5% si tenemos base pero IVA incorrecto
        if (isset($data['iva_5_base']) && $data['iva_5_base'] > 0) {
            $expectedIva5 = round($data['iva_5_base'] * self::IVA_5_RATE);
            $currentIva5 = (float) ($data['iva_5'] ?? 0);

            if (abs($currentIva5 - $expectedIva5) > self::TOLERANCE) {
                $data['iva_5'] = $expectedIva5;
                $corrected = true;
                Log::info('Auto-corrección IVA 5%', [
                    'original' => $currentIva5,
                    'corrected' => $expectedIva5,
                ]);
            }
        }

        // Auto-corrección de base si tenemos IVA pero no base
        if (isset($data['iva_10']) && $data['iva_10'] > 0 && (!isset($data['iva_10_base']) || $data['iva_10_base'] == 0)) {
            $data['iva_10_base'] = round($data['iva_10'] / self::IVA_10_RATE);
            $corrected = true;
            Log::info('Auto-corrección base IVA 10%', [
                'iva' => $data['iva_10'],
                'base_calculada' => $data['iva_10_base'],
            ]);
        }

        if (isset($data['iva_5']) && $data['iva_5'] > 0 && (!isset($data['iva_5_base']) || $data['iva_5_base'] == 0)) {
            $data['iva_5_base'] = round($data['iva_5'] / self::IVA_5_RATE);
            $corrected = true;
            Log::info('Auto-corrección base IVA 5%', [
                'iva' => $data['iva_5'],
                'base_calculada' => $data['iva_5_base'],
            ]);
        }

        return array_merge($data, ['corrected' => $corrected]);
    }

    /**
     * Calcular IVA crédito total de una factura
     *
     * @param array $data Datos de factura
     * @return float IVA crédito total
     */
    public function calculateTotalIvaCredito(array $data): float
    {
        $iva10 = (float) ($data['iva_10'] ?? 0);
        $iva5 = (float) ($data['iva_5'] ?? 0);

        return $iva10 + $iva5;
    }

    /**
     * Validar formato de RUC paraguayo
     *
     * @param string|null $ruc
     * @return bool
     */
    public function validateRuc(?string $ruc): bool
    {
        if (empty($ruc)) {
            return false;
        }

        // Formato: XX-XXXXXXX-X o XXXXXXXXXX (10 dígitos)
        $ruc = preg_replace('/[^0-9]/', '', $ruc);

        return strlen($ruc) >= 6 && strlen($ruc) <= 10;
    }

    /**
     * Validar formato de CDC paraguayo
     *
     * @param string|null $cdc
     * @return bool
     */
    public function validateCdc(?string $cdc): bool
    {
        if (empty($cdc)) {
            return false;
        }

        // CDC debe tener exactamente 44 dígitos
        $cdc = preg_replace('/[^0-9]/', '', $cdc);

        return strlen($cdc) === 44;
    }

    /**
     * Generar resumen de validación para mostrar al usuario
     *
     * @param array $validation Resultado de validateInvoiceAmounts()
     * @return string
     */
    public function getValidationSummary(array $validation): string
    {
        if ($validation['valid']) {
            return '✅ Importes validados correctamente';
        }

        $summary = '⚠️ Requiere revisión manual:\n';

        foreach ($validation['errors'] as $error) {
            $summary .= "• $error\n";
        }

        foreach ($validation['warnings'] as $warning) {
            $summary .= "⚡ $warning\n";
        }

        return trim($summary);
    }
}
