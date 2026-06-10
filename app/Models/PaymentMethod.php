<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'active',
        'addon_identifier',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
