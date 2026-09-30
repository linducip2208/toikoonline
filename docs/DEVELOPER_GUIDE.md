# DEVELOPER GUIDE — DELTA AGENT 4

## Struktur baru

```
app/Http/Controllers/Customer/AddressController.php  # CRUD alamat, owner-checked
app/Http/Controllers/Storefront/ContactController.php
app/Http/Controllers/Storefront/FaqController.php
app/Http/Controllers/Install/InstallController.php
app/Http/Middleware/InstallLock.php
app/Console/Commands/HealthCheckCommand.php
app/Filament/Pages/SystemHealth.php
app/Filament/Resources/ContactMessageResource.php (+Pages/)
app/Filament/Imports/CategoryImporter.php
app/Filament/Exports/{CategoryExporter,UserExporter}.php
app/Models/ContactMessage.php
app/Notifications/ContactMessageReceived.php  # channel database
database/migrations/2026_09_30_000504_create_contact_messages_table.php
public/manifest.webmanifest  public/sw.js
resources/views/{customer/addresses/*,storefront/{contact,faq},pwa/offline,install/*,filament/pages/system-health}
tests/Feature/JourneyTest.php
```

## PWA

- `public/manifest.webmanifest` — statis; ikon memakai `/favicon.ico` yang
  ADA (tidak mengarang file ikon). Tambahkan ikon 192/512 nyata nanti dan
  daftarkan di `icons[]`.
- `public/sw.js` — versioned cache `tokoonline-v1-static`; network-first
  untuk navigasi + fallback `/offline`; cache-first untuk `/build/*` dan
  gambar/font; **tidak pernah** menyentuh non-GET, `/checkout`, `/cart`,
  `/account`, `/admin`, `/api/`, `/install`, `/__pair`, `/webhooks/`.
  Checkout selalu network — tidak ada offline checkout palsu.
- WIRING (layout `layouts/storefront` milik Agent 1 — snippet untuk integrator):

```html
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#4f46e5">
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}
</script>
```

## Konvensi

- Semua controller baru: validasi FormRequest-inline via `$request->validate`,
  owner-check via `abort(403)` (bukan 404 — eksplisit & teruji).
- `Address.set_billing` adalah kolom string(20) di migrasi lama → tulis `'1'|null`.
- Test mendaftarkan rute Agent-4 secara lokal (`Route::` di `setUp`) agar
  coverage HTTP nyata tanpa menyentuh `routes/*.php`.

## Query-count audit (tanpa debugbar — `DB::listen` di tinker/test)

- Homepage (`HomeController@index`): ~20 query (16 query koleksi independen +
  settings/CMS). Tidak ada N+1 per-baris; `categoryProducts` memakai
  constrained eager load. Saran: cache `settings` + `cmsSectionOrder`
  (TTL 1 jam) bila TTFB tinggi — di luar kepemilikan file, tidak diubah.
- Product page (`ProductController@show`): 4 query (produk + relasi eager,
  2 query related). OK. Catatan: related tidak eager `stocks` — bila
  partial card memakai stok, laporkan ke pemilik.
