<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'name', 'country', 'state', 'city', 'postcode', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function methods()
    {
        return $this->hasMany(ShippingMethod::class, 'zone_id')->orderBy('sort_order');
    }

    /**
     * Wildcard match (* supported, comma-separated list). Empty pattern = any.
     */
    public static function patternMatches(?string $pattern, ?string $value): bool
    {
        $pattern = trim((string) $pattern);
        if ($pattern === '' || $pattern === '*') {
            return true;
        }
        $value = strtolower(trim((string) $value));
        foreach (explode(',', $pattern) as $part) {
            $part = strtolower(trim($part));
            if ($part === '' || $part === '*') {
                return true;
            }
            // fnmatch is case-sensitive; both sides already lowered.
            if (fnmatch($part, $value)) {
                return true;
            }
        }

        return false;
    }

    public function matches(array $address): bool
    {
        return static::patternMatches($this->country, $address['country'] ?? null)
            && static::patternMatches($this->state, $address['state'] ?? null)
            && static::patternMatches($this->city, $address['city'] ?? null)
            && static::patternMatches($this->postcode, $address['postcode'] ?? null);
    }
}
