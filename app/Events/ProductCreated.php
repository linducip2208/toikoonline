<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Product $product) {}

    public function payload(): array
    {
        return [
            'product_id' => $this->product->id,
            'slug' => $this->product->slug,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
