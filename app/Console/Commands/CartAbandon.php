<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Notifications\CartAbandonedNotice;
use App\Notifications\OrderStatusNotification;
use Illuminate\Console\Command;

/**
 * Flag abandoned carts (no update for 24h, has items, owner is a user)
 * and notify the owner once via database notification.
 * carts.updated_at exists, so detection is honest timestamp-based.
 */
class CartAbandon extends Command
{
    protected $signature = 'carts:abandoned {--hours=24 : Inactivity hours before a cart counts as abandoned}';

    protected $description = 'Flag and notify owners of abandoned carts';

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
                ->where('type', OrderStatusNotification::class)
                ->where('data->event', 'cart.abandoned')
                ->where('created_at', '>', now()->subDays(7))
                ->exists();

            if ($already) {
                continue;
            }

            $user->notify(new CartAbandonedNotice($items));
            $count++;
        }

        $this->info("Notified {$count} users about abandoned carts.");

        return self::SUCCESS;
    }
}
