<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order) {}

    public function payload(): array
    {
        return [
            'order_code' => $this->order->code,
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'total' => (int) round((float) $this->order->grand_total),
            'currency' => 'IDR',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
