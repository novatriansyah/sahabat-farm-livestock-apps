<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmSetting extends Model
{
    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function getJson(string $key, $default = null): mixed
    {
        $value = self::get($key);
        return $value ? json_decode($value, true) : $default;
    }

    public static function set(string $key, $value, string $label = '', string $group = 'GENERAL'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'label' => $label ?: $key,
                'group' => $group,
            ]
        );
    }
}
