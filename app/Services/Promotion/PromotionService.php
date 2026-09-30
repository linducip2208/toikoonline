<?php

namespace App\Services\Promotion;

use App\Models\Promotion;

/**
 * Auto-promotion rules engine. Pure + unit-testable: evaluate() takes plain
 * arrays, touches no checkout, performs no I/O.
 *
 * Line shape: ['product_id'=>int,'category_id'=>int|int[],'price'=>int IDR,'qty'=>int]
 *
 * Returns: ['line_discounts'=>[index=>int IDR],'free_shipping'=>bool,
 *   'shipping_discount'=>int,'total_discount'=>int,'applied'=>[names]]
 *
 * Composable by priority (lower first); discounts never exceed line totals,
 * grand total never negative (clamping is the integrator's job — see docs).
 */
class PromotionService
{
    /**
     * @param array<int,array{product_id?:int,category_id?:int|int[],price:int,qty:int}> $lines
     */
    public function evaluate(array $lines, ?int $userId, int $subtotal, ?array $rules = null): array
    {
        $rules ??= Promotion::active()->orderBy('priority')->get()->all();

        $lineDiscounts = array_fill(0, count($lines), 0);
        $applied = [];
        $freeShipping = false;
        $shippingDiscount = 0;

        foreach ($rules as $rule) {
            $type = is_array($rule) ? ($rule['type'] ?? '') : $rule->type;
            $config = is_array($rule) ? ($rule['config'] ?? []) : ($rule->config ?? []);
            $name = is_array($rule) ? ($rule['name'] ?? $type) : $rule->name;

            match ($type) {
                'auto_category_percent' => $this->applyCategoryPercent($lines, $lineDiscounts, (array) $config) ? $applied[] = $name : null,
                'auto_bogo' => $this->applyBogo($lines, $lineDiscounts, (array) $config) ? $applied[] = $name : null,
                'auto_tier' => $this->applyTier($lines, $lineDiscounts, $subtotal, (array) $config) ? $applied[] = $name : null,
                'auto_free_shipping' => $this->applyFreeShipping($subtotal, (array) $config, $freeShipping, $shippingDiscount) ? $applied[] = $name : null,
                default => null,
            };
        }

        // Clamp per-line: never more than line total.
        foreach ($lines as $i => $line) {
            $lineTotal = max(0, (int) ($line['price'] ?? 0)) * max(0, (int) ($line['qty'] ?? 0));
            $lineDiscounts[$i] = min(max(0, (int) $lineDiscounts[$i]), $lineTotal);
        }

        $totalDiscount = array_sum($lineDiscounts);

        return [
            'line_discounts' => $lineDiscounts,
            'free_shipping' => $freeShipping,
            'shipping_discount' => $shippingDiscount,
            'total_discount' => $totalDiscount,
            'applied' => array_values(array_unique($applied)),
        ];
    }

    /** config: {category_id:int|int[], percent:float, max_discount?:int} */
    protected function applyCategoryPercent(array $lines, array &$discounts, array $config): bool
    {
        $percent = (float) ($config['percent'] ?? 0);
        if ($percent <= 0) {
            return false;
        }
        $cats = (array) ($config['category_id'] ?? $config['category_ids'] ?? []);
        $cap = isset($config['max_discount']) ? (int) $config['max_discount'] : null;
        $hit = false;

        foreach ($lines as $i => $line) {
            $lineCats = (array) ($line['category_id'] ?? []);
            if ($cats && !array_intersect(array_map('strval', $cats), array_map('strval', $lineCats))) {
                continue;
            }
            $d = (int) round(((int) ($line['price'] ?? 0)) * $percent / 100) * max(0, (int) ($line['qty'] ?? 0));
            if ($cap !== null) {
                $d = min($d, $cap);
            }
            if ($d > 0) {
                $discounts[$i] += $d;
                $hit = true;
            }
        }

        return $hit;
    }

    /**
     * BOGO: cheapest-free per pair. config: {product_id?:int, category_id?:int|int[]}
     * Groups eligible units by price asc; every 2nd unit (cheapest of each pair) is free.
     */
    protected function applyBogo(array $lines, array &$discounts, array $config): bool
    {
        $units = [];
        foreach ($lines as $i => $line) {
            if (isset($config['product_id']) && (int) ($line['product_id'] ?? 0) !== (int) $config['product_id']) {
                continue;
            }
            if (isset($config['category_id'])) {
                $cats = (array) $config['category_id'];
                $lineCats = (array) ($line['category_id'] ?? []);
                if (!array_intersect(array_map('strval', $cats), array_map('strval', $lineCats))) {
                    continue;
                }
            }
            for ($k = 0; $k < max(0, (int) ($line['qty'] ?? 0)); $k++) {
                $units[] = ['line' => $i, 'price' => (int) ($line['price'] ?? 0)];
            }
        }
        if (count($units) < 2) {
            return false;
        }
        usort($units, fn ($a, $b) => $a['price'] <=> $b['price']);
        $freeCount = intdiv(count($units), 2);
        for ($k = 0; $k < $freeCount; $k++) {
            $discounts[$units[$k]['line']] += $units[$k]['price'];
        }

        return $freeCount > 0;
    }

    /**
     * Tiered subtotal discount. config: {tiers:[{min_subtotal:int, percent?:float, amount?:int}]}
     * Highest qualifying tier wins; percent applies to subtotal.
     */
    protected function applyTier(array $lines, array &$discounts, int $subtotal, array $config): bool
    {
        $best = null;
        foreach ((array) ($config['tiers'] ?? []) as $tier) {
            if ($subtotal >= (int) ($tier['min_subtotal'] ?? 0)) {
                $best = $tier;
            }
        }
        if (!$best) {
            return false;
        }
        $total = isset($best['amount'])
            ? (int) $best['amount']
            : (int) round($subtotal * ((float) ($best['percent'] ?? 0)) / 100);
        if (isset($best['max_discount'])) {
            $total = min($total, (int) $best['max_discount']);
        }
        if ($total <= 0 || $subtotal <= 0) {
            return false;
        }
        // Pro-rata across lines by line total.
        $lineTotals = [];
        foreach ($lines as $i => $line) {
            $lineTotals[$i] = max(0, (int) ($line['price'] ?? 0)) * max(0, (int) ($line['qty'] ?? 0));
        }
        $sum = array_sum($lineTotals);
        if ($sum <= 0) {
            return false;
        }
        $given = 0;
        foreach ($lineTotals as $i => $lt) {
            $share = (int) floor($total * $lt / $sum);
            $discounts[$i] += $share;
            $given += $share;
        }
        // Remainder to the largest line (never negative overall).
        if ($given < $total) {
            $big = array_search(max($lineTotals), $lineTotals, true);
            $discounts[$big] += $total - $given;
        }

        return true;
    }

    /** config: {min_subtotal:int, discount_amount?:int} — free shipping flag + optional cover. */
    protected function applyFreeShipping(int $subtotal, array $config, bool &$flag, int &$cover): bool
    {
        if ($subtotal < (int) ($config['min_subtotal'] ?? 0)) {
            return false;
        }
        $flag = true;
        $cover = max($cover, (int) ($config['discount_amount'] ?? 0));

        return true;
    }
}
