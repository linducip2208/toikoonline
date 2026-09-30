<?php

namespace App\Listeners;

use App\Events\CartAbandoned;
use App\Events\OrderCreated;
use App\Events\PaymentFailed;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\RefundCreated;
use App\Events\ReviewCreated;
use App\Mail\OrderMail;
use App\Notifications\CartAbandonedNotice;
use App\Notifications\GenericAutomationNotice;
use App\Notifications\OrderStatusNotification;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notify/mail/outbound-webhook for the new Agent-3 events.
 * Order mails reuse OrderMail template lookup (falls back to plain body
 * when no EmailTemplate row matches). Never throws.
 */
class NotifyLifecycleEvents
{
    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof OrderCreated => $this->orderMail($event->order, 'order.created', 'order.created'),
                $event instanceof PaymentFailed => $this->orderMail($event->order, 'payment.failed', 'payment.failed'),
                $event instanceof RefundCreated => $this->refund($event),
                $event instanceof CartAbandoned => $this->cart($event),
                $event instanceof ReviewCreated => $this->review($event),
                $event instanceof ProductCreated => $this->product($event, 'product.created'),
                $event instanceof ProductUpdated => $this->product($event, 'product.updated'),
                default => null,
            };
        } catch (\Exception $e) {
            Log::warning('NotifyLifecycleEvents failed (non-fatal)', ['error' => $e->getMessage()]);
        }
    }

    protected function orderMail(\App\Models\Order $order, string $event, string $webhook): void
    {
        $order->loadMissing('user');
        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, $event));
            if ($order->user->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderMail($order, $event));
                } catch (\Exception) {
                }
            }
        }
        WebhookDispatcher::dispatch($webhook, $this->orderPayload($event, $order));
    }

    protected function orderPayload(string $event, \App\Models\Order $order): array
    {
        $class = $event === 'order.created'
            ? \App\Events\OrderCreated::class
            : \App\Events\PaymentFailed::class;

        return (new $class($order))->payload();
    }

    protected function refund(RefundCreated $event): void
    {
        $payload = $event->payload();
        $order = $event->subject instanceof \App\Models\Order ? $event->subject : null;
        $user = $order?->user;
        if ($user) {
            $user->notify(new GenericAutomationNotice(
                'Refund issued',
                'Refund of Rp' . number_format($payload['amount'], 0, ',', '.') . ' for order ' . $payload['order_code'] . '.',
                ['event' => 'refund.created'] + $payload
            ));
        }
        WebhookDispatcher::dispatch('refund.created', $payload);
    }

    protected function cart(CartAbandoned $event): void
    {
        $event->user->notify(new CartAbandonedNotice($event->itemCount));
        WebhookDispatcher::dispatch('cart.abandoned', $event->payload());
    }

    protected function review(ReviewCreated $event): void
    {
        WebhookDispatcher::dispatch('review.created', $event->payload());
    }

    protected function product(object $event, string $name): void
    {
        WebhookDispatcher::dispatch($name, $event->payload());
    }
}
