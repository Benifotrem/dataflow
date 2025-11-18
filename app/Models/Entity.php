<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'tax_id',
        'country_code',
        'currency_code',
        'fiscal_config',
        'chart_of_accounts',
        'status',
    ];

    protected $casts = [
        'fiscal_config' => 'array',
        'chart_of_accounts' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class);
    }

    public function fiscalDeadlines()
    {
        return $this->hasMany(FiscalDeadline::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getIcalendarUrl()
    {
        return route('icalendar.feed', ['entity' => $this->id, 'token' => md5($this->id . config('app.key'))]);
    }
}
