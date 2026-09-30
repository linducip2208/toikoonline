<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\FlashDeal;

class FlashDealController extends Controller
{
    public function show($slug)
    {
        $deal = FlashDeal::where('slug', $slug)->where('status', true)->with('flashDealProducts.product')->firstOrFail();

        $products = $deal->flashDealProducts->map(function ($fdp) {
            $product = $fdp->product;
            if (!$product) return null;

            $effPrice = $fdp->discount_type === 'percent'
                ? $product->unit_price * (1 - $fdp->discount / 100)
                : $product->unit_price - $fdp->discount;

            $product->effective_price = max(0, $effPrice);
            $product->deal_discount = $fdp->discount;
            $product->deal_discount_type = $fdp->discount_type;

            return $product;
        })->filter();

        return view('storefront.flash-deal', compact('deal', 'products'));
    }
}
