<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'name', 'country', 'state', 'city', 'postcode',
        'rate', 'inclusive', 'priority', 'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'inclusive' => 'boolean',
        'is_active' => 'boolean',
    ];
}
