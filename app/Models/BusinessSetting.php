<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'type',
        'key',
        'value',
    ];

    public static function getValue(string $key, $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    protected static function booted(): void
    {
        // Keep the typed Settings service cache coherent when settings are
        // written directly through the model (admin pages, seeders).
        $flush = function (BusinessSetting $setting) {
            \App\Services\Settings\Settings::flush($setting->key);
        };
        static::saved($flush);
        static::deleted($flush);
    }
}
