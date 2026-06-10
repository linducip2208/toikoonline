<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'code',
        'details',
        'discount',
        'discount_type',
        'start_date',
        'end_date',
        'min_buy',
        'max_discount',
        'status',
    ];

    protected $casts = [
        'discount' => 'decimal:2',
        'min_buy' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
}
