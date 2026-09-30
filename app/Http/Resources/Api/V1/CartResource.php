<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name,
            'product_slug' => $this->product?->slug,
            'variation' => $this->variation,
            'price' => (int) round((float) $this->price),
            'quantity' => (int) $this->quantity,
            'line_total' => (int) round((float) $this->price * (int) $this->quantity),
        ];
    }
}
