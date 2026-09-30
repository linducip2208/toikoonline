<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Mail\OrderMail;
use App\Notifications\OrderStatusNotification;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Support\Facades\Mail;

class SendOrderPaidNotifications
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, 'order.paid'));

            if ($order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderMail($order, 'order.paid'));
                } catch (\Exception) {
                }
            }
        }

        WebhookDispatcher::dispatch('order.paid', $event->payload());
    }
}
