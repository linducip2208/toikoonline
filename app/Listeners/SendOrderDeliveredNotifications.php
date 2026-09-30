<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use App\Mail\OrderMail;
use App\Notifications\OrderStatusNotification;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Support\Facades\Mail;

class SendOrderDeliveredNotifications
{
    public function handle(OrderDelivered $event): void
    {
        $order = $event->order;

        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order.delivered'));

            if ($order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderMail($order, 'order.delivered'));
                } catch (\Exception) {
                }
            }
        }

        WebhookDispatcher::dispatch('order.delivered', $event->payload());
    }
}
