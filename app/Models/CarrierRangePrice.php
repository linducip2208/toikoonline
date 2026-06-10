<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrierRangePrice extends Model
{
    protected $fillable = [
        'carrier_id',
        'zone_id',
        'weight_from',
        'weight_to',
        'price',
    ];

    protected $casts = [
        'weight_from' => 'decimal:2',
        'weight_to' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
