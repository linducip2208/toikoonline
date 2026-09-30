<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'grand_total' => (int) round((float) $this->grand_total),
            'shipping_cost' => (int) round((float) $this->shipping_cost),
            'coupon_discount' => (int) round((float) $this->coupon_discount),
            'payment_status' => $this->payment_status,
            'delivery_status' => $this->delivery_status,
            'payment_type' => $this->payment_type,
            'courier' => $this->courier,
            'tracking_number' => $this->tracking_number,
            'created_at' => $this->created_at?->toIso8601String(),
            'items' => $this->whenLoaded('orderDetails', fn () => $this->orderDetails->map(fn ($d) => [
                'product_id' => $d->product_id,
                'product_name' => $d->product?->name,
                'variation' => $d->variation,
                'price' => (int) round((float) $d->price),
                'quantity' => (int) $d->quantity,
                'delivery_status' => $d->delivery_status,
            ])->all()),
        ];
    }
}
