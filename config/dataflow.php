<?php

return [
    // Límites del sistema
    'document_limit_base' => env('DOCUMENT_LIMIT_BASE', 500),
    'data_retention_days' => env('DATA_RETENTION_DAYS', 60),
    
    // Precios
    'plan_basic_price' => 19.99,
    'plan_advanced_price' => 49.99,
    'addon_500_docs_price' => 9.99,
    
    // IA
    'ai_provider' => env('AI_PROVIDER', 'openai'),
    'ai_model' => env('AI_MODEL', 'gpt-4o-mini'),
    
    // Países soportados
    'supported_countries' => [
        'ES' => ['name' => 'España', 'currency' => 'EUR', 'vat_rate' => 21],
        'MX' => ['name' => 'México', 'currency' => 'MXN', 'vat_rate' => 16],
        'AR' => ['name' => 'Argentina', 'currency' => 'ARS', 'vat_rate' => 21],
        'CO' => ['name' => 'Colombia', 'currency' => 'COP', 'vat_rate' => 19],
        'CL' => ['name' => 'Chile', 'currency' => 'CLP', 'vat_rate' => 19],
        'PE' => ['name' => 'Perú', 'currency' => 'PEN', 'vat_rate' => 18],
        'VE' => ['name' => 'Venezuela', 'currency' => 'VES', 'vat_rate' => 16],
        'EC' => ['name' => 'Ecuador', 'currency' => 'USD', 'vat_rate' => 12],
        'GT' => ['name' => 'Guatemala', 'currency' => 'GTQ', 'vat_rate' => 12],
        'CU' => ['name' => 'Cuba', 'currency' => 'CUP', 'vat_rate' => 0],
        'BO' => ['name' => 'Bolivia', 'currency' => 'BOB', 'vat_rate' => 13],
        'DO' => ['name' => 'República Dominicana', 'currency' => 'DOP', 'vat_rate' => 18],
        'HN' => ['name' => 'Honduras', 'currency' => 'HNL', 'vat_rate' => 15],
        'PY' => ['name' => 'Paraguay', 'currency' => 'PYG', 'vat_rate' => 10],
        'SV' => ['name' => 'El Salvador', 'currency' => 'USD', 'vat_rate' => 13],
        'NI' => ['name' => 'Nicaragua', 'currency' => 'NIO', 'vat_rate' => 15],
        'CR' => ['name' => 'Costa Rica', 'currency' => 'CRC', 'vat_rate' => 13],
        'PA' => ['name' => 'Panamá', 'currency' => 'PAB', 'vat_rate' => 7],
        'UY' => ['name' => 'Uruguay', 'currency' => 'UYU', 'vat_rate' => 22],
    ],
];
