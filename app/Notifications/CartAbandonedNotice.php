<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CartAbandonedNotice extends Notification
{
    use Queueable;

    public function __construct(public int $itemCount) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'cart.abandoned',
            'items' => $this->itemCount,
            'message' => "You still have {$this->itemCount} item(s) in your cart.",
        ];
    }
}
