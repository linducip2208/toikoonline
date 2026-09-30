<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    protected $fillable = ['shipment_id', 'order_detail_id', 'qty'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
