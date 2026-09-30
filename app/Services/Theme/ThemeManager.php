<?php

namespace App\Services\Theme;

use App\Models\BusinessSetting;
use App\Models\Theme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ThemeManager
{
    public static function activeKey(): string
    {
        try {
            $key = BusinessSetting::getValue('theme_active', 'default');
        } catch (\Throwable) {
            $key = 'default';
        }

        return trim((string) ($key ?: 'default'));
    }

    public static function active(): ?Theme
    {
        try {
            if (! Schema::hasTable('themes')) {
                return null;
            }

            return Cache::remember('theme.active.'.static::activeKey(), 300, function () {
                return Theme::where('key', static::activeKey())->first()
                    ?? Theme::where('is_active', true)->first()
                    ?? Theme::where('key', 'default')->first();
            });
        } catch (\Throwable) {
            return null;
        }
    }

    public static function setting(string $key, $default = null)
    {
        $theme = static::active();

        return $theme ? $theme->setting($key, $default) : $default;
    }

    public static function activate(string $key): bool
    {
        $theme = Theme::where('key', $key)->first();
        if (! $theme) {
            return false;
        }
        Theme::query()->update(['is_active' => false]);
        $theme->update(['is_active' => true]);
        BusinessSetting::updateOrCreate(['key' => 'theme_active'], ['type' => 'theme', 'value' => $key]);
        Cache::forget('theme.active.'.$key);
        Cache::forget('theme.active.default');

        return true;
    }
}
