<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'app_lang_code',
        'rtl',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rtl' => 'boolean',
            'status' => 'boolean',
        ];
    }
}
