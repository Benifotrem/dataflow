<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'telegram_id',
        'telegram_username',
        'telegram_chat_id',
        'telegram_linked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'telegram_linked_at' => 'datetime',
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

    // Métodos de Telegram
    public function hasTelegramLinked()
    {
        return !is_null($this->telegram_id);
    }

    public function linkTelegram($telegramId, $username, $chatId)
    {
        $this->update([
            'telegram_id' => $telegramId,
            'telegram_username' => $username,
            'telegram_chat_id' => $chatId,
            'telegram_linked_at' => now(),
        ]);
    }

    public function unlinkTelegram()
    {
        $this->update([
            'telegram_id' => null,
            'telegram_username' => null,
            'telegram_chat_id' => null,
            'telegram_linked_at' => null,
        ]);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
