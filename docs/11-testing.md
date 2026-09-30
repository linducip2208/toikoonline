# Pengujian TokoOnline

> Milik Agent 4: seluruh `tests/*`, kompatibilitas sqlite, dan 5+ file test baru.

## 1. Menjalankan test

```bash
# Seluruh suite milik Agent 4 (isolation = workaround bug PSR-4, lihat §4)
vendor/bin/phpunit --process-isolation tests/Unit/PricingTest.php
vendor/bin/phpunit --process-isolation tests/Unit/InventoryMathTest.php
vendor/bin/phpunit --process-isolation tests/Feature/StorefrontTest.php
vendor/bin/phpunit --process-isolation tests/Feature/CheckoutFlowTest.php
vendor/bin/phpunit --process-isolation tests/Feature/SecurityTest.php
vendor/bin/phpunit --process-isolation tests/Feature/LocaleTest.php
```

Env test: `phpunit.xml` memakai sqlite `:memory:` (`DB_CONNECTION=sqlite`).
Semua test feature memakai `RefreshDatabase` + seed peran minimal di `setUp`
(tanpa `migrate:fresh` / menyentuh DB dev). Middleware lisensi
(`RequirePair`) di-bypass per-test karena pairing adalah urusan environment,
bukan perilaku yang diuji.

## 2. Matriks test

| File | Isi | Status |
|---|---|---|
| `tests/Feature/StorefrontTest.php` | home, products, search, blog, sitemap, login/register, admin login, dashboard customer | 7/9 (2 merah = bug milik agent lain, §4) |
| `tests/Unit/PricingTest.php` | `CouponService`: persen, max-discount, amount cap, min-buy, kode asing, sudah dipakai, kedaluwarsa | 7/7 ✅ |
| `tests/Unit/InventoryMathTest.php` | `InventoryService`: available, receive, reserve, over-reserve throws, release, commit, adjust-negatif throws | 7/7 ✅ |
| `tests/Feature/CheckoutFlowTest.php` | validasi error, stok kurang ditolak, order dibuat + cart bersih + num_of_sale, empty-cart redirect, webhook HMAC valid/invalid | 5/6 (1 merah = bug milik agent lain, §4) |
| `tests/Feature/SecurityTest.php` | guest → `/account/*` ditolak; order milik orang lain 403 + milik sendiri 200; webhook signature salah 403; gateway asing 404; checkout wajib login | 5/5 ✅ |
| `tests/Feature/LocaleTest.php` | middleware `SetLocale` terapkan locale sesi; format angka id/en | 2/2 ✅ (skip jujur bila class Agent 1 belum ada) |

## 3. Kompatibilitas sqlite

19 migrasi memakai `DB::statement('ALTER TABLE ... CONVERT TO CHARACTER SET ...')`
(MySQL-only) → dibungkus minimal:

```php
if (DB::getDriverName() === 'mysql') {
    DB::statement('ALTER TABLE ...');
}
```

`2026_06_10_000092_fix_missing_primary_keys_and_timestamps.php`: statement
`ADD COLUMN ... AUTO_INCREMENT` / `MODIFY ...` + helper `SHOW COLUMNS`
dijaga dengan guard driver yang sama (tanpa perubahan skema).

## 4. Merah yang jujur (bug milik agent lain, bukan test)

1. `StorefrontTest::test_homepage_loads` — `CmsSection::ordered()` me-return
   Collection, `HomeController.php:44` memanggil `->get()` lagi.
2. `StorefrontTest::test_blog_page_loads` — `BlogCategory::blogs()` memakai FK
   default `blog_category_id`, kolom tabel `category_id`.
3. `CheckoutFlowTest::test_midtrans_webhook_marks_order_paid...` — mapping paid
   benar (order jadi `paid`), tapi `WebhookController.php:144` memanggil
   `InventoryService::commit($order)` padahal signature-nya
   `commit(int $productId, ...)` → `TypeError` → 500.
4. Suite tanpa `--process-isolation` fatal pada test ke-2:
   `WebhookSubscriptionResource/Pages/WebhookSubscriptionPages.php` memuat
   3 class dalam 1 file (bukan PSR-4) → `composer include` mengeksekusi ulang
   file setiap app boot. Perbaikan milik pemilik file.

Semua di atas DILAPORKAN dengan file:baris agar pemiliknya bisa memperbaiki
dalam satu edit; tidak ada asersi palsu untuk menghijaukan suite.
