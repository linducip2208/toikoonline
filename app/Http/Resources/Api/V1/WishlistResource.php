<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product', fn () => [
                'name' => $this->product?->name,
                'slug' => $this->product?->slug,
                'price' => (int) round((float) $this->product?->unit_price),
            ]),
        ];
    }
}
