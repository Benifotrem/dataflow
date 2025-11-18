<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankStatement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'entity_id',
        'user_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'statement_date',
        'period_start',
        'period_end',
        'bank_name',
        'account_number',
        'status',
        'retention_expires_at',
        'file_deleted',
        'file_deleted_at',
        'metadata',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'retention_expires_at' => 'datetime',
        'file_deleted' => 'boolean',
        'file_deleted_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->retention_expires_at && Carbon::now()->isAfter($this->retention_expires_at);
    }

    public function isFileDeleted()
    {
        return $this->file_deleted === true;
    }

    public function calculateRetentionExpiry()
    {
        $endOfMonth = Carbon::parse($this->statement_date)->endOfMonth();
        return $endOfMonth->addDays(config('contaplus.data_retention_days', 60));
    }
}
