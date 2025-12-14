<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Servicio de Validación Fiscal
 *
 * Valida la coherencia matemática de importes fiscales según normativa paraguaya:
 *
 * IMPORTANTE: En Paraguay, el precio final SIEMPRE incluye el IVA.
 * El cálculo es INVERSO (del total hacia las bases):
 *
 * - IVA 10%:
 *   - Total incluye IVA: ₲110,000
 *   - Base = Total / 1.10 = ₲100,000
 *   - IVA = Total - Base = ₲10,000
 *
 * - IVA 5%:
 *   - Total incluye IVA: ₲105,000
 *   - Base = Total / 1.05 = ₲100,000
 *   - IVA = Total - Base = ₲5,000
 *
 * - Exentas: Sin IVA, precio = precio
 */
class FiscalValidationService
{
    /**
     * Margen de error permitido en Guaraníes (debido a redondeos)
     */
    protected const TOLERANCE = 10;

    /**
     * Divisores para cálculo inverso (precio con IVA → base sin IVA)
     */
    protected const IVA_10_DIVISOR = 1.10;
    protected const IVA_5_DIVISOR = 1.05;

    /**
     * Validar coherencia matemática de una factura
     *
     * IMPORTANTE: En Paraguay los subtotales INCLUYEN el IVA.
     * El cálculo es inverso (del total hacia la base).
     *
     * @param array $data Datos de la factura con campos:
     *   - total_amount: Monto total de la factura
     *   - iva_10_base: Subtotal gravado 10% (INCLUYE IVA)
     *   - iva_10: IVA 10% desglosado
     *   - iva_5_base: Subtotal gravado 5% (INCLUYE IVA)
     *   - iva_5: IVA 5% desglosado
     *   - exentas: Monto exento (sin IVA)
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validateInvoiceAmounts(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Convertir a float y manejar valores null
        $totalAmount = (float) ($data['total_amount'] ?? 0);
        $subtotalConIva10 = (float) ($data['iva_10_base'] ?? 0); // Nombre engañoso, es el subtotal CON IVA
        $iva10Declarado = (float) ($data['iva_10'] ?? 0);
        $subtotalConIva5 = (float) ($data['iva_5_base'] ?? 0); // Nombre engañoso, es el subtotal CON IVA
        $iva5Declarado = (float) ($data['iva_5'] ?? 0);
        $exentas = (float) ($data['exentas'] ?? 0);

        // Validación 1: IVA 10% coherente (cálculo inverso)
        // Subtotal incluye IVA → Base = Subtotal / 1.10 → IVA = Subtotal - Base
        if ($subtotalConIva10 > 0 || $iva10Declarado > 0) {
            $baseSinIva10 = round($subtotalConIva10 / self::IVA_10_DIVISOR);
            $ivaEsperado10 = $subtotalConIva10 - $baseSinIva10;
            $difference = abs($iva10Declarado - $ivaEsperado10);

            if ($difference > self::TOLERANCE) {
                $errors[] = sprintf(
                    'IVA 10%% incoherente: Subtotal con IVA=%s, Base calculada=%s, IVA declarado=%s, IVA esperado=%s (diferencia: %s)',
                    number_format($subtotalConIva10, 0, ',', '.'),
                    number_format($baseSinIva10, 0, ',', '.'),
                    number_format($iva10Declarado, 0, ',', '.'),
                    number_format($ivaEsperado10, 0, ',', '.'),
                    number_format($difference, 0, ',', '.')
                );
            }
        }

        // Validación 2: IVA 5% coherente (cálculo inverso)
        // Subtotal incluye IVA → Base = Subtotal / 1.05 → IVA = Subtotal - Base
        if ($subtotalConIva5 > 0 || $iva5Declarado > 0) {
            $baseSinIva5 = round($subtotalConIva5 / self::IVA_5_DIVISOR);
            $ivaEsperado5 = $subtotalConIva5 - $baseSinIva5;
            $difference = abs($iva5Declarado - $ivaEsperado5);

            if ($difference > self::TOLERANCE) {
                $errors[] = sprintf(
                    'IVA 5%% incoherente: Subtotal con IVA=%s, Base calculada=%s, IVA declarado=%s, IVA esperado=%s (diferencia: %s)',
                    number_format($subtotalConIva5, 0, ',', '.'),
                    number_format($baseSinIva5, 0, ',', '.'),
                    number_format($iva5Declarado, 0, ',', '.'),
                    number_format($ivaEsperado5, 0, ',', '.'),
                    number_format($difference, 0, ',', '.')
                );
            }
        }

        // Validación 3: Total coherente con suma de subtotales (todos incluyen IVA ya)
        // Total = Subtotal10% (con IVA) + Subtotal5% (con IVA) + Exentas
        $calculatedTotal = $subtotalConIva10 + $subtotalConIva5 + $exentas;
        $totalDifference = abs($totalAmount - $calculatedTotal);

        if ($totalDifference > self::TOLERANCE) {
            $errors[] = sprintf(
                'Total incoherente: Total declarado=%s, Suma calculada=%s (Subtotal 10%%=%s + Subtotal 5%%=%s + Exentas=%s) (diferencia: %s)',
                number_format($totalAmount, 0, ',', '.'),
                number_format($calculatedTotal, 0, ',', '.'),
                number_format($subtotalConIva10, 0, ',', '.'),
                number_format($subtotalConIva5, 0, ',', '.'),
                number_format($exentas, 0, ',', '.'),
                number_format($totalDifference, 0, ',', '.')
            );
        }

        // Validación 4: Si hay total pero no hay desglose
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
     * Intentar corregir automáticamente importes basándose en cálculo inverso
     *
     * En Paraguay, el subtotal INCLUYE IVA, por lo que:
     * - Si tenemos subtotal, calculamos IVA = Subtotal - (Subtotal / 1.1)
     * - Si solo tenemos IVA, calculamos Subtotal = IVA * (1.1 / 0.1)
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

        // Auto-corrección de IVA 10% si tenemos subtotal (con IVA) pero IVA incorrecto
        if (isset($data['iva_10_base']) && $data['iva_10_base'] > 0) {
            $subtotalConIva = $data['iva_10_base'];
            $baseSinIva = round($subtotalConIva / self::IVA_10_DIVISOR);
            $ivaEsperado = $subtotalConIva - $baseSinIva;
            $currentIva10 = (float) ($data['iva_10'] ?? 0);

            if (abs($currentIva10 - $ivaEsperado) > self::TOLERANCE) {
                $data['iva_10'] = $ivaEsperado;
                $corrected = true;
                Log::info('Auto-corrección IVA 10% (cálculo inverso)', [
                    'subtotal_con_iva' => $subtotalConIva,
                    'base_sin_iva' => $baseSinIva,
                    'iva_original' => $currentIva10,
                    'iva_corregido' => $ivaEsperado,
                ]);
            }
        }

        // Auto-corrección de IVA 5% si tenemos subtotal (con IVA) pero IVA incorrecto
        if (isset($data['iva_5_base']) && $data['iva_5_base'] > 0) {
            $subtotalConIva = $data['iva_5_base'];
            $baseSinIva = round($subtotalConIva / self::IVA_5_DIVISOR);
            $ivaEsperado = $subtotalConIva - $baseSinIva;
            $currentIva5 = (float) ($data['iva_5'] ?? 0);

            if (abs($currentIva5 - $ivaEsperado) > self::TOLERANCE) {
                $data['iva_5'] = $ivaEsperado;
                $corrected = true;
                Log::info('Auto-corrección IVA 5% (cálculo inverso)', [
                    'subtotal_con_iva' => $subtotalConIva,
                    'base_sin_iva' => $baseSinIva,
                    'iva_original' => $currentIva5,
                    'iva_corregido' => $ivaEsperado,
                ]);
            }
        }

        // Auto-corrección de subtotal si tenemos IVA pero no subtotal
        // Subtotal (con IVA) = IVA * (1.1 / 0.1) = IVA * 11
        if (isset($data['iva_10']) && $data['iva_10'] > 0 && (!isset($data['iva_10_base']) || $data['iva_10_base'] == 0)) {
            $data['iva_10_base'] = round($data['iva_10'] * 11); // IVA * (1.1 / 0.1)
            $corrected = true;
            Log::info('Auto-corrección subtotal 10% desde IVA', [
                'iva' => $data['iva_10'],
                'subtotal_calculado' => $data['iva_10_base'],
            ]);
        }

        // Subtotal (con IVA) = IVA * (1.05 / 0.05) = IVA * 21
        if (isset($data['iva_5']) && $data['iva_5'] > 0 && (!isset($data['iva_5_base']) || $data['iva_5_base'] == 0)) {
            $data['iva_5_base'] = round($data['iva_5'] * 21); // IVA * (1.05 / 0.05)
            $corrected = true;
            Log::info('Auto-corrección subtotal 5% desde IVA', [
                'iva' => $data['iva_5'],
                'subtotal_calculado' => $data['iva_5_base'],
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
