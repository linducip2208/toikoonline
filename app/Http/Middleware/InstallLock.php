<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Installer lock: block /install* once storage/app/installed.lock exists.
 *
 * REGISTRATION (bootstrap/app.php) — must run BEFORE RequirePair so a fresh
 * (unpaired) app can still reach the installer:
 *
 *   $middleware->web(prepend: [
 *       \App\Http\Middleware\InstallLock::class,
 *   ]);
 *
 * Optional global gate (NOT enabled by default — keeps local dev & tests
 * untouched): redirect every non-install request to the installer while the
 * lock file is missing. See docs/INSTALLATION.md for the snippet.
 */
class InstallLock
{
    public static function lockPath(): string
    {
        return storage_path('app/installed.lock');
    }

    public static function isInstalled(): bool
    {
        return file_exists(static::lockPath());
    }

    public function handle(Request $request, Closure $next): Response
    {
        $path = '/'.ltrim($request->path(), '/');
        $isInstallRoute = str_starts_with($path, '/install');

        if ($isInstallRoute && static::isInstalled()) {
            return redirect()->to('/')->with('error', 'Aplikasi sudah terinstal. Hapus file storage/app/installed.lock untuk instal ulang.');
        }

        return $next($request);
    }
}
