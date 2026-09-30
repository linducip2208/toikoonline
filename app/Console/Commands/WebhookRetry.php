<?php

namespace App\Console\Commands;

use App\Models\WebhookDelivery;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Console\Command;

class WebhookRetry extends Command
{
    protected $signature = 'webhooks:retry {--limit=50 : Max deliveries to retry}';

    protected $description = 'Retry failed outbound webhook deliveries with backoff';

    public function handle(): int
    {
        $due = WebhookDelivery::where('status', 'failed')
            ->where(function ($q) {
                $q->whereNull('next_retry_at')->orWhere('next_retry_at', '<=', now());
            })
            ->where('attempts', '<', 5)
            ->orderBy('next_retry_at')
            ->limit((int) $this->option('limit'))
            ->get();

        $ok = 0;
        foreach ($due as $delivery) {
            if (WebhookDispatcher::attempt($delivery)) {
                $ok++;
            }
        }

        $this->info("Retried {$due->count()} deliveries, {$ok} delivered.");

        return self::SUCCESS;
    }
}
