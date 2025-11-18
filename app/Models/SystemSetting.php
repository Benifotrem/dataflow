<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        if ($setting->type === 'encrypted') {
            return $setting->value ? Crypt::decryptString($setting->value) : $default;
        }

        return $setting->value ?? $default;
    }

    public static function set($key, $value, $type = 'string')
    {
        if ($type === 'encrypted' && $value) {
            $value = Crypt::encryptString($value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
