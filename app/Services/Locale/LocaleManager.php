<?php

namespace App\Services\Locale;

use App\Models\BusinessSetting;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class LocaleManager
{
    public static function activeLocales(): array
    {
        try {
            if (! Schema::hasTable('languages')) {
                return ['id', 'en'];
            }

            return Cache::remember('locale.active', 300, function () {
                $codes = Language::where('status', true)->pluck('code')->map(fn ($c) => strtolower(trim((string) $c)))->filter()->unique()->values()->all();

                return $codes !== [] ? $codes : ['id', 'en'];
            });
        } catch (\Throwable) {
            return ['id', 'en'];
        }
    }

    public static function defaultLocale(): string
    {
        try {
            $v = BusinessSetting::getValue('translation_default', null)
                ?? BusinessSetting::getValue('default_language', 'id');
        } catch (\Throwable) {
            $v = 'id';
        }
        $v = strtolower(trim((string) $v));
        $active = static::activeLocales();
        if (in_array($v, $active, true)) {
            return $v;
        }

        return in_array('id', $active, true) ? 'id' : ($active[0] ?? 'id');
    }

    public static function fallbackLocale(): string
    {
        try {
            $v = BusinessSetting::getValue('translation_fallback', null);
        } catch (\Throwable) {
            $v = null;
        }
        $v = $v ? strtolower(trim((string) $v)) : 'en';
        $active = static::activeLocales();

        return in_array($v, $active, true) ? $v : static::defaultLocale();
    }

    public static function isRtl(?string $locale = null): bool
    {
        $locale = strtolower($locale ?: app()->getLocale());
        try {
            if (! Schema::hasTable('languages')) {
                return false;
            }
            $lang = Language::where('code', $locale)->orWhere('app_lang_code', $locale)->first();

            return (bool) ($lang->rtl ?? false);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function dateFormat(?string $locale = null): string
    {
        $locale = strtolower($locale ?: app()->getLocale());

        return $locale === 'en' ? 'M d, Y' : 'd M Y';
    }

    public static function formatNumber(int|float $amount, ?string $locale = null): string
    {
        // Money is integer IDR; keep helper locale-aware for display.
        $locale = strtolower($locale ?: app()->getLocale());

        return $locale === 'en' ? number_format($amount, 0, '.', ',') : number_format($amount, 0, ',', '.');
    }
}
