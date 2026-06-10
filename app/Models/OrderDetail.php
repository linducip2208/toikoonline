<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'seller_id',
        'product_id',
        'variation',
        'price',
        'coupon_discount',
        'tax',
        'shipping_cost',
        'quantity',
        'payment_status',
        'delivery_status',
        'refund_days',
        'reviewed',
        'shipping_type',
        'pickup_point_id',
        'product_referral_code',
        'gst_rate',
        'gst_amount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
            'reviewed' => 'boolean',
            'gst_rate' => 'decimal:2',
            'gst_amount' => 'decimal:2',
        ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
