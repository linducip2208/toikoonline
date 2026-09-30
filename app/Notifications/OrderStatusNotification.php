<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $event = 'order.paid') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->event,
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'total' => (int) round((float) $this->order->grand_total),
            'message' => match ($this->event) {
                'order.shipped' => 'Order ' . $this->order->code . ' has been shipped.',
                'order.delivered' => 'Order ' . $this->order->code . ' has been delivered.',
                'order.created' => 'Order ' . $this->order->code . ' has been created.',
                'payment.failed' => 'Payment failed for order ' . $this->order->code . '.',
                default => 'Payment received for order ' . $this->order->code . '.',
            },
        ];
    }
}
