<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public mixed $subject, public int $amount = 0) {}

    public function payload(): array
    {
        $order = $this->subject instanceof \App\Models\Order ? $this->subject : null;

        return [
            'order_code' => $order?->code ?? (string) ($this->subject->order_code ?? ''),
            'order_id' => $order?->id ?? (int) ($this->subject->order_id ?? 0),
            'user_id' => $order?->user_id ?? (int) ($this->subject->order->user_id ?? 0),
            'amount' => (int) $this->amount,
            'currency' => 'IDR',
            'refunded_at' => now()->toIso8601String(),
        ];
    }
}
