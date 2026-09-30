<?php

namespace App\Services\Tax;

use App\Models\TaxRate;

class TaxService
{
    public function forAddress(array $address): ?TaxRate
    {
        $country = strtolower(trim((string) ($address['country'] ?? '*')));
        $state = strtolower(trim((string) ($address['state'] ?? '*')));
        $city = strtolower(trim((string) ($address['city'] ?? '*')));
        $postcode = trim((string) ($address['postcode'] ?? $address['postal_code'] ?? '*'));

        $rates = TaxRate::where('is_active', true)->orderByDesc('priority')->get();

        foreach ($rates as $rate) {
            if ($this->matches($rate->country, $country)
                && $this->matches($rate->state, $state)
                && $this->matches($rate->city, $city)
                && $this->matchesPostcode($rate->postcode, $postcode)) {
                return $rate;
            }
        }

        return null;
    }

    public function rateForAddress(array $address): float
    {
        return (float) ($this->forAddress($address)?->rate ?? 0);
    }

    public function computeTax(int $priceInclusiveOrExclusive, array $address, int $quantity = 1): array
    {
        $rate = $this->forAddress($address);
        $pct = (float) ($rate?->rate ?? 0);
        $inclusive = (bool) ($rate?->inclusive ?? false);

        $gross = $priceInclusiveOrExclusive * $quantity;

        if ($pct <= 0) {
            return ['rate' => 0.0, 'inclusive' => $inclusive, 'tax' => 0, 'net' => $gross, 'gross' => $gross];
        }

        if ($inclusive) {
            $net = (int) round($gross / (1 + $pct / 100));
            $tax = $gross - $net;
        } else {
            $net = $gross;
            $tax = (int) round($gross * $pct / 100);
        }

        return ['rate' => $pct, 'inclusive' => $inclusive, 'tax' => $tax, 'net' => $net, 'gross' => $net + $tax];
    }

    protected function matches(?string $pattern, string $value): bool
    {
        $pattern = strtolower(trim((string) $pattern));

        if ($pattern === '' || $pattern === '*') {
            return true;
        }

        if (str_contains($pattern, '*')) {
            return fnmatch($pattern, $value, FNM_CASEFOLD);
        }

        return $pattern === $value;
    }

    protected function matchesPostcode(?string $pattern, string $value): bool
    {
        return $this->matches($pattern ?? '*', strtolower($value));
    }
}
