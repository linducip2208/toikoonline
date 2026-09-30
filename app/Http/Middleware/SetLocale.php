<?php

namespace App\Http\Middleware;

use App\Services\Locale\LocaleManager;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $active = LocaleManager::activeLocales();
        $default = LocaleManager::defaultLocale();

        $first = strtolower(trim((string) $request->segment(1)));
        if (in_array($first, $active, true) && $first !== $default) {
            $locale = $first;
            session(['locale' => $locale]);
        } else {
            $locale = strtolower(trim((string) session('locale', '')));
            if (! in_array($locale, $active, true)) {
                $locale = $default;
            }
        }

        app()->setLocale($locale);
        try {
            Carbon::setLocale($locale);
        } catch (\Throwable) {
        }
        view()->share('activeLocales', $active);
        view()->share('currentLocale', $locale);
        view()->share('isRtl', LocaleManager::isRtl($locale));

        return $next($request);
    }
}
