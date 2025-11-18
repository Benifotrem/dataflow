<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Relaciones
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class);
    }

    // Métodos de negocio
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isAdvisor()
    {
        return $this->role === 'advisor';
    }

    public function isSuperAdmin()
    {
        return $this->is_admin === true;
    }

    public function canManageTenant()
    {
        return $this->isOwner() || $this->isSuperAdmin();
    }
}
