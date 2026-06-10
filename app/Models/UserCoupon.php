<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'coupon_code',
        'min_buy',
        'validation_days',
        'discount',
        'discount_type',
        'expiry_date',
    ];

    protected $casts = [
        'min_buy' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
