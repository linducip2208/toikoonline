<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingBoxSize extends Model
{
    protected $fillable = [
        'courier_type',
        'user_id',
        'length',
        'breadth',
        'height',
    ];
}
