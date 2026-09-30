# Ecommerce — B2B, Cart Merge, Checkout

## B2B (non-breaking, B2C tidak berubah)

- `companies` (name, code unik, tax_id/NPWP, payment_terms_days, credit_limit decimal legacy, is_active).
- `company_user` (company_id, user_id, role owner/staff, can_approve).
- `price_lists` (company_id nullable = global, name, currency default IDR, is_active).
- `price_list_items` (price_list_id, product_id, variant nullable, price INTEGER IDR, min_qty).
- `quotes` (company_id, user_id, status draft/sent/approved/rejected/expired, valid_until, notes, coupon_id)
  + `quote_items` (product/variant/qty/price integer).
- `orders.company_id` nullable (B2C = null).

## Price resolution

`PriceListService::priceFor($product, $variant, $user, $qty)`:
company list user → global list → null (caller pakai harga reguler).
Item dengan `min_qty <= qty` terbesar menang. Semua integer IDR.

`CheckoutService` (signature backward compatible, param `$user` opsional):
`effectivePrice($product, $user=null, $qty=1, $variation=null)`,
`expectedUnitPrice($product, $variation, $user=null, $qty=1)`,
`validate($cartItems, $user=null)` → validasi harga price-list juga.
Diskon grup: `groupDiscountPercent($user)` = max discount_percent grup aktif;
`validate()` mengembalikan `group_discount` (integer, persen × subtotal, capped subtotal).

## Quote approve → kupon

`QuoteService::approve($quote, ['discount','discount_type'])`:
validasi status + valid_until → buat `Coupon` (type standard, user_id = pengaju,
min_buy 0, fixed/percent, max_discount = total untuk percent) → quote approved + coupon_id.
Filament QuoteResource punya aksi "Setujui + buat kupon".

## Guest → akun

Guest cart disimpan di session (TIDAK ada kolom session_id di carts — keputusan sadar,
lihat RISIKO di ORDERS.md). `CartMergeService::mergeSessionFor($user)` dipanggil di
`LoginController@login` dan `RegisterController@register` setelah auth sukses.
Qty dijumlah + cap stok; baris 0-stok dilewati; session dihapus setelah merge.
Guest checkout TETAP butuh login (keputusan: hindari rework identitas order).
