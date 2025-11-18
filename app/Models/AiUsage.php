<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    use HasFactory;

    protected $table = 'ai_usage';

    protected $fillable = [
        'tenant_id',
        'document_id',
        'year',
        'month',
        'documents_processed',
        'api_calls',
        'cost',
        'provider',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
