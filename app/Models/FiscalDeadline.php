<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalDeadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'title',
        'description',
        'due_date',
        'due_time',
        'type',
        'frequency',
        'country_code',
        'status',
        'completed_at',
        'send_reminder',
        'reminder_days_before',
        'metadata',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'send_reminder' => 'boolean',
        'metadata' => 'array',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }
}
