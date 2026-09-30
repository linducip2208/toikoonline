<?php

namespace App\Services\Shipping;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\Log;

/**
 * Table-rate shipping: zone match by address wildcards, weight, subtotal.
 * Pure quote math lives on ShippingMethod::quoteFor (unit-testable).
 * Output rows use the same normalized shape as ShippingManager::quote so
 * checkout can merge both sources and sort by cost.
 */
class ShippingMethodService
{
    /**
     * @param array{country?:string,state?:string,city?:string,postcode?:string} $address
     * @return array<int, array{provider_id:int|null, provider:string, courier:string, service:string, description:string, cost:int, etd:string, source:string}>
     */
    public function quote(array $address, int $weightGram, int $subtotal): array
    {
        try {
            $zones = ShippingZone::with(['methods' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            Log::warning('ShippingMethodService skipped (non-fatal)', ['error' => $e->getMessage()]);

            return [];
        }

        $rates = [];
        foreach ($zones as $zone) {
            if (!$zone->matches($address)) {
                continue;
            }
            foreach ($zone->methods as $method) {
                /** @var ShippingMethod $method */
                $rates[] = [
                    'provider_id' => null,
                    'provider' => $zone->name,
                    'courier' => (string) $method->courier,
                    'service' => (string) $method->service,
                    'description' => (string) $method->name,
                    'cost' => $method->quoteFor(max(1, $weightGram), max(0, $subtotal)),
                    'etd' => (string) ($method->eta ?? ''),
                    'source' => 'table-rate',
                    'method_id' => $method->id,
                    'zone_id' => $zone->id,
                ];
            }
        }

        usort($rates, fn ($a, $b) => $a['cost'] <=> $b['cost']);

        return $rates;
    }

    /**
     * Merge table rates with live provider quotes. Sorted by cost.
     *
     * @param array<int,array> $liveRates rows from ShippingManager::quote
     * @return array<int,array>
     */
    public function mergeWithLive(array $liveRates, array $address, int $weightGram, int $subtotal): array
    {
        $merged = array_merge($liveRates, $this->quote($address, $weightGram, $subtotal));
        usort($merged, fn ($a, $b) => ((int) ($a['cost'] ?? 0)) <=> ((int) ($b['cost'] ?? 0)));

        return $merged;
    }
}
