<?php

namespace App\Http\Middleware;

use App\Services\Analytics\Tracker;
use Closure;
use Illuminate\Http\Request;

/**
 * Tracks page_view + product_view analytics events (fire-and-forget).
 *
 * NOTE (integrator): register in bootstrap/app.php:
 *
 *   ->withMiddleware(function (Middleware $middleware): void {
 *       $middleware->web(append: [
 *           \App\Http\Middleware\TrackPageView::class,
 *       ]);
 *   })
 *
 * Product-view detection uses the route name `products.show` with a
 * `slug` parameter — no controller edits required.
 */
class TrackPageView
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('get') && ! $request->ajax() && ! $request->wantsJson()) {
            $route = $request->route();

            if ($route && $route->getName() === 'products.show') {
                Tracker::track('product_view', [
                    'slug' => $route->parameter('slug'),
                ]);
            } else {
                Tracker::track('page_view');
            }
        }

        return $response;
    }
}
