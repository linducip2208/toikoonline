<?php

namespace App\Services\B2B;

use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\User;

/**
 * Effective-price resolver. Priority:
 *   1. active company price list of the user (company list)
 *   2. active global price list (company_id null)
 *   3. product regular price (fallback, resolved by caller)
 *
 * All prices are integer IDR.
 */
class PriceListService
{
    public function priceFor(Product $product, ?string $variant = null, ?User $user = null, int $qty = 1): ?int
    {
        $variant = $variant !== '' ? $variant : null;

        if ($user) {
            $companyIds = $user->companies()->where('companies.is_active', true)->pluck('companies.id')->all();
            foreach ($companyIds as $companyId) {
                $hit = $this->lookup($companyId, $product->id, $variant, $qty);
                if ($hit !== null) {
                    return $hit;
                }
            }
        }

        return $this->lookup(null, $product->id, $variant, $qty);
    }

    /**
     * @param int|null $companyId null = global list
     */
    protected function lookup(?int $companyId, int $productId, ?string $variant, int $qty): ?int
    {
        $lists = PriceList::where('is_active', true)
            ->when($companyId === null, fn ($q) => $q->whereNull('company_id'), fn ($q) => $q->where('company_id', $companyId))
            ->orderBy('id')
            ->pluck('id');

        if ($lists->isEmpty()) {
            return null;
        }

        $item = PriceListItem::whereIn('price_list_id', $lists)
            ->where('product_id', $productId)
            ->where('min_qty', '<=', max(1, $qty))
            ->when($variant === null, fn ($q) => $q->whereNull('variant'), fn ($q) => $q->where('variant', $variant))
            ->orderByDesc('min_qty')
            ->first();

        return $item ? (int) $item->price : null;
    }
}
