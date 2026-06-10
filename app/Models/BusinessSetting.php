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
}
