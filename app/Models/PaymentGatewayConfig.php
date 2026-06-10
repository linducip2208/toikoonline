<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'name',
        'gateway_format',
        'api_key_encrypted',
        'api_secret_encrypted',
        'merchant_id',
        'base_url',
        'extra_headers',
        'webhook_config',
        'config',
        'is_active',
        'is_sandbox',
        'sort_order',
    ];

    protected $casts = [
        'extra_headers' => 'array',
        'webhook_config' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
    ];

    public function setApiKeyEncryptedAttribute(?string $value): void
    {
        $this->attributes['api_key_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiKeyEncryptedAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function setApiSecretEncryptedAttribute(?string $value): void
    {
        $this->attributes['api_secret_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiSecretEncryptedAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function getServerKeyAttribute(): ?string
    {
        return $this->getRawAttribute('api_key_encrypted');
    }

    public function getClientKeyAttribute(): ?string
    {
        return $this->getRawAttribute('api_secret_encrypted');
    }

    public function getMaskedKey(): string
    {
        $key = $this->server_key;
        if (!$key || strlen($key) <= 8) {
            return '***';
        }
        return substr($key, 0, 4) . '****' . substr($key, -4);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOfFormat(Builder $query, string $format): void
    {
        $query->where('gateway_format', $format);
    }

    protected function getRawAttribute(string $field): ?string
    {
        $value = $this->attributes[$field] ?? null;
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }
}
