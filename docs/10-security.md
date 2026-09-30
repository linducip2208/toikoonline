# Keamanan TokoOnline

> Audit Agent 4 (30 Sep 2026). Temuan di file milik agent lain DILAPORKAN,
> tidak diperbaiki — kecuali wiring yang diminta (seeder + snippet registrasi).

## 1. Hasil audit per area

| Area | Status | File:baris | Keterangan |
|---|---|---|---|
| CSRF webhook pembayaran | ⚠️ WIRING | `bootstrap/app.php:19-21` | `validateCsrfTokens(except: ['/admin/login'])` — route `POST /webhooks/payment/{gatewayId}` **tidak** dikecualikan → notifikasi Midtrans/Xendit/Tripay asli akan 419. Integrator: tambahkan `'webhooks/*'` ke `except`. Test memakai `withoutMiddleware(VerifyCsrfToken::class)` khusus untuk logika signature. |
| Rate-limit webhook | ⚠️ SARAN | `routes/web.php` (~l.105) | Webhook tanpa `throttle`. Saran integrator: `Route::post(...)->middleware('throttle:60,1')`. |
| Mass assignment | ✅ OK | `app/Models/*.php` | Semua model memakai `$fillable` eksplisit (Order, Product, Cart, Coupon, dsb.). Tidak ada `$guarded = []`. |
| XSS Blade | ✅ OK | `resources/views/**` | Output memakai `{{ }}`; `{!! !!}` hanya untuk `$post->content` (konten CMS admin-tepercaya) di `storefront/blog/show.blade.php:156`. Saran: sanitasi HTML saat simpan (integrator/CMS). |
| Upload | ✅ OK | `ProfileController@update` | Validasi `image|max:2048` untuk avatar. |
| Otorisasi akun | ✅ OK | `Customer\OrderController@show:22-24`, `CheckoutController@success:211-213` | Cek `user_id === Auth::id()` + `abort(403)`. |
| HMAC webhook | ✅ OK | `SnapRedirectAdapter@verifyCallback:74-84` | `hash_equals(sha512(order_id.status_code.gross_amount.server_key))`. Test HMAC fixture hijau. |
| Kunci gateway | ✅ OK | `PaymentGatewayConfig` | `api_key_encrypted`/`api_secret_encrypted` via `Crypt`; `getMaskedKey()` untuk tampilan. |
| OrderObserver timeline | ⚠️ BUG (milik agent lain) | `app/Observers/OrderObserver.php` | `DeliveryHistory::create()` tanpa `order_detail_id` (NOT NULL) → tiap update resi/status 500. Pemilik: perbaiki/isi `order_detail_id`. |
| Webhook paid side-effect | ⚠️ BUG (milik agent lain) | `WebhookController.php:144` | `InventoryService::commit($order)` vs signature `commit(int $productId, ...)` → `TypeError` (tidak tertangkap `catch (\Exception)`) → response 500 walau order sudah paid. Lihat TEST RESULTS. |
| Homepage 500 (test) | ⚠️ BUG (milik agent lain) | `CmsSection::ordered()` (`app/Models/CmsSection.php:22-25`) + `HomeController.php:44` | `ordered()` me-return Collection lalu `->get()` dipanggil lagi → `ArgumentCountError`. Fix pemilik: jadikan scope/query atau hapus `->get()` di controller. |
| Blog index 500 (test) | ⚠️ BUG (milik agent lain) | `app/Models/BlogCategory.php:15-18` | `blogs()` pakai FK default `blog_category_id`, tabel memakai `category_id`. Fix pemilik: `hasMany(Blog::class, 'category_id')`. |
| Double-boot fatal (test) | ⚠️ BUG (milik agent lain) | `WebhookSubscriptionResource/Pages/WebhookSubscriptionPages.php:10` | 3 class dalam 1 file (pelanggaran PSR-4) → `composer include` dieksekusi ulang tiap app boot → suite multi-test fatal. Fix pemilik: pecah jadi 3 file PSR-4. Workaround sementara: `phpunit --process-isolation`. |

## 2. Kebijakan otorisasi (baru, Agent 4)

`app/Policies/`: `ProductPolicy`, `OrderPolicy`, `UserPolicy`,
`CategoryPolicy`, `CouponPolicy`, `PagePolicy`.

**WIRING — integrator** (Laravel 12 tidak memakai `AuthServiceProvider`;
registrasi di `app/Providers/AppServiceProvider.php` — file milik integrator):

```php
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Product::class, ProductPolicy::class);
    Gate::policy(Order::class, OrderPolicy::class);
    Gate::policy(User::class, UserPolicy::class);
    Gate::policy(Category::class, CategoryPolicy::class);
    Gate::policy(Coupon::class, CouponPolicy::class);
    Gate::policy(Page::class, PagePolicy::class);
}
```

## 3. Seeder izin (baru, Agent 4)

```bash
php artisan db:seed --class=Database\Seeders\PermissionsSeeder
```

Membuat (firstOrCreate) 21 permission: `view/create/update/delete/publish_products`,
`view/update/refund/cancel_orders`, `manage_inventory|shipping|payment|cms|pages|
themes|media|settings|users|roles|api|webhooks`; sinkron ke peran
(super_admin penuh; admin tanpa roles+settings; staff operasional).

## 4. Middleware analitik (baru, Agent 4)

**WIRING — integrator** (`bootstrap/app.php`, milik integrator):

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\TrackPageView::class,
    ]);
})
```

Hanya GET non-AJAX; deteksi `product_view` via nama route `products.show`,
selainnya `page_view`. Fire-and-forget (try/catch, tak pernah throw).

## 5. Audit sensitive-logging (Services/Payment + Shipping — report only)

| File | Temuan |
|---|---|
| `Services/Payment/SnapRedirectAdapter.php:51,54` | `Log::error('Midtrans Snap error', ['response' => body])` — body gateway bisa memuat token; saran: log status code saja. |
| `Services/Payment/PaymentGatewayService.php:46-49` | Log hanya `gateway_id` + format — aman. |
| `Payment/WebhookController.php` (baru, agent lain) | `logAttempt()` sudah dirancang aman (tanpa secret); kecuali `Log::error(..., ['gateway_id', 'payload' => $payload])` versi lama — versi terbaru sudah menghapus payload. Payload mentah disimpan di kolom `payment_transactions.raw` (data gateway, bukan secret server) — pastikan tabel itu tidak diekspos ke Filament read (cek `PaymentTransactionResource`, milik Agent 2/3). |
| `Services/Shipping/*` | Tidak ditemukan log berisi API key / alamat lengkap pada file yang dibaca (`ShippingService.php` log waybill/order-level). API key Biteship/RajaOngkir hanya dipakai di header HTTP, tidak di-log. |
