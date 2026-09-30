<?php

namespace App\Console\Commands;

use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Console\Command;

class ReconcilePayments extends Command
{
    protected $signature = 'payments:reconcile {--limit=100 : Max pending intents to check}';

    protected $description = 'Pull gateway statuses for pending payment intents';

    public function handle(PaymentGatewayService $service): int
    {
        $pending = PaymentTransaction::whereIn('status', ['intent', 'pending'])
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at')
            ->limit((int) $this->option('limit'))
            ->get();

        $synced = 0;
        foreach ($pending as $txn) {
            $result = $service->reconcileTransaction($txn);
            if ($result['success'] ?? false) {
                $synced++;
            }
        }

        $this->info("Checked {$pending->count()} intents, synced {$synced}.");

        return self::SUCCESS;
    }
}
