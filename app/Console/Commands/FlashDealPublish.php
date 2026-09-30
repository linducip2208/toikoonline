<?php

namespace App\Console\Commands;

use App\Models\FlashDeal;
use Illuminate\Console\Command;

/**
 * Auto-publish / unpublish flash deals by start_date / end_date.
 * Inspected columns: title, start_date, end_date, status(bool), featured.
 * Dates are unix timestamps (int) in this schema — compared as integers.
 */
class FlashDealPublish extends Command
{
    protected $signature = 'flash-deals:publish';

    protected $description = 'Auto toggle flash deal status by start/end timestamps';

    public function handle(): int
    {
        $now = now()->timestamp;
        $on = 0;
        $off = 0;

        foreach (FlashDeal::all() as $deal) {
            $start = (int) ($deal->start_date ?? 0);
            $end = (int) ($deal->end_date ?? 0);
            $shouldBeOn = ($start <= 0 || $now >= $start) && ($end <= 0 || $now <= $end);

            if ($shouldBeOn && !$deal->status) {
                $deal->update(['status' => true]);
                $on++;
            } elseif (!$shouldBeOn && $deal->status) {
                $deal->update(['status' => false]);
                $off++;
            }
        }

        $this->info("Flash deals: {$on} published, {$off} unpublished.");

        return self::SUCCESS;
    }
}
