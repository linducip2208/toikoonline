# SECURITY (Agent 4 — re-audit)

## (a) Authorization sweep — controller milik Agent 4

| Controller | Check | Status |
|---|---|---|
| `Customer\AddressController` semua action | `user_id === Auth::id()`, else 403 | ✅ + test 403 lintas-user |
| `Storefront\ContactController` | publik, validate, throttle documented | ✅ |
| `Storefront\FaqController` | hanya `Page::active()` (status + jadwal) | ✅ |
| `Install\InstallController` | tolak bila lock ada di tiap action | ✅ |
| `Customer\ProfileController` (existing) | update milik sendiri via `Auth::user()` | ✅ (read-only audit) |
| `Customer\OrderController` (existing) | `user_id` check di show/receive/refund | ✅ (read-only audit) |
| `Storefront\CheckoutController@success` | `user_id` check | ✅ (read-only audit) |

Laporan lain: `PagePreviewController` memakai `middleware('auth')` saja —
preview page unpublished oleh user biasa (bukan admin). Di luar kepemilikan;
disarankan `can:view` / role admin (file: `app/Http/Controllers/Storefront/PagePreviewController.php`).

## (b) Upload hardening

| Lokasi | Disk | Status |
|---|---|---|
| `ProductResource` FileUpload (thumbnails/photos/variants/meta) | default (local) + `image()` + resize | ✅ aman (tipe image, resize server-side) |
| `CategoryResource` banner/icon | default + `image()` | ✅ |
| `UploadResource` (milik Agent 1 — audit saja) | — | ⚠️ laporkan ke Agent 1 bila tanpa validasi mime |
| `ProfileController` avatar | `public`, `image`, max 2048 | ✅ |

File milik Agent 4 tidak menambah upload baru. Rekomendasi global: set
`FILESYSTEM_DISK=public` + `php artisan storage:link` di produksi; validasi
`mimes:jpg,jpeg,png,webp|max:2048` di semua FileUpload non-image.

## (c) N+1

- FIXED: `ProductResource` +`stocks` eager (`app/Filament/Resources/ProductResource.php:473`).
- Homepage/product page: tidak ada N+1 per-baris (lihat DEVELOPER_GUIDE).

## (d) Image `loading=lazy`

- ✅ `storefront/search` (sudah), `customer/wishlist` (sudah), `storefront/cart` (ditambah Agent 4).
- `storefront/product-detail`, `product-listing`, `home/sections/*` milik agen
  lain — audit visual: tambahkan `loading="lazy"` kecuali hero/LCP.

## (e) Rate limit

Login belum di-rate-limit (`routes/auth.php` di luar kepemilikan) — snippet
`throttle:login` (5/menit per email+IP) di `docs/CONFIGURATION.md`.
Webhook pembayaran sudah `throttle:60,1`. Kontak disarankan `throttle:10,1`.
