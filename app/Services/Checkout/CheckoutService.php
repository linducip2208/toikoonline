<?php

namespace App\Services\Checkout;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Services\B2B\PriceListService;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public array $lastErrors = [];

    public function effectivePrice(Product $product, ?User $user = null, int $qty = 1, ?string $variation = null): int
    {
        // B2B/wholesale price wins when available (integer IDR).
        if ($user) {
            try {
                $listPrice = app(PriceListService::class)->priceFor($product, $variation, $user, $qty);
                if ($listPrice !== null) {
                    return (int) max(0, $listPrice);
                }
            } catch (\Throwable) {
                // Fall through to regular price - price lists are optional.
            }
        }

        $now = now()->timestamp;
        $hasDiscount = (float) $product->discount > 0
            && (empty($product->discount_start_date) || (int) $product->discount_start_date <= $now)
            && (empty($product->discount_end_date) || (int) $product->discount_end_date >= $now);

        if (! $hasDiscount) {
            return (int) $product->unit_price;
        }

        $eff = $product->discount_type === 'percent'
            ? (float) $product->unit_price * (1 - (float) $product->discount / 100)
            : (float) $product->unit_price - (float) $product->discount;

        return (int) max(0, round($eff));
    }

    public function expectedUnitPrice(Product $product, ?string $variation, ?User $user = null, int $qty = 1): int
    {
        if ($product->variant_product && $variation !== null && $variation !== '') {
            $stock = ProductStock::where('product_id', $product->id)
                ->where('variant', $variation)
                ->first();
            if ($stock && (float) $stock->price > 0) {
                $variantPrice = (int) $stock->price;
                // Company price list still wins over variant price.
                if ($user) {
                    try {
                        $listPrice = app(PriceListService::class)->priceFor($product, $variation, $user, $qty);
                        if ($listPrice !== null) {
                            return (int) max(0, $listPrice);
                        }
                    } catch (\Throwable) {
                    }
                }

                return $variantPrice;
            }
        }

        return $this->effectivePrice($product, $user, $qty, $variation);
    }

    public function availableQty(Product $product, ?string $variation): int
    {
        if ($product->digital) {
            return PHP_INT_MAX;
        }

        $query = ProductStock::where('product_id', $product->id);
        if ($product->variant_product && $variation !== null && $variation !== '') {
            $query->where('variant', $variation);
        }

        return (int) $query->sum('qty');
    }

    public function expectedTaxPerUnit(Product $product, int $unitPrice): int
    {
        if (($product->tax_type ?? 'amount') === 'percent') {
            return (int) round($unitPrice * (float) $product->tax / 100);
        }

        return (int) $product->tax;
    }

    /**
     * Active customer-group discount percent (max across user's groups).
     */
    public function groupDiscountPercent(?User $user): float
    {
        if (! $user) {
            return 0.0;
        }
        try {
            return (float) ($user->customerGroups()->where('customer_groups.is_active', true)->max('discount_percent') ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    /**
     * @param Collection $cartItems Cart models with product relation
     * @param array $address ['city'=>?, 'postal_code'|'postcode'=>?] untuk mode pajak zona
     * @return array{subtotal:int,tax:int,weight:int,lines:array,group_discount:int}
     *
     * @throws ValidationException on price mismatch or insufficient stock
     */
    public function validate(Collection $cartItems, ?User $user = null, array $address = []): array
    {
        $errors = [];
        $subtotal = 0;
        $tax = 0;
        $weight = 0;
        $lines = [];

        foreach ($cartItems as $item) {
            $product = $item->product instanceof Product ? $item->product : Product::find($item->product_id);

            if (! $product || ! $product->published) {
                $errors[] = __('commerce.checkout_product_unavailable', ['name' => $item->product->name ?? '#'.$item->product_id]);
                continue;
            }

            $qty = (int) $item->quantity;
            $expected = $this->expectedUnitPrice($product, $item->variation, $user, $qty);

            if ((int) $item->price !== $expected) {
                $errors[] = __('commerce.checkout_price_changed', ['name' => $product->name]);
                continue;
            }

            $available = $this->availableQty($product, $item->variation);
            if ((int) $item->quantity > $available) {
                $errors[] = __('commerce.checkout_stock_insufficient', ['name' => $product->name]);
                continue;
            }

            $lineTax = $this->expectedTaxPerUnit($product, $expected);
            $subtotal += $expected * $qty;
            $tax += $lineTax * $qty;
            $weight += (int) (($product->weight ?: 500) * $qty);

            $lines[] = [
                'product_id' => $product->id,
                'variation' => $item->variation,
                'unit_price' => $expected,
                'tax' => $lineTax,
                'quantity' => $qty,
            ];
        }

        if ($errors !== []) {
            $this->lastErrors = $errors;
            throw ValidationException::withMessages(['cart' => $errors]);
        }

        $groupDiscount = 0;
        $pct = $this->groupDiscountPercent($user);
        if ($pct > 0 && $subtotal > 0) {
            $groupDiscount = (int) min($subtotal, round($subtotal * $pct / 100));
        }

        // Mode pajak zona (opt-in via BusinessSetting tax_engine=zone):
        // pajak dihitung dari alamat + TaxRate, bukan per-produk.
        // Default 'product' = perilaku lama (aman untuk nominal gateway).
        try {
            if ($address !== [] && \App\Models\BusinessSetting::getValue('tax_engine', 'product') === 'zone'
                && class_exists(\App\Services\Tax\TaxService::class)) {
                $zone = app(\App\Services\Tax\TaxService::class)->computeTax($subtotal, [
                    'city' => $address['city'] ?? null,
                    'postcode' => $address['postal_code'] ?? $address['postal'] ?? null,
                ]);
                if (isset($zone['tax'])) {
                    $tax = (int) max(0, $zone['tax']);
                }
            }
        } catch (\Throwable) {
        }

        return [
            'subtotal' => (int) $subtotal,
            'tax' => (int) $tax,
            'weight' => (int) $weight,
            'lines' => $lines,
            'group_discount' => (int) $groupDiscount,
        ];
    }
}
