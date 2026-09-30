<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Mail\OrderMail;
use App\Notifications\OrderStatusNotification;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedNotifications
{
    public function handle(OrderShipped $event): void
    {
        $order = $event->order;

        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order.shipped'));

            if ($order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderMail($order, 'order.shipped'));
                } catch (\Exception) {
                }
            }
        }

        WebhookDispatcher::dispatch('order.shipped', $event->payload());
    }
}
