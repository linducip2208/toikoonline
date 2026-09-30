<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'zone_id', 'provider_format', 'courier', 'service', 'name',
        'base_rate', 'per_kg', 'weight_tiers', 'free_min_subtotal',
        'eta', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'integer',
            'per_kg' => 'integer',
            'weight_tiers' => 'array',
            'free_min_subtotal' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }

    /**
     * Pure quote math. Money integer IDR. Never negative.
     */
    public function quoteFor(int $weightGram, int $subtotal): int
    {
        if ($this->free_min_subtotal && $subtotal >= (int) $this->free_min_subtotal) {
            return 0;
        }

        $weightKg = $weightGram / 1000;
        foreach ((array) ($this->weight_tiers ?? []) as $tier) {
            $maxKg = (float) ($tier['max_weight_kg'] ?? 0);
            if ($maxKg > 0 && $weightKg <= $maxKg) {
                return max(0, (int) ($tier['rate'] ?? 0));
            }
        }

        $extraKg = max(0, (int) ceil($weightKg) - 1);

        return max(0, (int) $this->base_rate + $extraKg * (int) $this->per_kg);
    }
}
