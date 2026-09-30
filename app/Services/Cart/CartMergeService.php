<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Merge guest (session) cart lines into the authenticated user's DB cart.
 * Quantities are summed and capped at available stock; over-cap lines are
 * capped (never dropped silently below what fits... zero-fit lines skipped).
 *
 * @return array{merged:int, capped:int, skipped:int}
 */
class CartMergeService
{
    public function merge(array $sessionCart, User $user): array
    {
        $merged = 0;
        $capped = 0;
        $skipped = 0;

        DB::transaction(function () use ($sessionCart, $user, &$merged, &$capped, &$skipped) {
            foreach ($sessionCart as $line) {
                $productId = (int) ($line['product_id'] ?? 0);
                if ($productId <= 0) {
                    $skipped++;
                    continue;
                }
                $variation = $line['variation'] ?? null;
                $variation = $variation === '' ? null : $variation;
                $qty = max(1, (int) ($line['quantity'] ?? 1));

                $available = $this->availableStock($productId, $variation);

                $existing = Cart::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->where('variation', $variation)
                    ->first();

                $already = $existing ? (int) $existing->quantity : 0;
                $wanted = $already + $qty;
                $final = min($wanted, $available);

                if ($final <= 0) {
                    $skipped++;
                    continue;
                }
                if ($final < $wanted) {
                    $capped++;
                }

                if ($existing) {
                    $existing->update(['quantity' => $final]);
                } else {
                    Cart::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'variation' => $variation,
                        'price' => (int) round((float) ($line['price'] ?? 0)),
                        'tax' => (int) round((float) ($line['tax'] ?? 0)),
                        'shipping_cost' => (int) round((float) ($line['shipping_cost'] ?? 0)),
                        'quantity' => $final,
                        'owner_id' => $user->id,
                    ]);
                }
                $merged++;
            }
        });

        return ['merged' => $merged, 'capped' => $capped, 'skipped' => $skipped];
    }

    public function mergeSessionFor(User $user): array
    {
        $cart = session()->get('cart', []);
        if ($cart === []) {
            return ['merged' => 0, 'capped' => 0, 'skipped' => 0];
        }
        $result = $this->merge($cart, $user);
        session()->forget('cart');

        return $result;
    }

    protected function availableStock(int $productId, ?string $variation): int
    {
        $product = \App\Models\Product::find($productId);
        if (! $product) {
            return 0;
        }
        if ($product->digital) {
            return PHP_INT_MAX;
        }
        $query = ProductStock::where('product_id', $productId);
        if ($variation !== null && $variation !== '') {
            $query->where('variant', $variation);
        }

        return (int) $query->sum('qty');
    }
}
