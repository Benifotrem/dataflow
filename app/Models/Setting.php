<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Obtener un setting por su key
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Guardar o actualizar un setting
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general')
    {
        $setting = static::firstOrNew(['key' => $key]);

        if ($type === 'encrypted' && $value !== null) {
            $value = Crypt::encryptString($value);
        } elseif ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        $setting->fill([
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);

        return $setting->save();
    }

    /**
     * Obtener todos los settings de un grupo
     */
    public static function getGroup(string $group): array
    {
        $settings = static::where('group', $group)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    /**
     * Convertir el valor según su tipo
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'encrypted' => Crypt::decryptString($value),
            'json' => json_decode($value, true),
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
    }

    /**
     * Verificar si un setting existe
     */
    public static function has(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Eliminar un setting
     */
    public static function remove(string $key): bool
    {
        return static::where('key', $key)->delete();
    }
}
