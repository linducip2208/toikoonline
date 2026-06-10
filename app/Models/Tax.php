<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'tax_status',
    ];

    protected function casts(): array
    {
        return [
            'tax_status' => 'boolean',
        ];
    }
}
