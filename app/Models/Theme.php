<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['key', 'name', 'version', 'settings', 'is_active'];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function setting(string $key, $default = null)
    {
        return data_get($this->settings ?? [], $key, $default);
    }
}
