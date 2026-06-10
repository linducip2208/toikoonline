<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'seller_id',
        'shipping_address',
        'billing_address',
        'additional_info',
        'shipping_method',
        'delivery_status',
        'payment_type',
        'payment_status',
        'payment_details',
        'code',
        'date',
        'coupon_discount',
        'discount',
        'discount_type',
        'grand_total',
        'coupon_code',
        'tax_amount',
        'commission_calculated',
        'manual_payment',
        'manual_payment_data',
        'view',
        'delivery_viewed',
        'payment_status_viewed',
        'assign_delivery_boy',
        'pickup_point_id',
        'carrier_id',
        'order_from',
    ];

    protected $casts = [
        'coupon_discount' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'commission_calculated' => 'boolean',
        'manual_payment' => 'boolean',
        'view' => 'boolean',
        'delivery_viewed' => 'boolean',
        'payment_status_viewed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function refundRequests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function pickupPoint()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'assign_delivery_boy');
    }

    public function affiliateLog()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function clubPoint()
    {
        return $this->hasMany(ClubPoint::class);
    }

    public function deliveryHistories()
    {
        return $this->hasMany(DeliveryHistory::class);
    }

    public function commissionHistory()
    {
        return $this->hasOne(CommissionHistory::class);
    }
}
