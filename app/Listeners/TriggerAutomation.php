<?php

namespace App\Listeners;

use App\Services\Automation\AutomationRunner;
use Illuminate\Support\Facades\Log;

/**
 * Generic automation trigger. Registered for every Agent-3 event;
 * delegates to AutomationRunner (per-rule try/catch, never throws).
 */
class TriggerAutomation
{
    public function handle(object $event): void
    {
        try {
            $name = method_exists($event, 'payload') ? $this->eventName($event) : null;
            if ($name === null) {
                return;
            }
            $subject = $this->subject($event);
            app(AutomationRunner::class)->handle($name, $event->payload(), $subject);
        } catch (\Exception $e) {
            Log::warning('TriggerAutomation failed (non-fatal)', ['error' => $e->getMessage()]);
        }
    }

    protected function eventName(object $event): ?string
    {
        return match (get_class($event)) {
            \App\Events\OrderCreated::class => 'order.created',
            \App\Events\OrderPaid::class => 'order.paid',
            \App\Events\OrderShipped::class => 'order.shipped',
            \App\Events\OrderDelivered::class => 'order.delivered',
            \App\Events\PaymentFailed::class => 'payment.failed',
            \App\Events\RefundCreated::class => 'refund.created',
            \App\Events\StockLow::class => 'stock.low',
            \App\Events\CartAbandoned::class => 'cart.abandoned',
            \App\Events\ReviewCreated::class => 'review.created',
            \App\Events\ProductCreated::class => 'product.created',
            \App\Events\ProductUpdated::class => 'product.updated',
            default => null,
        };
    }

    protected function subject(object $event): mixed
    {
        foreach (['order', 'user', 'review', 'product', 'subject'] as $prop) {
            if (isset($event->{$prop})) {
                return $event->{$prop};
            }
        }

        return null;
    }
}
