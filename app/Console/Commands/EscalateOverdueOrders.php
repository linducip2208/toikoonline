<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class EscalateOverdueOrders extends Command
{
    protected $signature = 'orders:escalate-overdue';
    protected $description = 'Auto-escalate orders that are past delivery estimate';

    public function handle(): void
    {
        $overdueOrders = Order::whereIn('delivery_status', ['confirmed', 'on_delivery'])
            ->where('created_at', '<', now()->subDays(3))
            ->get();

        if ($overdueOrders->isEmpty()) {
            $this->info('No overdue orders to escalate.');
            return;
        }

        foreach ($overdueOrders as $order) {
            $order->delivery_status = 'cancelled';
            $order->save();

            $this->line("Escalated order #{$order->code} (ID: {$order->id}) — delivery status set to cancelled.");
        }

        $this->info("Escalated {$overdueOrders->count()} overdue orders.");
    }
}
