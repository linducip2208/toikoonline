<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Fire-and-forget event tracker. Never throws — analytics must not
 * break the storefront even if the events table is missing.
 */
class Tracker
{
    public static function track(string $event, array $properties = [], ?string $url = null): void
    {
        try {
            AnalyticsEvent::create([
                'event' => substr($event, 0, 100),
                'user_id' => Auth::id(),
                'session_id' => substr((string) session()->getId(), 0, 100),
                'url' => $url ?? substr((string) request()->fullUrl(), 0, 500),
                'properties' => $properties ?: null,
            ]);
        } catch (\Throwable $e) {
            Log::debug('Analytics track skipped: '.$e->getMessage());
        }
    }
}
