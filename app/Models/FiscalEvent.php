<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FiscalEvent extends Model
{
    protected $fillable = [
        'tenant_id',
        'country_code',
        'title',
        'description',
        'event_type',
        'event_date',
        'notification_days_before',
        'is_recurring',
        'is_active',
        'is_default',
        'last_notified_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    /**
     * Relación con Tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope para eventos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para eventos próximos
     */
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('event_date', '>=', now())
                    ->where('event_date', '<=', now()->addDays($days))
                    ->orderBy('event_date', 'asc');
    }

    /**
     * Scope para eventos que necesitan notificación
     */
    public function scopeNeedsNotification($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('last_notified_at')
                  ->orWhere('last_notified_at', '<', now()->subYear());
            })
            ->where('event_date', '>=', now())
            ->whereRaw('DATE_SUB(event_date, INTERVAL notification_days_before DAY) <= CURDATE()');
    }

    /**
     * Scope por país
     */
    public function scopeCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    /**
     * Scope por tipo de evento
     */
    public function scopeType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Obtener días restantes hasta el evento
     */
    public function getDaysUntilAttribute(): int
    {
        return now()->diffInDays($this->event_date, false);
    }

    /**
     * Verificar si el evento es hoy
     */
    public function getIstodayAttribute(): bool
    {
        return $this->event_date->isToday();
    }

    /**
     * Verificar si ya pasó
     */
    public function getIsPastAttribute(): bool
    {
        return $this->event_date->isPast();
    }

    /**
     * Obtener nombre legible del tipo de evento
     */
    public function getEventTypeNameAttribute(): string
    {
        return match($this->event_type) {
            'vat_liquidation' => 'Liquidación de IVA',
            'income_tax' => 'Impuesto a la Renta',
            'tax_declaration' => 'Declaración de Impuestos',
            'social_security' => 'Seguridad Social',
            'annual_accounts' => 'Cuentas Anuales',
            'quarterly_declaration' => 'Declaración Trimestral',
            'monthly_declaration' => 'Declaración Mensual',
            'custom' => 'Evento Personalizado',
            default => ucfirst($this->event_type),
        };
    }

    /**
     * Obtener color para el badge según tipo
     */
    public function getEventColorAttribute(): string
    {
        return match($this->event_type) {
            'vat_liquidation' => 'blue',
            'income_tax' => 'red',
            'tax_declaration' => 'purple',
            'social_security' => 'green',
            'annual_accounts' => 'indigo',
            'quarterly_declaration' => 'yellow',
            'monthly_declaration' => 'orange',
            'custom' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Marcar como notificado
     */
    public function markAsNotified(): void
    {
        $this->update(['last_notified_at' => now()]);
    }

    /**
     * Duplicar evento para el próximo año (si es recurrente)
     */
    public function duplicateForNextYear(): ?self
    {
        if (!$this->is_recurring || $this->is_past) {
            return null;
        }

        return self::create([
            'tenant_id' => $this->tenant_id,
            'country_code' => $this->country_code,
            'title' => $this->title,
            'description' => $this->description,
            'event_type' => $this->event_type,
            'event_date' => $this->event_date->addYear(),
            'notification_days_before' => $this->notification_days_before,
            'is_recurring' => true,
            'is_active' => true,
            'is_default' => $this->is_default,
        ]);
    }
}
