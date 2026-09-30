# Translasi

Implementasi aktual (default locale `id`):

- File: `lang/id.json` + `lang/en.json` (414 kunci per 30 Sep 2026).
  Namespace: `common.*`, `storefront.*`, `checkout.*`, `cms.*`,
  `commerce.*`, `pseo.*`. Nilai `id` = teks Indonesia yang tampil saat ini.
- View yang sudah `__()`: `layouts/storefront.blade.php`,
  `storefront/home.blade.php` + `home/sections/*`, `storefront/partials/*`,
  `storefront/product-detail.blade.php`, `storefront/product-listing.blade.php`,
  `storefront/checkout*.blade.php`, `pseo/*`.
  Belum (milik agen lain / konten): cart, search, blog, dashboard views,
  `home/sections` tidak ada yang dikecualikan — semua sudah.
- Terjemahan entitas (produk/kategori/dll): trait `Translatable`
  (`entity_translations`, fallback locale→default→atribut mentah).
- Admin terjemahan (Filament → Translations):
  filter Missing in en/id, import/export CSV, aksi bulk
  "Publish: isi locale lain yang hilang" (firstOrCreate, tanpa duplikat),
  header action "Laporan Cakupan" (cakupan per locale: `lang/*.json`
  vs tabel `translations`).
- `php artisan translations:scan [--sync]`: pindai key `__()/trans()`
  di `app/` + `resources/views`, laporkan yang hilang di id.json/en.json/DB;
  `--sync` mengisi yang hilang ke tabel translations.
- Aturan: setiap teks baru untuk user wajib masuk ke **kedua** file lang.
