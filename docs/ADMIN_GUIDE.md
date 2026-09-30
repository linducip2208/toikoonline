# ADMIN GUIDE — DELTA AGENT 4

## Baru

| Item | Lokasi | Grup | Fungsi |
|---|---|---|---|
| Pesan Kontak (read-only) | `ContactMessageResource` | 📢 Marketing | Inbox form kontak: list + view + tandai dibaca + hapus. `canCreate() = false` |
| Kesehatan Sistem | `Filament\Pages\SystemHealth` | ⚙️ Sistem | Jalankan checks saat dibuka; tombol Refresh + Backup Database (`backup:database`) |
| Import Kategori | `CategoryResource` header | 📦 Katalog | `ImportAction` via `CategoryImporter` (kolom: name*, slug, parent_id, komisi, meta) |
| Export Kategori | `CategoryResource` header | 📦 Katalog | `ExportAction` via `CategoryExporter` |
| Export Pelanggan | `UserResource` header | ⚙️ Sistem | `ExportAction` via `UserExporter` (nama, email, telepon, kota, tipe) |

## Import/Export audit (wiring Product — VERIFIED OK)

- `ProductResource` header sudah wired: `ImportAction(ProductImporter)` +
  `ExportAction(ProductExporter)` — kolom & `resolveRecord` valid.
- **N+1 fix**: `ProductResource::getEloquentQuery` sekarang eager-load
  `stocks` (kolom SKU memakai `$record->stocks->first()` — sebelumnya query
  per baris). `category, brand` sudah eager.
- `CategoryResource` sebelumnya TANPA import/export → ditambah simetris
  dengan pola produk.

## Health checks (`health:check`, `--json` untuk monitoring)

`db` (PDO), `storage writable`, `cache` read/write, `queue` (hitung tabel
`jobs`/`failed_jobs` bila driver database), `disk` (ruang bebas, FAIL bila
< 10%). Exit code 1 bila ada FAIL. Jadwalkan:

```
* * * * * cd /var/www/tokoonline && php artisan schedule:run >> /dev/null 2>&1
```

## Backup

`backup:database` → `storage/app/backups/*.sql`, prune > 7 hari. Tombol di
SystemHealth memanggil command yang sama via `Artisan::call`.

## FAQ & Kontak (konten CMS)

- FAQ storefront (`/faq`) membaca halaman CMS `type = faq` + blok builder
  `faq` di halaman lain. Buat via Pages → type `faq`, status tampil.
- Pesan kontak masuk ke Marketing → Pesan Kontak + notifikasi database
  untuk semua `super_admin`/`admin`.
