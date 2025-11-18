<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'document_quantity',
        'price',
        'year',
        'month',
        'status',
        'purchased_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
