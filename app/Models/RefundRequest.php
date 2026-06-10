<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'order_detail_id',
        'refund_status',
        'refund_label',
        'refund_reason',
        'refund_amount',
        'reject_reason',
        'admin_seen',
        'refund_staff_id',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'admin_seen' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function refundStaff()
    {
        return $this->belongsTo(User::class, 'refund_staff_id');
    }
}
