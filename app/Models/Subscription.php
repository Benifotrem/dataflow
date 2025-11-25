<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan',
        'price',
        'payment_link',
        'payment_status',
        'payment_transaction_id',
        'payment_completed_at',
        'payment_notified_at',
        'payment_metadata',
        'document_limit',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'payment_notified_at' => 'datetime',
        'payment_metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isBasic()
    {
        return $this->plan === 'basic';
    }

    public function isAdvanced()
    {
        return $this->plan === 'advanced';
    }

    // Métodos de pago
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPendingPayment()
    {
        return $this->payment_status === 'pending';
    }

    public function isPaymentCompleted()
    {
        return $this->payment_status === 'completed';
    }

    public function hasPaymentLink()
    {
        return !is_null($this->payment_link);
    }

    public function needsPayment()
    {
        return ($this->isExpired() || $this->status === 'expired') && !$this->isPaymentCompleted();
    }

    public function generatePaymentReference()
    {
        return 'SUB-' . $this->id . '-' . strtoupper(substr(md5($this->id . time()), 0, 8));
    }
}
