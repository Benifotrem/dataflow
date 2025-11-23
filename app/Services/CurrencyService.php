<?php

namespace App\Services;

class CurrencyService
{
    /**
     * Monedas disponibles por país
     * Incluye todos los países de habla hispana + Brasil + Guaraní paraguayo
     */
    protected static $currencies = [
        // América del Sur
        'AR' => ['code' => 'ARS', 'symbol' => '$', 'name' => 'Peso Argentino', 'decimals' => 2],
        'BO' => ['code' => 'BOB', 'symbol' => 'Bs', 'name' => 'Boliviano', 'decimals' => 2],
        'BR' => ['code' => 'BRL', 'symbol' => 'R$', 'name' => 'Real Brasileño', 'decimals' => 2],
        'CL' => ['code' => 'CLP', 'symbol' => '$', 'name' => 'Peso Chileno', 'decimals' => 0],
        'CO' => ['code' => 'COP', 'symbol' => '$', 'name' => 'Peso Colombiano', 'decimals' => 0],
        'EC' => ['code' => 'USD', 'symbol' => '$', 'name' => 'Dólar Estadounidense', 'decimals' => 2],
        'GY' => ['code' => 'GYD', 'symbol' => '$', 'name' => 'Dólar Guyanés', 'decimals' => 2],
        'PY' => ['code' => 'PYG', 'symbol' => '₲', 'name' => 'Guaraní Paraguayo', 'decimals' => 0],
        'PE' => ['code' => 'PEN', 'symbol' => 'S/', 'name' => 'Sol Peruano', 'decimals' => 2],
        'SR' => ['code' => 'SRD', 'symbol' => '$', 'name' => 'Dólar Surinamés', 'decimals' => 2],
        'UY' => ['code' => 'UYU', 'symbol' => '$', 'name' => 'Peso Uruguayo', 'decimals' => 2],
        'VE' => ['code' => 'VES', 'symbol' => 'Bs.S', 'name' => 'Bolívar Soberano', 'decimals' => 2],

        // América Central y Caribe
        'BZ' => ['code' => 'BZD', 'symbol' => 'BZ$', 'name' => 'Dólar Beliceño', 'decimals' => 2],
        'CR' => ['code' => 'CRC', 'symbol' => '₡', 'name' => 'Colón Costarricense', 'decimals' => 2],
        'CU' => ['code' => 'CUP', 'symbol' => '₱', 'name' => 'Peso Cubano', 'decimals' => 2],
        'DO' => ['code' => 'DOP', 'symbol' => 'RD$', 'name' => 'Peso Dominicano', 'decimals' => 2],
        'SV' => ['code' => 'USD', 'symbol' => '$', 'name' => 'Dólar Estadounidense', 'decimals' => 2],
        'GT' => ['code' => 'GTQ', 'symbol' => 'Q', 'name' => 'Quetzal Guatemalteco', 'decimals' => 2],
        'HT' => ['code' => 'HTG', 'symbol' => 'G', 'name' => 'Gourde Haitiano', 'decimals' => 2],
        'HN' => ['code' => 'HNL', 'symbol' => 'L', 'name' => 'Lempira Hondureño', 'decimals' => 2],
        'JM' => ['code' => 'JMD', 'symbol' => 'J$', 'name' => 'Dólar Jamaicano', 'decimals' => 2],
        'NI' => ['code' => 'NIO', 'symbol' => 'C$', 'name' => 'Córdoba Nicaragüense', 'decimals' => 2],
        'PA' => ['code' => 'PAB', 'symbol' => 'B/.', 'name' => 'Balboa Panameño', 'decimals' => 2],
        'PR' => ['code' => 'USD', 'symbol' => '$', 'name' => 'Dólar Estadounidense', 'decimals' => 2],

        // América del Norte
        'MX' => ['code' => 'MXN', 'symbol' => '$', 'name' => 'Peso Mexicano', 'decimals' => 2],
        'US' => ['code' => 'USD', 'symbol' => '$', 'name' => 'Dólar Estadounidense', 'decimals' => 2],

        // Europa
        'ES' => ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro', 'decimals' => 2],
        'AD' => ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro', 'decimals' => 2],
        'GQ' => ['code' => 'XAF', 'symbol' => 'FCFA', 'name' => 'Franco CFA', 'decimals' => 0],
    ];

    /**
     * Nombres de países en español
     */
    protected static $countries = [
        'AR' => 'Argentina',
        'BO' => 'Bolivia',
        'BR' => 'Brasil',
        'CL' => 'Chile',
        'CO' => 'Colombia',
        'CR' => 'Costa Rica',
        'CU' => 'Cuba',
        'DO' => 'República Dominicana',
        'EC' => 'Ecuador',
        'SV' => 'El Salvador',
        'GQ' => 'Guinea Ecuatorial',
        'GT' => 'Guatemala',
        'HN' => 'Honduras',
        'MX' => 'México',
        'NI' => 'Nicaragua',
        'PA' => 'Panamá',
        'PY' => 'Paraguay',
        'PE' => 'Perú',
        'PR' => 'Puerto Rico',
        'ES' => 'España',
        'UY' => 'Uruguay',
        'VE' => 'Venezuela',
        'US' => 'Estados Unidos',
        'AD' => 'Andorra',
        'BZ' => 'Belice',
        'GY' => 'Guyana',
        'HT' => 'Haití',
        'JM' => 'Jamaica',
        'SR' => 'Surinam',
    ];

    /**
     * Obtener todas las monedas disponibles
     */
    public static function getAllCurrencies(): array
    {
        // Obtener monedas únicas (algunos países comparten moneda)
        $currencies = [];
        foreach (self::$currencies as $countryCode => $currency) {
            $currencyCode = $currency['code'];
            if (!isset($currencies[$currencyCode])) {
                $currencies[$currencyCode] = $currency;
            }
        }
        return $currencies;
    }

    /**
     * Obtener moneda por código de país
     */
    public static function getCurrencyByCountry(string $countryCode): ?array
    {
        return self::$currencies[$countryCode] ?? null;
    }

    /**
     * Obtener moneda por código de moneda
     */
    public static function getCurrencyByCode(string $currencyCode): ?array
    {
        foreach (self::$currencies as $currency) {
            if ($currency['code'] === $currencyCode) {
                return $currency;
            }
        }
        return null;
    }

    /**
     * Formatear cantidad con moneda
     */
    public static function format(float $amount, string $currencyCode, bool $showSymbol = true): string
    {
        $currency = self::getCurrencyByCode($currencyCode);

        if (!$currency) {
            // Fallback a formato básico
            return number_format($amount, 2, ',', '.');
        }

        $decimals = $currency['decimals'];
        $formatted = number_format($amount, $decimals, ',', '.');

        if ($showSymbol) {
            // Para símbolos como $ que van adelante
            if (in_array($currency['symbol'], ['$', 'R$', 'Bs', 'BZ$', 'RD$', 'J$', 'C$', '€'])) {
                return $currency['symbol'] . ' ' . $formatted;
            }
            // Para símbolos como ₲, S/, Q que pueden ir adelante
            return $currency['symbol'] . ' ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Formatear cantidad con código de moneda
     */
    public static function formatWithCode(float $amount, string $currencyCode): string
    {
        $currency = self::getCurrencyByCode($currencyCode);

        if (!$currency) {
            return number_format($amount, 2, ',', '.') . ' ' . $currencyCode;
        }

        $decimals = $currency['decimals'];
        $formatted = number_format($amount, $decimals, ',', '.');

        return $formatted . ' ' . $currencyCode;
    }

    /**
     * Obtener todos los países
     */
    public static function getAllCountries(): array
    {
        return self::$countries;
    }

    /**
     * Obtener nombre de país
     */
    public static function getCountryName(string $countryCode): ?string
    {
        return self::$countries[$countryCode] ?? null;
    }

    /**
     * Obtener países con sus monedas
     */
    public static function getCountriesWithCurrencies(): array
    {
        $result = [];
        foreach (self::$countries as $code => $name) {
            $currency = self::getCurrencyByCountry($code);
            $result[$code] = [
                'name' => $name,
                'currency' => $currency,
            ];
        }
        return $result;
    }

    /**
     * Validar código de moneda
     */
    public static function isValidCurrency(string $currencyCode): bool
    {
        return self::getCurrencyByCode($currencyCode) !== null;
    }

    /**
     * Validar código de país
     */
    public static function isValidCountry(string $countryCode): bool
    {
        return isset(self::$countries[$countryCode]);
    }

    /**
     * Obtener monedas únicas agrupadas
     */
    public static function getUniqueCurrencies(): array
    {
        $unique = [];
        foreach (self::$currencies as $currency) {
            $code = $currency['code'];
            if (!isset($unique[$code])) {
                $unique[$code] = $currency;
            }
        }
        // Ordenar alfabéticamente por nombre
        uasort($unique, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $unique;
    }
}
