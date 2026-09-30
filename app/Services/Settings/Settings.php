<?php

namespace App\Services\Settings;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

/**
 * Typed settings service over the `business_settings` table (same store as
 * BusinessSetting::getValue — no parallel settings store).
 *
 * Facade-style usage: \App\Services\Settings\Settings::get('site_name')
 */
class Settings
{
    public const CACHE_PREFIX = 'settings.key.';

    public const GROUP_TYPE_MAP = [
        'social' => ['facebook', 'instagram', 'twitter', 'tiktok', 'youtube'],
        'smtp' => ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption', 'mail_from_address', 'mail_from_name'],
        'storefront' => ['best_selling', 'coupon_system', 'flash_deal', 'todays_deal', 'featured_products', 'featured_categories', 'newsletter', 'top_brands', 'new_products', 'home_banner1', 'home_banner2', 'home_banner3', 'category_products'],
    ];

    public static function typeFor(string $key): string
    {
        return SettingDefinition::definition($key)['type'] ?? 'string';
    }

    public static function defaultFor(string $key): mixed
    {
        return SettingDefinition::definition($key)['default'] ?? null;
    }

    protected static function tableReady(): bool
    {
        try {
            return Schema::hasTable('business_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    protected static function raw(string $key): ?string
    {
        if (! static::tableReady()) {
            return null;
        }

        try {
            return Cache::remember(static::CACHE_PREFIX.$key, 3600, function () use ($key) {
                return BusinessSetting::where('key', $key)->value('value');
            });
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Typed get with seeder-safe default (no DB write).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $def = SettingDefinition::definition($key);
        $fallback = $default ?? $def['default'] ?? null;

        $raw = static::raw($key);
        if ($raw === null || $raw === '') {
            return $fallback;
        }

        return static::castFromString($raw, static::typeFor($key), $fallback);
    }

    public static function castFromString(string $raw, string $type, mixed $fallback = null): mixed
    {
        try {
            return match ($type) {
                'int' => is_numeric($raw) ? (int) $raw : $fallback,
                'bool' => in_array(strtolower(trim($raw)), ['1', 'true', 'yes', 'on'], true),
                'json' => json_decode($raw, true) ?? $fallback,
                // Legacy plaintext (written via admin UI/seeder before encryption) is
                // returned as-is so direct BusinessSetting writes keep working.
                'encrypted' => $raw !== '' ? static::decryptOrRaw($raw, $fallback) : $fallback,
                default => $raw,
            };
        } catch (\Throwable) {
            return $fallback;
        }
    }

    protected static function decryptOrRaw(string $raw, mixed $fallback): mixed
    {
        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable) {
            return $raw !== '' ? $raw : $fallback;
        }
    }

    public static function castToString(mixed $value, string $type): string
    {
        return match ($type) {
            'int' => (string) (int) $value,
            'bool' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE) ?: '[]',
            'encrypted' => ($value === null || $value === '') ? '' : Crypt::encryptString((string) $value),
            default => (string) ($value ?? ''),
        };
    }

    /**
     * Validated set. Returns the stored (typed) value.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function set(string $key, mixed $value): mixed
    {
        $def = SettingDefinition::definition($key);
        if ($def && ! empty($def['rules'])) {
            Validator::make(['value' => $value], ['value' => $def['rules']])->validate();
        }

        $type = static::typeFor($key);
        $stored = static::castToString($value, $type);

        BusinessSetting::updateOrCreate(
            ['type' => static::dbTypeFor($key), 'key' => $key],
            ['value' => $stored]
        );
        static::flush($key);

        return static::get($key);
    }

    public static function dbTypeFor(string $key): string
    {
        foreach (static::GROUP_TYPE_MAP as $type => $keys) {
            if (in_array($key, $keys, true)) {
                return $type;
            }
        }

        return 'general';
    }

    public static function flush(?string $key = null): void
    {
        try {
            if ($key) {
                Cache::forget(static::CACHE_PREFIX.$key);
            } else {
                foreach (array_keys(SettingDefinition::all()) as $k) {
                    Cache::forget(static::CACHE_PREFIX.$k);
                }
            }
        } catch (\Throwable) {
        }
    }

    /**
     * All keys of a group with typed values (seeder-safe).
     */
    public static function group(string $group): array
    {
        $out = [];
        foreach (SettingDefinition::all() as $key => $def) {
            if ($def['group'] === $group) {
                $out[$key] = static::get($key);
            }
        }

        return $out;
    }

    public static function groups(): array
    {
        $out = [];
        foreach (array_keys(static::definedGroups()) as $group) {
            $out[$group] = static::group($group);
        }

        return $out;
    }

    public static function definedGroups(): array
    {
        return SettingDefinition::groups();
    }
}
