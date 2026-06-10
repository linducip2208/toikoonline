<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'exchange_rate',
        'status',
        'code',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:5',
            'status' => 'boolean',
        ];
    }
}
