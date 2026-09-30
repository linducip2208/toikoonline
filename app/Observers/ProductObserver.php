<?php

namespace App\Observers;

use App\Models\Product;

/**
 * Emit product.created / product.updated (guarded, non-fatal) untuk
 * automation + outbound webhook. Sitemap mengandalkan updated_at.
 */
class ProductObserver
{
    public function created(Product $product): void
    {
        $this->fire('created', $product);
    }

    public function updated(Product $product): void
    {
        $this->fire('updated', $product);
    }

    protected function fire(string $kind, Product $product): void
    {
        try {
            $class = $kind === 'created' ? \App\Events\ProductCreated::class : \App\Events\ProductUpdated::class;
            if (! class_exists($class)) {
                return;
            }
            event(new $class($product));
        } catch (\Throwable) {
        }
    }
}
