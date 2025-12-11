<?php

use App\Services\CurrencyService;

if (!function_exists('format_currency')) {
    /**
     * Formatear un monto con la moneda del tenant actual
     */
    function format_currency(float $amount, ?string $currencyCode = null, bool $showSymbol = true): string
    {
        // Si no se especifica moneda, usar la del tenant actual
        if (!$currencyCode && auth()->check() && auth()->user()->tenant) {
            $currencyCode = auth()->user()->tenant->currency_code;
        }

        // Fallback a USD si no hay moneda
        $currencyCode = $currencyCode ?? 'USD';

        return CurrencyService::format($amount, $currencyCode, $showSymbol);
    }
}

if (!function_exists('format_currency_with_code')) {
    /**
     * Formatear un monto con el código de moneda
     */
    function format_currency_with_code(float $amount, ?string $currencyCode = null): string
    {
        // Si no se especifica moneda, usar la del tenant actual
        if (!$currencyCode && auth()->check() && auth()->user()->tenant) {
            $currencyCode = auth()->user()->tenant->currency_code;
        }

        // Fallback a USD si no hay moneda
        $currencyCode = $currencyCode ?? 'USD';

        return CurrencyService::formatWithCode($amount, $currencyCode);
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Obtener el símbolo de la moneda del tenant actual
     */
    function get_currency_symbol(?string $currencyCode = null): string
    {
        // Si no se especifica moneda, usar la del tenant actual
        if (!$currencyCode && auth()->check() && auth()->user()->tenant) {
            $currencyCode = auth()->user()->tenant->currency_code;
        }

        // Fallback a USD si no hay moneda
        $currencyCode = $currencyCode ?? 'USD';

        $currency = CurrencyService::getCurrencyByCode($currencyCode);
        return $currency['symbol'] ?? '$';
    }
}

if (!function_exists('get_tenant_currency')) {
    /**
     * Obtener información completa de la moneda del tenant actual
     */
    function get_tenant_currency(): ?array
    {
        if (auth()->check() && auth()->user()->tenant) {
            return CurrencyService::getCurrencyByCode(auth()->user()->tenant->currency_code);
        }
        return null;
    }
}

if (!function_exists('get_tenant_country')) {
    /**
     * Obtener nombre del país del tenant actual
     */
    function get_tenant_country(): ?string
    {
        if (auth()->check() && auth()->user()->tenant) {
            return CurrencyService::getCountryName(auth()->user()->tenant->country_code);
        }
        return null;
    }
}

if (!function_exists('parse_paraguayan_number')) {
    /**
     * Parsear números paraguayos que usan punto como separador de miles
     *
     * Ejemplos:
     * - "768.455" → 768455
     * - "1.500.000" → 1500000
     * - "90.000" → 90000
     * - 768.455 (float) → 768455
     *
     * @param string|int|float $value
     * @return float
     */
    function parse_paraguayan_number($value): float
    {
        if (is_numeric($value) && !is_string($value)) {
            return (float) $value;
        }

        // Convertir a string y eliminar espacios
        $value = trim((string) $value);

        // Si tiene punto como separador de miles (formato paraguayo)
        // Eliminar todos los puntos y tratar como número entero
        $value = str_replace('.', '', $value);

        // Reemplazar coma por punto (por si acaso hay decimales con coma)
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}
