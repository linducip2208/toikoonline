<?php

namespace App\Listeners;

use App\Events\StockLow;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Stock-low notifier. Reads ProductStock qty vs Product low_stock_qty.
 * Stock mutation itself is owned by Agent 2 (InventoryService); this
 * listener only notifies and never writes stock. If the expected columns
 * are absent it logs and skips (documented in return notes).
 */
class NotifyStockLow
{
    public function handle(StockLow $event): void
    {
        try {
            if (!Schema::hasColumn('products', 'low_stock_qty')) {
                Log::info('StockLow skipped: products.low_stock_qty missing');
                return;
            }

            $threshold = (int) ($event->product->low_stock_qty ?? 0);
            if ($threshold > 0 && $event->qty <= $threshold) {
                Log::warning('Stock low', ['product' => $event->product->id, 'qty' => $event->qty]);
            }
        } catch (\Exception $e) {
            Log::info('StockLow skipped: ' . $e->getMessage());
        }
    }
}
