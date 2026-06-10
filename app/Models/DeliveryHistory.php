<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    protected $fillable = [
        'order_id',
        'order_detail_id',
        'delivery_boy_id',
        'delivery_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function deliveryBoy()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id');
    }
}
