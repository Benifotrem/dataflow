<?php

namespace App\Services;

class TrendingTopicsService
{
    /**
     * Temas fiscales y contables trending por país
     * Estos se actualizan manualmente basados en las tendencias estacionales
     */
    protected $topics = [
        'es' => [ // España
            'Declaración de la renta 2024',
            'IVA trimestral autónomos',
            'Modelo 303: cómo presentarlo',
            'Deducciones fiscales para autónomos',
            'Facturación electrónica obligatoria',
            'Nuevo sistema SII de la AEAT',
            'Gastos deducibles autónomos',
            'Retenciones IRPF: guía completa',
            'Calendario fiscal España 2024',
            'Declaración trimestral IVA',
        ],
        'mx' => [ // México
            'Declaración anual SAT 2024',
            'Factura electrónica CFDI 4.0',
            'Régimen Simplificado de Confianza',
            'Deducciones personales México',
            'IVA: cálculo y presentación',
            'Contabilidad electrónica SAT',
            'Nómina digital CFDI',
            'ISR personas físicas',
            'Carta porte SAT requisitos',
            'Declaraciones mensuales México',
        ],
        'ar' => [ // Argentina
            'Monotributo: categorías 2024',
            'Factura electrónica AFIP',
            'Ganancias: deducciones permitidas',
            'IVA: régimen de percepción',
            'Liquidación de sueldos',
            'Responsable inscripto: obligaciones',
            'Bienes personales declaración',
            'Retenciones y percepciones',
            'Libro IVA digital',
            'DDJJ anuales AFIP',
        ],
        'co' => [ // Colombia
            'Declaración de renta Colombia',
            'Factura electrónica DIAN',
            'RUT: actualización y consulta',
            'IVA bimestral Colombia',
            'Retención en la fuente',
            'Régimen simple de tributación',
            'Exógena DIAN 2024',
            'Medios de pago bancarizados',
            'Nómina electrónica Colombia',
            'Calendario tributario DIAN',
        ],
        'cl' => [ // Chile
            'Operación Renta Chile 2024',
            'Factura electrónica SII',
            'Declaración mensual IVA',
            'Boletas electrónicas Chile',
            'F29: cómo llenar correctamente',
            'Gastos rechazados tributarios',
            'Renta atribuida vs semi-integrado',
            'Retiros de utilidades',
            'Libro de compras y ventas',
            'AT: declaración y pago',
        ],
        'pe' => [ // Perú
            'Declaración anual renta Perú',
            'Comprobantes electrónicos SUNAT',
            'IGV: cálculo mensual',
            'RUC: obtención y consulta',
            'Libros electrónicos PLE',
            'Retenciones del IGV',
            'Detracciones SPOT',
            'Régimen MYPE Tributario',
            'PDT 621: guía de uso',
            'Calendario de vencimientos SUNAT',
        ],
        'uy' => [ // Uruguay
            'Declaración jurada DGI',
            'Factura electrónica Uruguay',
            'IVA mensual Uruguay',
            'IRAE: impuesto a la renta',
            'Monotributo Uruguay',
            'BPS: liquidación de aportes',
            'Retenciones de IVA',
            'Ajuste por inflación fiscal',
            'Declaración jurada IP',
            'CFE: comprobantes fiscales',
        ],
    ];

    /**
     * Palabras clave relacionadas con cada tema
     */
    protected $keywords = [
        'fiscal' => ['impuestos', 'declaración', 'tributación', 'deducción', 'retención'],
        'contable' => ['contabilidad', 'asientos', 'balance', 'libro diario', 'estados financieros'],
        'iva' => ['IVA', 'impuesto al valor agregado', 'crédito fiscal', 'débito fiscal'],
        'renta' => ['impuesto a la renta', 'IRPF', 'ISR', 'ganancias'],
        'autonomo' => ['autónomo', 'freelance', 'trabajador independiente', 'monotributo'],
        'facturacion' => ['factura electrónica', 'facturación', 'comprobante', 'CFDI'],
        'pyme' => ['PyME', 'pequeña empresa', 'emprendedor', 'negocio'],
    ];

    /**
     * Obtener temas trending para un país
     */
    public function getTopicsForCountry(string $countryCode, int $limit = 10): array
    {
        $countryCode = strtolower($countryCode);

        // Si no hay temas específicos para el país, usar temas genéricos
        if (!isset($this->topics[$countryCode])) {
            return $this->getGenericTopics($limit);
        }

        $topics = $this->topics[$countryCode];

        // Mezclar aleatoriamente
        shuffle($topics);

        // Limitar cantidad
        return array_slice($topics, 0, $limit);
    }

    /**
     * Obtener un tema aleatorio para un país
     */
    public function getRandomTopic(string $countryCode): string
    {
        $topics = $this->getTopicsForCountry($countryCode, 100);
        return $topics[array_rand($topics)];
    }

    /**
     * Obtener palabras clave sugeridas para un tema
     */
    public function getKeywordsForTopic(string $topic): array
    {
        $keywords = [];
        $topicLower = strtolower($topic);

        // Buscar palabras clave relacionadas
        foreach ($this->keywords as $category => $words) {
            foreach ($words as $word) {
                if (stripos($topicLower, $word) !== false) {
                    $keywords = array_merge($keywords, $words);
                    break;
                }
            }
        }

        // Agregar palabras clave genéricas
        $keywords = array_merge($keywords, [
            'contabilidad',
            'software contable',
            'automatización',
            'gestión fiscal',
        ]);

        // Eliminar duplicados
        $keywords = array_unique($keywords);

        // Limitar a 8 keywords
        return array_slice($keywords, 0, 8);
    }

    /**
     * Obtener temas genéricos
     */
    protected function getGenericTopics(int $limit): array
    {
        $generic = [
            'Cómo optimizar la declaración de impuestos',
            'Facturación electrónica: ventajas y requisitos',
            'Gestión contable para PyMEs',
            'Deducciones fiscales que no conocías',
            'Software de contabilidad: cómo elegir',
            'Automatización de procesos contables',
            'IVA: errores comunes a evitar',
            'Cierre contable anual: checklist',
            'Digitalización de documentos fiscales',
            'Conciliación bancaria: guía práctica',
        ];

        shuffle($generic);
        return array_slice($generic, 0, $limit);
    }

    /**
     * Obtener todos los países disponibles
     */
    public function getAvailableCountries(): array
    {
        return array_keys($this->topics);
    }
}
