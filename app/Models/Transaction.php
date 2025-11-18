<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'entity_id',
        'document_id',
        'type',
        'transaction_date',
        'description',
        'amount',
        'currency',
        'account_code',
        'category',
        'tax_amount',
        'tax_rate',
        'tax_type',
        'counterparty',
        'payment_method',
        'reference',
        'reconciled',
        'reconciled_at',
        'metadata',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
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

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function isReconciled()
    {
        return $this->reconciled === true;
    }

    public function isIncome()
    {
        return $this->type === 'income';
    }

    public function isExpense()
    {
        return $this->type === 'expense';
    }
}
