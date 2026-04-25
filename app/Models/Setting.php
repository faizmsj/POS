<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory;

    protected static array $runtimeCache = [];

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public function getValueAttribute($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public static function valueOf(string $key, $default = null)
    {
        if (array_key_exists($key, static::$runtimeCache)) {
            return static::$runtimeCache[$key];
        }

        $setting = static::query()->where('key', $key)->first();
        $value = $setting?->value ?? $default;
        static::$runtimeCache[$key] = $value;

        return $value;
    }

    public static function assetUrl(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:image'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
