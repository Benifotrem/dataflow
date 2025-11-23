<?php

namespace App\Services;

class TrendingTopicsService
{
    /**
     * Temas fiscales y contables trending por país
     * Estos se actualizan manualmente basados en las tendencias estacionales
     * Ordenados alfabéticamente por código de país
     */
    protected $topics = [
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
        'bo' => [ // Bolivia
            'Declaración jurada impuestos Bolivia',
            'Factura electrónica SIN',
            'IVA mensual Bolivia',
            'IUE: impuesto sobre utilidades',
            'Libro de compras y ventas IVA',
            'RC-IVA retenciones',
            'IT: impuesto a las transacciones',
            'Régimen tributario simplificado',
            'NIT Bolivia: obtención',
            'Dosificación de facturas',
        ],
        'br' => [ // Brasil
            'Declaração de Imposto de Renda',
            'Nota Fiscal Eletrônica (NF-e)',
            'ICMS: cálculo e recolhimento',
            'Simples Nacional: guia completo',
            'SPED Fiscal e Contábil',
            'MEI: obrigações e benefícios',
            'Certificado Digital para empresas',
            'Escrituração Contábil Digital',
            'eSocial: folha de pagamento',
            'DARF: como emitir e pagar',
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
        'cr' => [ // Costa Rica
            'Declaración renta Costa Rica',
            'Factura electrónica Hacienda',
            'IVA trimestral Costa Rica',
            'Tributación digital CR',
            'Régimen tradicional vs simplificado',
            'Declaración D-101',
            'Retenciones en la fuente',
            'Libros contables electrónicos',
            'Impuesto sobre la renta personas físicas',
            'ATV: comprobantes electrónicos',
        ],
        'cu' => [ // Cuba
            'Declaración jurada de ingresos',
            'Impuesto por la utilización de la fuerza de trabajo',
            'Facturación en CUP y MLC',
            'Régimen tributario trabajadores por cuenta propia',
            'Contribución a la seguridad social',
            'Impuesto sobre ingresos personales',
            'Registro tributario ONAT',
            'Declaración jurada anual',
            'Régimen general vs simplificado',
            'Pago de impuestos en Cuba',
        ],
        'do' => [ // República Dominicana
            'Declaración jurada ISR',
            'Factura electrónica DGII',
            'ITBIS mensual República Dominicana',
            'Retenciones del ITBIS',
            'Régimen simplificado tributación',
            'NCF: numeración comprobantes fiscales',
            'Software de facturación aprobado',
            'TSS: seguridad social',
            'Declaración anual IR-2',
            'Calendario fiscal DGII',
        ],
        'ec' => [ // Ecuador
            'Declaración renta Ecuador',
            'Factura electrónica SRI',
            'IVA mensual Ecuador',
            'RISE: régimen simplificado',
            'Retenciones en la fuente',
            'Anexos transaccionales SRI',
            'RUC Ecuador: obtención',
            'Comprobantes electrónicos',
            'Impuesto a la renta sociedades',
            'Formularios SRI más usados',
        ],
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
        'gt' => [ // Guatemala
            'Declaración jurada ISR Guatemala',
            'Factura electrónica FEL',
            'IVA mensual Guatemala',
            'Régimen sobre utilidades',
            'RTU: Régimen de pequeño contribuyente',
            'Retenciones del IVA',
            'Libros contables SAT',
            'NIT Guatemala: inscripción',
            'Declaración anual SAT',
            'Régimen opcional simplificado',
        ],
        'hn' => [ // Honduras
            'Declaración renta Honduras',
            'Facturación electrónica SAR',
            'ISV: impuesto sobre ventas',
            'Régimen de facturación',
            'RTN: obtención y actualización',
            'Declaración mensual impuestos',
            'Retenciones ISR Honduras',
            'Declaración jurada anual',
            'Sistema de facturación electrónica',
            'Obligaciones tributarias mensuales',
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
        'ni' => [ // Nicaragua
            'Declaración mensual IR Nicaragua',
            'Factura electrónica DGI',
            'IVA Nicaragua: liquidación',
            'Retenciones en la fuente',
            'RUC Nicaragua: inscripción',
            'Declaración anual IR',
            'Régimen cuota fija',
            'Sistema de facturación',
            'Impuesto sobre la renta',
            'Calendario tributario DGI',
        ],
        'pa' => [ // Panamá
            'Declaración jurada renta Panamá',
            'Factura electrónica DGI',
            'ITBMS mensual Panamá',
            'RUC Panamá: obtención',
            'Declaración de operaciones',
            'Retenciones ITBMS',
            'Aviso de operaciones',
            'Régimen ordinario vs simplificado',
            'Sistema de facturación electrónica',
            'Impuesto sobre la renta',
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
        'pr' => [ // Puerto Rico
            'Planilla de contribución sobre ingresos',
            'IVU: impuesto sobre ventas',
            'Formulario 480.20',
            'Retención en el origen',
            'Declaración trimestral IVU',
            'Número de identificación patronal',
            'W-2PR: planilla informativa',
            'Contribución sobre ingresos',
            'Declaración de comerciante',
            'Hacienda Puerto Rico: calendario',
        ],
        'py' => [ // Paraguay - País por defecto
            'Declaración jurada IRP Paraguay',
            'Factura electrónica SET',
            'IVA mensual Paraguay',
            'IRE: impuesto a la renta empresarial',
            'Imagro: impuesto a las actividades agropecuarias',
            'RUC Paraguay: inscripción',
            'Marangatu: sistema tributario',
            'Libros contables electrónicos',
            'Régimen simplificado Paraguay',
            'Retenciones del IVA',
        ],
        'sv' => [ // El Salvador
            'Declaración renta El Salvador',
            'Comprobantes fiscales electrónicos',
            'IVA mensual El Salvador',
            'NIT El Salvador: obtención',
            'Pago a cuenta mensual',
            'Retenciones IVA e ISR',
            'Declaración anual ISR',
            'Sistema de facturación electrónica',
            'Régimen de incorporación fiscal',
            'Ministerio de Hacienda obligaciones',
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
        've' => [ // Venezuela
            'Declaración ISLR Venezuela',
            'Facturación electrónica SENIAT',
            'IVA mensual Venezuela',
            'RIF: registro de información fiscal',
            'Retenciones de IVA',
            'Declaración definitiva ISLR',
            'Libro de compras y ventas',
            'Retenciones de ISLR',
            'Régimen simplificado tributación',
            'Portal SENIAT: declaraciones',
        ],
    ];

    /**
     * País por defecto (Paraguay)
     */
    const DEFAULT_COUNTRY = 'py';

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
     * Obtener todos los países disponibles (ordenados alfabéticamente)
     */
    public function getAvailableCountries(): array
    {
        $countries = array_keys($this->topics);
        sort($countries);
        return $countries;
    }

    /**
     * Obtener siguiente país en orden alfabético para generación automática
     */
    public function getNextCountryInSequence(?string $lastCountry = null): string
    {
        $countries = $this->getAvailableCountries();

        if (!$lastCountry) {
            return $countries[0]; // Primer país alfabéticamente
        }

        $currentIndex = array_search($lastCountry, $countries);

        if ($currentIndex === false) {
            return $countries[0];
        }

        // Siguiente país, o volver al inicio si llegamos al final
        $nextIndex = ($currentIndex + 1) % count($countries);
        return $countries[$nextIndex];
    }

    /**
     * Obtener nombres de países en español
     */
    public function getCountryNames(): array
    {
        return [
            'ar' => 'Argentina',
            'bo' => 'Bolivia',
            'br' => 'Brasil',
            'cl' => 'Chile',
            'co' => 'Colombia',
            'cr' => 'Costa Rica',
            'cu' => 'Cuba',
            'do' => 'República Dominicana',
            'ec' => 'Ecuador',
            'es' => 'España',
            'gt' => 'Guatemala',
            'hn' => 'Honduras',
            'mx' => 'México',
            'ni' => 'Nicaragua',
            'pa' => 'Panamá',
            'pe' => 'Perú',
            'pr' => 'Puerto Rico',
            'py' => 'Paraguay',
            'sv' => 'El Salvador',
            'uy' => 'Uruguay',
            've' => 'Venezuela',
        ];
    }
}
