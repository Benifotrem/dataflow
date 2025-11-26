<?php

namespace Database\Seeders;

use App\Models\FiscalEvent;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FiscalEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Determinar el país del tenant (por defecto Paraguay si no está configurado)
            $countryCode = $tenant->country_code ?? 'PY';

            $this->seedEventsForCountry($tenant->id, $countryCode);
        }
    }

    /**
     * Crear eventos fiscales por país
     */
    protected function seedEventsForCountry(int $tenantId, string $countryCode): void
    {
        $events = $this->getEventsByCountry($countryCode);
        $currentYear = now()->year;

        foreach ($events as $event) {
            // Crear el evento para el año actual
            FiscalEvent::create([
                'tenant_id' => $tenantId,
                'country_code' => $countryCode,
                'title' => $event['title'],
                'description' => $event['description'],
                'event_type' => $event['type'],
                'event_date' => "{$currentYear}-{$event['month']}-{$event['day']}",
                'notification_days_before' => $event['notify_days'] ?? 7,
                'is_recurring' => true,
                'is_active' => true,
                'is_default' => true,
            ]);
        }
    }

    /**
     * Obtener eventos por país
     */
    protected function getEventsByCountry(string $countryCode): array
    {
        return match($countryCode) {
            'PY' => $this->getParaguayEvents(),
            'ES' => $this->getSpainEvents(),
            'AR' => $this->getArgentinaEvents(),
            default => $this->getParaguayEvents(), // Por defecto Paraguay
        };
    }

    /**
     * Eventos fiscales de Paraguay
     */
    protected function getParaguayEvents(): array
    {
        return [
            // IVA Mensual (día 25 de cada mes)
            [
                'title' => 'Vencimiento IVA - Enero',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Diciembre',
                'type' => 'vat_liquidation',
                'month' => '01',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Febrero',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Enero',
                'type' => 'vat_liquidation',
                'month' => '02',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Marzo',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Febrero',
                'type' => 'vat_liquidation',
                'month' => '03',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Abril',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Marzo',
                'type' => 'vat_liquidation',
                'month' => '04',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Mayo',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Abril',
                'type' => 'vat_liquidation',
                'month' => '05',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Junio',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Mayo',
                'type' => 'vat_liquidation',
                'month' => '06',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Julio',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Junio',
                'type' => 'vat_liquidation',
                'month' => '07',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Agosto',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Julio',
                'type' => 'vat_liquidation',
                'month' => '08',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Septiembre',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Agosto',
                'type' => 'vat_liquidation',
                'month' => '09',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Octubre',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Septiembre',
                'type' => 'vat_liquidation',
                'month' => '10',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Noviembre',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Octubre',
                'type' => 'vat_liquidation',
                'month' => '11',
                'day' => '25',
                'notify_days' => 7,
            ],
            [
                'title' => 'Vencimiento IVA - Diciembre',
                'description' => 'Presentación y pago de declaración jurada del IVA correspondiente al mes de Noviembre',
                'type' => 'vat_liquidation',
                'month' => '12',
                'day' => '25',
                'notify_days' => 7,
            ],

            // IPS (Seguridad Social) - día 10 de cada mes
            [
                'title' => 'Vencimiento IPS',
                'description' => 'Pago de aportes al Instituto de Previsión Social',
                'type' => 'social_security',
                'month' => '01',
                'day' => '10',
                'notify_days' => 5,
            ],

            // Impuesto a la Renta Empresarial (IRE)
            [
                'title' => 'Vencimiento IRE - Cuota 1',
                'description' => 'Primera cuota del Impuesto a la Renta Empresarial',
                'type' => 'income_tax',
                'month' => '04',
                'day' => '30',
                'notify_days' => 15,
            ],
            [
                'title' => 'Vencimiento IRE - Cuota 2',
                'description' => 'Segunda cuota del Impuesto a la Renta Empresarial',
                'type' => 'income_tax',
                'month' => '07',
                'day' => '31',
                'notify_days' => 15,
            ],
            [
                'title' => 'Vencimiento IRE - Cuota 3',
                'description' => 'Tercera cuota del Impuesto a la Renta Empresarial',
                'type' => 'income_tax',
                'month' => '10',
                'day' => '31',
                'notify_days' => 15,
            ],
        ];
    }

    /**
     * Eventos fiscales de España
     */
    protected function getSpainEvents(): array
    {
        return [
            // IVA Trimestral
            [
                'title' => 'Modelo 303 - 1T',
                'description' => 'Declaración trimestral del IVA - Primer trimestre',
                'type' => 'quarterly_declaration',
                'month' => '04',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 303 - 2T',
                'description' => 'Declaración trimestral del IVA - Segundo trimestre',
                'type' => 'quarterly_declaration',
                'month' => '07',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 303 - 3T',
                'description' => 'Declaración trimestral del IVA - Tercer trimestre',
                'type' => 'quarterly_declaration',
                'month' => '10',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 303 - 4T',
                'description' => 'Declaración trimestral del IVA - Cuarto trimestre',
                'type' => 'quarterly_declaration',
                'month' => '01',
                'day' => '30',
                'notify_days' => 10,
            ],

            // Resumen Anual IVA
            [
                'title' => 'Modelo 390',
                'description' => 'Resumen anual del IVA',
                'type' => 'annual_accounts',
                'month' => '01',
                'day' => '30',
                'notify_days' => 15,
            ],

            // IRPF Trimestral
            [
                'title' => 'Modelo 130 - 1T',
                'description' => 'Pago fraccionado IRPF autónomos - Primer trimestre',
                'type' => 'quarterly_declaration',
                'month' => '04',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 130 - 2T',
                'description' => 'Pago fraccionado IRPF autónomos - Segundo trimestre',
                'type' => 'quarterly_declaration',
                'month' => '07',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 130 - 3T',
                'description' => 'Pago fraccionado IRPF autónomos - Tercer trimestre',
                'type' => 'quarterly_declaration',
                'month' => '10',
                'day' => '20',
                'notify_days' => 10,
            ],
            [
                'title' => 'Modelo 130 - 4T',
                'description' => 'Pago fraccionado IRPF autónomos - Cuarto trimestre',
                'type' => 'quarterly_declaration',
                'month' => '01',
                'day' => '30',
                'notify_days' => 10,
            ],

            // Renta Anual
            [
                'title' => 'Declaración de la Renta',
                'description' => 'Campaña de la Renta (IRPF) - Personas físicas',
                'type' => 'income_tax',
                'month' => '06',
                'day' => '30',
                'notify_days' => 30,
            ],
        ];
    }

    /**
     * Eventos fiscales de Argentina
     */
    protected function getArgentinaEvents(): array
    {
        return [
            // IVA Mensual
            [
                'title' => 'Vencimiento IVA',
                'description' => 'Declaración jurada y pago del Impuesto al Valor Agregado',
                'type' => 'vat_liquidation',
                'month' => '01',
                'day' => '20',
                'notify_days' => 7,
            ],

            // Ganancias - Anticipos
            [
                'title' => 'Anticipo Ganancias 1',
                'description' => 'Primer anticipo del Impuesto a las Ganancias',
                'type' => 'income_tax',
                'month' => '04',
                'day' => '15',
                'notify_days' => 10,
            ],
            [
                'title' => 'Anticipo Ganancias 2',
                'description' => 'Segundo anticipo del Impuesto a las Ganancias',
                'type' => 'income_tax',
                'month' => '06',
                'day' => '15',
                'notify_days' => 10,
            ],
            [
                'title' => 'Anticipo Ganancias 3',
                'description' => 'Tercer anticipo del Impuesto a las Ganancias',
                'type' => 'income_tax',
                'month' => '08',
                'day' => '15',
                'notify_days' => 10,
            ],
            [
                'title' => 'Anticipo Ganancias 4',
                'description' => 'Cuarto anticipo del Impuesto a las Ganancias',
                'type' => 'income_tax',
                'month' => '10',
                'day' => '15',
                'notify_days' => 10,
            ],
            [
                'title' => 'Anticipo Ganancias 5',
                'description' => 'Quinto anticipo del Impuesto a las Ganancias',
                'type' => 'income_tax',
                'month' => '12',
                'day' => '15',
                'notify_days' => 10,
            ],

            // Declaración Jurada Anual Ganancias
            [
                'title' => 'Declaración Jurada Ganancias',
                'description' => 'Presentación de la declaración jurada anual del Impuesto a las Ganancias',
                'type' => 'annual_accounts',
                'month' => '05',
                'day' => '31',
                'notify_days' => 20,
            ],

            // Seguridad Social
            [
                'title' => 'AFIP - Seguridad Social',
                'description' => 'Pago de aportes y contribuciones a la seguridad social',
                'type' => 'social_security',
                'month' => '01',
                'day' => '10',
                'notify_days' => 5,
            ],
        ];
    }
}
