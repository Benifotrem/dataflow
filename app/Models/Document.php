<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'entity_id',
        'user_id',
        'type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'ocr_status',
        'ocr_data',
        'amount',
        'currency',
        'document_date',
        'issuer',
        'recipient',
        'validated',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'ocr_data' => 'array',
        'document_date' => 'date',
        'validated' => 'boolean',
        'validated_at' => 'datetime',
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

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function isPending()
    {
        return $this->ocr_status === 'pending';
    }

    public function isProcessing()
    {
        return $this->ocr_status === 'processing';
    }

    public function isCompleted()
    {
        return $this->ocr_status === 'completed';
    }

    public function isValidated()
    {
        return $this->validated === true;
    }
}
