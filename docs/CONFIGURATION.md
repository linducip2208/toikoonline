# CONFIGURATION (Agent 4)

## Rute baru (DOCUMENTED — integrator append ke `routes/web.php`)

```php
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Storefront\ContactController;
use App\Http\Controllers\Storefront\FaqController;

// dalam group account (auth) yang sudah ada:
Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

// publik:
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:10,1');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/offline', fn () => view('pwa.offline'))->name('pwa.offline');

// PWA (statis — cukup file; alternatif route bila ingin header dinamis):
// Route::get('/manifest.webmanifest', fn () => response()->file(public_path('manifest.webmanifest'))
//     ->header('Content-Type', 'application/manifest+json'))->name('pwa.manifest');

// installer — lihat docs/INSTALLATION.md
```

## Rate-limit login (DOCUMENTED — `routes/auth.php` milik bersama, snippet saja)

```php
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

// di dalam group guest:
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:login'); // 5 percobaan/menit per email+IP

// di AppServiceProvider::boot (atau RouteServiceProvider):
RateLimiter::for('login', fn (Request $r) =>
    \Illuminate\Cache\RateLimiting\Limit::perMinute(5)
        ->by(strtolower($r->input('email', '').'|'.$r->ip()))
        ->response(fn () => back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi 1 menit.'])));
```

Tidak diterapkan langsung oleh Agent 4 karena `routes/auth.php` di luar
kepemilikan dan perubahan rate limiter global berisiko konflik dengan
agen lain.

## i18n URL prefix (DOCUMENTED — butuh grup route `{locale}`)

`SetLocale` membaca segmen pertama (`/en/...`) tetapi `routes/web.php` belum
punya grup prefix locale, sehingga `/en/products/{slug}` saat ini 404.
Locale via session tetap bekerja (teruji di `JourneyTest`). Snippet:

```php
Route::prefix('{locale}')->where(['locale' => 'en'])->middleware('web')->group(function () {
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show.en');
});
```

## Env keys yang ditulis installer

`DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_NAME,
APP_URL, APP_TIMEZONE, APP_LOCALE` + `business_settings` (`shop_name,
currency, timezone`).
