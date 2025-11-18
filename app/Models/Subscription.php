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
}
