# Panduan Admin TokoOnline

> Untuk operator toko: dashboard, laporan, pesanan, produk, dan analitik.

## 1. Dashboard (`/admin`)

Widget tampil sesuai data nyata — basis kosong menampilkan `0` / status kosong, bukan angka contoh.

| Widget | Sumber data |
|---|---|
| Pesanan Pending / 30 Hari / Pelanggan / Stok Menipis | `orders`, `users`, `product_stocks` via `ReportService` |
| Pendapatan / Pesanan / Pelanggan Baru / AOV / Refund (30 hari) | `ReportService` (rentang default 30 hari) |
| Grafik Pendapatan | `OrderChartWidget` — filter: Hari ini / Kemarin / 7 / 30 / 90 hari / Tahun ini |
| Status Pengiriman & Status Pembayaran | `OrderStatusChart`, `PaymentStatusChart` — filter periode sama |
| 10 Produk Terlaris / Kategori Terlaris | `num_of_sale` + `order_details` qty |
| Stok Menipis | `product_stocks.qty <= COALESCE(products.low_stock_qty, 10)` |
| Keranjang Terlantar | muncul hanya bila ada cart > 24 jam (`carts`) |
| Pesanan Terbaru | 10 order terakhir + relasi user |

Semua angka dashboard dan halaman Dasbor Analitik berasal dari **satu sumber**:
`App\Services\Analytics\ReportService`.

## 2. Dasbor Analitik (`/admin/analytics-dashboard`, grup 📊 Laporan)

Filter periode: Hari ini, Kemarin, 7/30/90 hari, Tahun ini, Kustom (dari–sampai).
Menampilkan: pendapatan (paid), pesanan, pelanggan baru, AOV, refund,
breakdown status pembayaran & pengiriman, produk & kategori terlaris,
stok menipis, dan pendapatan harian (zero-filled).

## 3. Alur pesanan

1. **Pesanan → daftar**: filter status bayar/kirim, buka detail untuk verifikasi.
2. **Konfirmasi**: ubah `delivery_status` bertahap
   `pending → confirmed → picked_up → on_delivery → delivered`.
3. **Resi**: isi `courier` + `tracking_number` — otomatis jadi timeline
   di halaman `/account/orders/{id}` pelanggan (via `OrderObserver`).
4. **Refund**: set `payment_status = refunded`; nominal ikut total refund laporan.
5. **Batal**: set `delivery_status = cancelled` (hanya sebelum dikirim).

## 4. Produk & stok

- Produk: `published` + `approved` = tampil di storefront.
- Stok per varian di `product_stocks` (`variant`, `sku`, `price`, `qty`).
- Ambang peringatan per produk: kolom `low_stock_qty` (0 = pakai default 10).
- Harga checkout divalidasi ulang server (`CheckoutService`): harga cart harus
  sama dengan harga efektif produk dan qty tidak melebihi stok.

## 5. Kupon & loyalty

- Kupon (`coupons`): tipe `percent`/`amount`, `min_buy`, `max_discount`,
  `start_date`/`end_date` (timestamp), 1x pakai per user (`coupon_usages`).
- Poin loyalty: otomatis 1 poin per Rp10.000 saat webhook menandai lunas.

## 6. Peran & izin

Seed izin granular (lihat `docs/10-security.md`):

```bash
php artisan db:seed --class=Database\Seeders\PermissionsSeeder
```

- `super_admin`: semua izin (termasuk `manage_roles`, `manage_settings`).
- `admin`: semua kecuali `manage_roles` + `manage_settings`.
- `staff`: operasional (`view_products`, `view_orders`, `update_orders`,
  `manage_inventory`, `manage_shipping`, `manage_media`).
- `customer`: tanpa izin admin.
