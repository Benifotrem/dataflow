<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'slug',
        'type',
        'country_code',
        'currency_code',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    // Boot method para generar slug automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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

    public function aiUsage()
    {
        return $this->hasMany(AiUsage::class);
    }

    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    // Métodos de negocio
    public function isB2C()
    {
        return $this->type === 'b2c';
    }

    public function isB2B()
    {
        return $this->type === 'b2b';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->first();
    }

    public function getCurrentMonthDocumentCount()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        $usage = $this->aiUsage()
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        return $usage ? $usage->documents_processed : 0;
    }

    public function hasExceededDocumentLimit()
    {
        $currentCount = $this->getCurrentMonthDocumentCount();
        $subscription = $this->activeSubscription();

        if (!$subscription) {
            return true;
        }

        $limit = $subscription->document_limit;

        // Agregar documentos de addons activos
        $addonDocuments = $this->addons()
            ->where('year', date('Y'))
            ->where('month', date('n'))
            ->where('status', 'active')
            ->sum('document_quantity');

        $totalLimit = $limit + $addonDocuments;

        return $currentCount >= $totalLimit;
    }
}
