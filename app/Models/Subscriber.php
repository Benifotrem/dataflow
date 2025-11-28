<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'ip_address',
        'source',
        'interests',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'interests' => 'array',
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Métodos auxiliares
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function unsubscribe(): bool
    {
        $this->status = 'unsubscribed';
        $this->unsubscribed_at = now();
        return $this->save();
    }

    public function resubscribe(): bool
    {
        $this->status = 'active';
        $this->unsubscribed_at = null;
        return $this->save();
    }
}
