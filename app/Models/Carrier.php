<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'active',
        'addon_identifier',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function logoFile()
    {
        return $this->belongsTo(Upload::class, 'logo');
    }

    public function rangePrices()
    {
        return $this->hasMany(CarrierRangePrice::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
