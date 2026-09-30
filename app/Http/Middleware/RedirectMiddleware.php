<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (! Schema::hasTable('redirects')) {
                return $next($request);
            }
            if (! $request->isMethod('get')) {
                return $next($request);
            }
            $path = '/'.ltrim($request->path(), '/');
            $path = $path === '/' ? '/' : rtrim($path, '/');
            $row = Redirect::where('from_path', $path)->where('is_active', true)->first();
            if ($row) {
                $row->increment('hits');
                $code = in_array((int) $row->code, [301, 302], true) ? (int) $row->code : 301;

                return redirect($row->to_path, $code);
            }
        } catch (\Throwable) {
        }

        return $next($request);
    }
}
