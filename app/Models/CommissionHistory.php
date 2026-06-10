<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionHistory extends Model
{
    protected $fillable = [
        'order_id',
        'order_detail_id',
        'seller_id',
        'admin_commission',
        'seller_earning',
    ];

    protected $casts = [
        'admin_commission' => 'decimal:2',
        'seller_earning' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
