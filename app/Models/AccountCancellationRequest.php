<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCancellationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'reasons',
        'other_reason',
        'feedback',
        'retention_offer',
        'accepted_offer',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'reasons' => 'array',
        'accepted_offer' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relación con usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRetained($query)
    {
        return $query->where('status', 'retained');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Motivos predefinidos de cancelación
     */
    public static function getAvailableReasons(): array
    {
        return [
            'too_expensive' => 'Es muy caro para mi presupuesto',
            'not_using' => 'No estoy usando el sistema lo suficiente',
            'missing_features' => 'Le faltan funcionalidades que necesito',
            'difficult_to_use' => 'Es difícil de usar',
            'found_alternative' => 'Encontré una mejor alternativa',
            'technical_issues' => 'He tenido problemas técnicos',
            'poor_support' => 'El soporte no ha sido bueno',
            'business_closed' => 'Cerré mi negocio',
            'other' => 'Otro motivo',
        ];
    }

    /**
     * Ofertas de retención disponibles según el motivo
     */
    public static function getRetentionOffers(array $reasons): array
    {
        $offers = [];

        if (in_array('too_expensive', $reasons)) {
            $offers[] = [
                'type' => 'discount_3_months',
                'title' => '🎁 50% de descuento por 3 meses',
                'description' => 'Te ofrecemos 3 meses con 50% de descuento para que sigas aprovechando Dataflow',
            ];
        }

        if (in_array('not_using', $reasons)) {
            $offers[] = [
                'type' => 'training_session',
                'title' => '📚 Sesión de capacitación gratuita',
                'description' => 'Te ayudamos a sacar el máximo provecho con una sesión personalizada de 1 hora',
            ];
        }

        if (in_array('missing_features', $reasons)) {
            $offers[] = [
                'type' => 'priority_features',
                'title' => '⭐ Prioridad en desarrollo de funcionalidades',
                'description' => 'Tu feedback será prioritario y trabajaremos en las funcionalidades que necesitas',
            ];
        }

        if (in_array('difficult_to_use', $reasons)) {
            $offers[] = [
                'type' => 'onboarding_help',
                'title' => '🤝 Asistencia personalizada',
                'description' => 'Te asignamos un asesor personal durante 2 semanas para ayudarte con cualquier duda',
            ];
        }

        if (in_array('technical_issues', $reasons)) {
            $offers[] = [
                'type' => 'priority_support',
                'title' => '🔧 Soporte técnico prioritario',
                'description' => 'Resolvemos tus problemas con máxima prioridad y te damos 1 mes gratis',
            ];
        }

        // Oferta genérica si no hay ninguna específica
        if (empty($offers)) {
            $offers[] = [
                'type' => 'discount_1_month',
                'title' => '🎁 1 mes gratis',
                'description' => 'Antes de irte, te regalamos 1 mes para que reconsideres tu decisión',
            ];
        }

        return $offers;
    }

    /**
     * Marcar como retenido
     */
    public function markAsRetained(string $offerAccepted): void
    {
        $this->update([
            'status' => 'retained',
            'retention_offer' => $offerAccepted,
            'accepted_offer' => true,
        ]);
    }

    /**
     * Marcar como cancelado
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
