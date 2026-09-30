<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static function effectivePrice(mixed $product): int
    {
        $price = (float) ($product->unit_price ?? 0);
        $discount = (float) ($product->discount ?? 0);

        if ($discount > 0 && self::discountActive($product)) {
            if (($product->discount_type ?? '') === 'percent') {
                $price -= $price * ($discount / 100);
            } else {
                $price -= $discount;
            }
        }

        return (int) round(max(0, $price));
    }

    public static function discountActive(mixed $product): bool
    {
        $now = now()->timestamp;
        $start = $product->discount_start_date ? (int) $product->discount_start_date : null;
        $end = $product->discount_end_date ? (int) $product->discount_end_date : null;

        if ($start && $now < $start) {
            return false;
        }
        if ($end && $now > $end) {
            return false;
        }

        return true;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (int) round((float) $this->unit_price),
            'final_price' => self::effectivePrice($this->resource),
            'discount' => (float) ($this->discount ?? 0),
            'discount_type' => $this->discount_type,
            'rating' => (float) ($this->rating ?? 0),
            'num_of_sale' => (int) ($this->num_of_sale ?? 0),
            'unit' => $this->unit,
            'thumbnail' => $this->thumbnail_img,
            'photos' => $this->photos,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id, 'name' => $this->category?->name, 'slug' => $this->category?->slug,
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand?->id, 'name' => $this->brand?->name, 'slug' => $this->brand?->slug,
            ]),
        ];
    }
}
