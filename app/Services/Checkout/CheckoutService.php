<?php

namespace App\Services\Checkout;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public array $lastErrors = [];

    public function effectivePrice(Product $product): int
    {
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

    public function expectedUnitPrice(Product $product, ?string $variation): int
    {
        if ($product->variant_product && $variation !== null && $variation !== '') {
            $stock = ProductStock::where('product_id', $product->id)
                ->where('variant', $variation)
                ->first();
            if ($stock && (float) $stock->price > 0) {
                return (int) $stock->price;
            }
        }

        return $this->effectivePrice($product);
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
     * @param Collection $cartItems Cart models with product relation
     * @return array{subtotal:int,tax:int,weight:int,lines:array}
     *
     * @throws ValidationException on price mismatch or insufficient stock
     */
    public function validate(Collection $cartItems): array
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

            $expected = $this->expectedUnitPrice($product, $item->variation);

            if ((int) $item->price !== $expected) {
                $errors[] = __('commerce.checkout_price_changed', ['name' => $product->name]);
                continue;
            }

            $available = $this->availableQty($product, $item->variation);
            if ((int) $item->quantity > $available) {
                $errors[] = __('commerce.checkout_stock_insufficient', ['name' => $product->name]);
                continue;
            }

            $qty = (int) $item->quantity;
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

        return [
            'subtotal' => (int) $subtotal,
            'tax' => (int) $tax,
            'weight' => (int) $weight,
            'lines' => $lines,
        ];
    }
}
