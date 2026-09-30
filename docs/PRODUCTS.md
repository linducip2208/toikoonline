# Products — B2B Hooks

- `Product::priceListItems()` (hasMany PriceListItem) — relasi aditif baru.
- Harga efektif checkout: price-list perusahaan → price-list global → harga reguler
  (diskon produk) → harga varian `product_stocks.price` (lihat ECOMMERCE.md).
- `OrderDetail.price` = unit price yang benar-benar ditagih (hasil revalidasi server,
  sudah termasuk harga price-list). Semantik kolom tidak berubah (tetap per-unit).
- Filament: PriceListResource (repeater item: product/variant/price integer/min_qty),
  CompanyResource (aksi attachUser via CompanyService), QuoteResource (aksi approve).
- Tidak ada perubahan kolom decimal legacy di products/product_stocks.
