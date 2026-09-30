<?php

namespace App\Console\Commands;

use App\Events\CartAbandoned;
use App\Models\Cart;
use Illuminate\Console\Command;

/**
 * Dispatch cart.abandoned events for inactive carts (default 24h).
 * Listeners notify the owner (database) + fire outbound webhooks +
 * run automation rules. Does NOT duplicate carts:abandoned (that command
 * only notifies); this one dispatches the event so all sides can act.
 * Safe to re-run: skips users notified in the last 7 days.
 */
class DispatchAbandonedCarts extends Command
{
    protected $signature = 'carts:abandoned-dispatch {--hours=24 : Inactivity hours before a cart counts as abandoned}';

    protected $description = 'Dispatch cart.abandoned events for inactive carts';

    public function handle(): int
    {
        $cutoff = now()->subHours((int) $this->option('hours'));

        $userIds = Cart::where('updated_at', '<', $cutoff)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        $count = 0;
        foreach ($userIds as $userId) {
            $items = Cart::where('user_id', $userId)->where('updated_at', '<', $cutoff)->count();
            if ($items === 0) {
                continue;
            }
            $user = \App\Models\User::find($userId);
            if (!$user) {
                continue;
            }
            $already = $user->notifications()
                ->whereIn('type', [
                    \App\Notifications\CartAbandonedNotice::class,
                    \App\Notifications\GenericAutomationNotice::class,
                ])
                ->where('data->event', 'cart.abandoned')
                ->where('created_at', '>', now()->subDays(7))
                ->exists();
            if ($already) {
                continue;
            }

            event(new CartAbandoned($user, $items));
            $count++;
        }

        $this->info("Dispatched cart.abandoned for {$count} users.");

        return self::SUCCESS;
    }
}
