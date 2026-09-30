# INSTALLATION (Agent 4 — Web Installer)

Wizard instalasi berbasis web, **berbasis lock-file saja** (tanpa cek `APP_ENV`):
tanpa `storage/app/installed.lock` installer berjalan normal (termasuk local dev);
setelah lock ditulis, `InstallLock` memblokir `/install*`.

## File

- `app/Http/Controllers/Install/InstallController.php` — langkah: requirements →
  database (tes koneksi + `migrate --force` + `db:seed --force`) → tulis `.env`
  (tanpa pernah meng-echo secret ke view) → akun admin (`super_admin`) →
  konfigurasi toko → tulis `storage/app/installed.lock` → view `install/finish`.
- `app/Http/Middleware/InstallLock.php` — blokir `/install*` bila lock ada.
- `resources/views/install/index.blade.php`, `resources/views/install/finish.blade.php`.

## WIRING NEEDED (integrator — append ke `routes/web.php`)

```php
use App\Http\Controllers\Install\InstallController;

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::post('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/store', [InstallController::class, 'store'])->name('store');
});
```

## Middleware registration (`bootstrap/app.php`) — HARUS sebelum `RequirePair`

```php
$middleware->web(prepend: [
    \App\Http\Middleware\InstallLock::class,
]);
```

## RequirePair bypass (tambahkan di `RequirePair::shouldBypass`)

```php
if (str_starts_with($path, '/install')) return true;
```

Tanpa ini, instalasi fresh (belum pairing) akan di-redirect ke `/__pair`
sebelum mencapai wizard.

## Proteksi instalasi ulang

- `InstallLock` + setiap action controller menolak bila lock ada (redirect `/`).
- Instal ulang manual: hapus `storage/app/installed.lock`, buka `/install`.
- TIDAK ada token reinstall — penghapusan lock membutuhkan akses server
  (disengaja; token URL akan bocor via log).

## Gate global opsional (TIDAK aktif default)

Jangan aktifkan global redirect-to-installer bila lock hilang — itu akan
merusak local dev & test. Jika produksi menginginkannya, tambahkan di
`InstallLock::handle` sebelum `return $next($request)`:

```php
if (! static::isInstalled() && ! $isInstallRoute && app()->isProduction()) {
    return redirect()->route('install.index');
}
```
