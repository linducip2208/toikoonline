<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ShippingConfig extends Model
{
    protected $fillable = [
        'name',
        'provider_format',
        'api_key_encrypted',
        'base_url',
        'extra_params',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'extra_params' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function setApiKeyEncryptedAttribute(?string $value): void
    {
        if ($value !== null && $value !== '') {
            $this->attributes['api_key_encrypted'] = Crypt::encryptString($value);
        } else {
            $this->attributes['api_key_encrypted'] = null;
        }
    }

    public function getApiKeyEncryptedAttribute(): ?string
    {
        $value = $this->attributes['api_key_encrypted'] ?? null;

        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
