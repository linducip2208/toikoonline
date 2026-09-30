<?php

namespace App\Providers;

use App\Events\OrderDelivered;
use App\Events\OrderPaid;
use App\Events\OrderShipped;
use App\Events\StockLow;
use App\Listeners\NotifyStockLow;
use App\Listeners\SendOrderDeliveredNotifications;
use App\Listeners\SendOrderPaidNotifications;
use App\Listeners\SendOrderShippedNotifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Agent 3 event bridge. WIRING NEEDED: add
 *   App\Providers\EventBridgeServiceProvider::class,
 * to bootstrap/providers.php (integrator-owned, not edited here).
 */
class EventBridgeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OrderPaid::class, SendOrderPaidNotifications::class);
        Event::listen(OrderShipped::class, SendOrderShippedNotifications::class);
        Event::listen(OrderDelivered::class, SendOrderDeliveredNotifications::class);
        Event::listen(StockLow::class, NotifyStockLow::class);
    }
}
