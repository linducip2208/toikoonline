<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderShipped
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order, public ?string $courier = null, public ?string $waybill = null) {}

    public function payload(): array
    {
        return [
            'order_code' => $this->order->code,
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'courier' => $this->courier ?? $this->order->courier,
            'waybill' => $this->waybill ?? $this->order->tracking_number,
            'shipped_at' => now()->toIso8601String(),
        ];
    }
}
