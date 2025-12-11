<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Conector para validación fiscal con la SET (DNIT) de Paraguay
 *
 * Este servicio se conecta al sistema de la Dirección Nacional de Ingresos Tributarios
 * para validar RUCs, Timbrados y datos de facturas electrónicas.
 */
class DnitConnector
{
    /**
     * Timeout para las peticiones SOAP (en segundos)
     */
    protected int $timeout = 30;

    /**
     * Número máximo de reintentos para errores temporales
     */
    protected int $maxRetries = 3;

    /**
     * Tiempo de caché para validaciones exitosas (en segundos)
     * 30 días = 2592000 segundos
     */
    protected int $cacheTime = 2592000;

    /**
     * URL del servicio WSDL de la SET
     */
    protected string $wsdlUrl;

    /**
     * Credenciales de autenticación
     */
    protected ?string $username;
    protected ?string $password;

    public function __construct()
    {
        $this->wsdlUrl = config('services.dnit.wsdl_url', 'https://ekuatia.set.gov.py/consultas/qr');
        $this->username = config('services.dnit.username');
        $this->password = config('services.dnit.password');
    }

    /**
     * Validar RUC (Registro Único de Contribuyentes)
     *
     * @param string $ruc RUC a validar (formato: 12345678-9)
     * @return array ['valid' => bool, 'data' => array, 'error' => string|null]
     */
    public function validateRuc(string $ruc): array
    {
        // Limpiar RUC (remover guiones y espacios)
        $ruc = preg_replace('/[^0-9]/', '', $ruc);

        // Validar formato básico
        if (strlen($ruc) < 6 || strlen($ruc) > 10) {
            return [
                'valid' => false,
                'data' => null,
                'error' => 'Formato de RUC inválido. Debe tener entre 6 y 10 dígitos.',
            ];
        }

        // Verificar en caché
        $cacheKey = "dnit:ruc:{$ruc}";
        if (Cache::has($cacheKey)) {
            Log::info('RUC obtenido desde caché', ['ruc' => $ruc]);
            return Cache::get($cacheKey);
        }

        try {
            // Llamar al servicio de la SET con reintentos
            $response = $this->callWithRetry(function () use ($ruc) {
                return $this->queryRucApi($ruc);
            });

            $result = [
                'valid' => $response['valid'] ?? false,
                'data' => $response['data'] ?? null,
                'error' => $response['error'] ?? null,
            ];

            // Cachear solo respuestas exitosas
            if ($result['valid']) {
                Cache::put($cacheKey, $result, $this->cacheTime);
                Log::info('RUC validado exitosamente', ['ruc' => $ruc]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error al validar RUC', [
                'ruc' => $ruc,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'data' => null,
                'error' => 'Error de conexión con la SET: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validar Timbrado de factura
     *
     * @param string $timbrado Número de timbrado (8 dígitos)
     * @param string $ruc RUC del emisor
     * @return array ['valid' => bool, 'data' => array, 'error' => string|null]
     */
    public function validateTimbrado(string $timbrado, string $ruc): array
    {
        // Limpiar inputs
        $timbrado = preg_replace('/[^0-9]/', '', $timbrado);
        $ruc = preg_replace('/[^0-9]/', '', $ruc);

        // Validar formato básico
        if (strlen($timbrado) !== 8) {
            return [
                'valid' => false,
                'data' => null,
                'error' => 'Formato de Timbrado inválido. Debe tener 8 dígitos.',
            ];
        }

        // Verificar en caché
        $cacheKey = "dnit:timbrado:{$ruc}:{$timbrado}";
        if (Cache::has($cacheKey)) {
            Log::info('Timbrado obtenido desde caché', ['timbrado' => $timbrado, 'ruc' => $ruc]);
            return Cache::get($cacheKey);
        }

        try {
            // Llamar al servicio de la SET con reintentos
            $response = $this->callWithRetry(function () use ($timbrado, $ruc) {
                return $this->queryTimbradoApi($timbrado, $ruc);
            });

            $result = [
                'valid' => $response['valid'] ?? false,
                'data' => $response['data'] ?? null,
                'error' => $response['error'] ?? null,
            ];

            // Cachear solo respuestas exitosas
            if ($result['valid']) {
                Cache::put($cacheKey, $result, $this->cacheTime);
                Log::info('Timbrado validado exitosamente', ['timbrado' => $timbrado, 'ruc' => $ruc]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error al validar Timbrado', [
                'timbrado' => $timbrado,
                'ruc' => $ruc,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'data' => null,
                'error' => 'Error de conexión con la SET: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validar factura completa (RUC + Timbrado + Fecha + Monto)
     *
     * @param array $invoiceData ['ruc' => string, 'timbrado' => string, 'fecha' => string, 'monto' => float]
     * @return array ['valid' => bool, 'data' => array, 'errors' => array]
     */
    public function validateInvoice(array $invoiceData): array
    {
        $errors = [];
        $data = [];

        // Validar RUC
        $rucValidado = null;
        if (isset($invoiceData['ruc_emisor'])) {
            $rucValidation = $this->validateRuc($invoiceData['ruc_emisor']);
            $data['ruc_validation'] = $rucValidation;

            if (!$rucValidation['valid']) {
                $errors[] = $rucValidation['error'] ?? 'RUC inválido';
            } else {
                // Guardar el RUC validado (puede ser diferente al extraído si hubo fallback)
                $rucValidado = $rucValidation['data']['ruc'] ?? $invoiceData['ruc_emisor'];
            }
        } else {
            $errors[] = 'RUC del emisor no proporcionado';
        }

        // Validar Timbrado (usar RUC validado, no el extraído)
        if (isset($invoiceData['timbrado']) && $rucValidado) {
            $timbradoValidation = $this->validateTimbrado(
                $invoiceData['timbrado'],
                $rucValidado
            );
            $data['timbrado_validation'] = $timbradoValidation;

            if (!$timbradoValidation['valid']) {
                $errors[] = $timbradoValidation['error'] ?? 'Timbrado inválido';
            }
        } else {
            $errors[] = 'Timbrado no proporcionado';
        }

        // Validar fecha (formato YYYY-MM-DD)
        if (isset($invoiceData['fecha_emision'])) {
            try {
                $fecha = \Carbon\Carbon::parse($invoiceData['fecha_emision']);

                // Verificar que no sea una fecha futura
                if ($fecha->isFuture()) {
                    $errors[] = 'La fecha de emisión no puede ser futura';
                }

                // Verificar que no sea más antigua de 10 años
                if ($fecha->lt(now()->subYears(10))) {
                    $errors[] = 'La fecha de emisión es demasiado antigua';
                }

                $data['fecha_parsed'] = $fecha->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = 'Formato de fecha inválido. Use YYYY-MM-DD';
            }
        } else {
            $errors[] = 'Fecha de emisión no proporcionada';
        }

        // Validar monto
        if (isset($invoiceData['monto_total'])) {
            if (!is_numeric($invoiceData['monto_total']) || $invoiceData['monto_total'] <= 0) {
                $errors[] = 'El monto debe ser un número positivo';
            }
        } else {
            $errors[] = 'Monto total no proporcionado';
        }

        return [
            'valid' => empty($errors),
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Ejecutar llamada con reintentos exponenciales
     *
     * @param callable $callback Función a ejecutar
     * @return mixed
     */
    protected function callWithRetry(callable $callback)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;

                // No reintentar en errores de validación (solo en errores de red/servicio)
                if ($this->isValidationError($e)) {
                    throw $e;
                }

                if ($attempt < $this->maxRetries) {
                    // Backoff exponencial: 1s, 2s, 4s
                    $sleepTime = pow(2, $attempt - 1);

                    Log::warning('Reintentando llamada a DNIT', [
                        'attempt' => $attempt,
                        'max_retries' => $this->maxRetries,
                        'sleep_time' => $sleepTime,
                        'error' => $e->getMessage(),
                    ]);

                    sleep($sleepTime);
                }
            }
        }

        throw $lastException;
    }

    /**
     * Verificar si es un error de validación (no reintentable)
     */
    protected function isValidationError(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());

        // Errores que no deben reintentar
        $validationErrors = [
            'formato',
            'inválido',
            'no encontrado',
            'inexistente',
        ];

        foreach ($validationErrors as $errorType) {
            if (str_contains($message, $errorType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Consultar RUC desde base de datos local (datos oficiales de la SET)
     *
     * Este método consulta la base de datos local que contiene los datos
     * oficiales publicados por la SET Paraguay.
     *
     * Para actualizar los datos, ejecuta: php artisan ruc:download
     */
    protected function queryRucApi(string $ruc): array
    {
        // Para ambiente de desarrollo/testing con simulación
        if (config('app.env') === 'local' && config('services.dnit.simulate', false)) {
            Log::warning('Modo de desarrollo: validación simulada', ['ruc' => $ruc]);

            return [
                'valid' => true,
                'data' => [
                    'ruc' => $ruc,
                    'razon_social' => 'CONTRIBUYENTE DE PRUEBA',
                    'estado' => 'ACTIVO',
                    'simulated' => true,
                ],
                'error' => null,
            ];
        }

        // Limpiar RUC (quitar guiones, espacios, puntos)
        $rucLimpio = str_replace(['-', ' ', '.'], '', $ruc);

        try {
            // Consultar en base de datos local (datos oficiales de la SET)
            // Intentar buscar con el RUC completo primero
            $contribuyente = \DB::table('ruc_contribuyentes')
                ->where('ruc', $rucLimpio)
                ->first();

            // Si no encuentra y el RUC tiene más de 1 dígito,
            // intentar separando el último dígito (probablemente es el DV pegado)
            if (!$contribuyente && strlen($rucLimpio) > 1) {
                $rucSinUltimo = substr($rucLimpio, 0, -1);
                $contribuyente = \DB::table('ruc_contribuyentes')
                    ->where('ruc', $rucSinUltimo)
                    ->first();
            }

            if ($contribuyente) {
                return [
                    'valid' => true,
                    'data' => [
                        'ruc' => $contribuyente->ruc,
                        'razon_social' => $contribuyente->razon_social,
                        'estado' => $contribuyente->estado ?? 'ACTIVO',
                        'tipo_contribuyente' => $contribuyente->tipo_contribuyente ?? null,
                        'dv' => $contribuyente->dv ?? null,
                        'offline' => true, // Indica que es consulta offline
                    ],
                    'error' => null,
                ];
            }

            return [
                'valid' => false,
                'data' => null,
                'error' => 'RUC no encontrado en la base de datos local. Ejecuta "php artisan ruc:download" para actualizar.',
            ];

        } catch (\Exception $e) {
            Log::error('Error consultando RUC en base de datos local', [
                'ruc' => $ruc,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'data' => null,
                'error' => 'Error consultando RUC: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Consultar API de Timbrado de la SET
     */
    protected function queryTimbradoApi(string $timbrado, string $ruc): array
    {
        // Para ambiente de desarrollo/testing
        if (config('app.env') === 'local') {
            Log::warning('Modo de desarrollo: validación simulada', ['timbrado' => $timbrado, 'ruc' => $ruc]);

            return [
                'valid' => true,
                'data' => [
                    'timbrado' => $timbrado,
                    'ruc' => $ruc,
                    'vigente' => true,
                    'fecha_inicio' => now()->subMonths(3)->format('Y-m-d'),
                    'fecha_fin' => now()->addMonths(9)->format('Y-m-d'),
                    'simulated' => true,
                ],
                'error' => null,
            ];
        }

        // Implementación real
        // NOTA: Ajustar según endpoint real de la SET
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->get("https://servicios.set.gov.py/eset-publico/rest/timbrado/verificar", [
                'timbrado' => $timbrado,
                'ruc' => $ruc,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error al consultar Timbrado: ' . $response->status());
        }

        $data = $response->json();

        if (isset($data['vigente']) && $data['vigente']) {
            return [
                'valid' => true,
                'data' => [
                    'timbrado' => $timbrado,
                    'ruc' => $ruc,
                    'vigente' => true,
                    'fecha_inicio' => $data['fechaInicio'] ?? null,
                    'fecha_fin' => $data['fechaFin'] ?? null,
                ],
                'error' => null,
            ];
        }

        return [
            'valid' => false,
            'data' => null,
            'error' => 'Timbrado no vigente o no encontrado',
        ];
    }

    /**
     * Limpiar caché para un RUC específico
     */
    public function clearRucCache(string $ruc): void
    {
        $ruc = preg_replace('/[^0-9]/', '', $ruc);
        $cacheKey = "dnit:ruc:{$ruc}";
        Cache::forget($cacheKey);

        Log::info('Caché de RUC eliminado', ['ruc' => $ruc]);
    }

    /**
     * Limpiar caché para un Timbrado específico
     */
    public function clearTimbradoCache(string $timbrado, string $ruc): void
    {
        $timbrado = preg_replace('/[^0-9]/', '', $timbrado);
        $ruc = preg_replace('/[^0-9]/', '', $ruc);
        $cacheKey = "dnit:timbrado:{$ruc}:{$timbrado}";
        Cache::forget($cacheKey);

        Log::info('Caché de Timbrado eliminado', ['timbrado' => $timbrado, 'ruc' => $ruc]);
    }
}
