# UPGRADE (Agent 4)

## Ke 2026_09_30_000504 (contact_messages)

- Migrasi BARU saja, tanpa mengubah migrasi lama:

```
php artisan migrate --force
```

- Tabel `contact_messages`: `name, email, subject?, message, is_read=false`,
  index `is_read`. Rollback aman (`down` drop table).
- Resource admin `ContactMessageResource` otomatis muncul di grup 📢 Marketing
  setelah migrate (tanpa publish config).
- Notifikasi database memakai tabel `notifications` yang sudah ada
  (`2026_06_10_000091`).

## PWA

- Copy `public/manifest.webmanifest` + `public/sw.js` ikut deploy statis
  (tanpa `npm run build`). Naikkan `VERSION` di `sw.js` tiap ada perubahan
  asset agar cache lama dibersihkan.
- Tambahkan snippet manifest + registrasi SW ke layout (lihat
  `docs/DEVELOPER_GUIDE.md`).

## Installer di server yang sudah jalan

- Buat lock manual agar wizard terkunci: `echo '{}' > storage/app/installed.lock`.
- Hapus lock HANYA untuk instal ulang yang disengaja.

## Test

```
php vendor/bin/phpunit --filter JourneyTest
```
