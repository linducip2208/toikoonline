# Orders — State Machine, Cancel, MONEY POLICY

## MONEY POLICY (integer-IDR)

- Kolom DB legacy tetap `decimal(20,2)` — TIDAK ada migrasi kolom (kompat SQLite/MySQL,
  hindari alter berisiko di tabel besar).
- Semua matematika BARU memakai integer IDR; `round()` sebelum persist ke kolom decimal.
- `price_list_items.price`, `quote_items.price` memakai integer (unsignedBigInteger).
- `Order.discount` = diskon grup pelanggan (integer IDR, persen × subtotal, max subtotal,
  tidak pernah negatif). `coupon_discount` = kupon. `grand_total = max(0, subtotal + tax + shipping - coupon - group)`.

## State machine

`App\Services\Order\OrderStateService::transition($order, $paymentTo=null, $deliveryTo=null, $actorId=null, $note=null)`:
- Map di `config/order_statuses.php` → `transitions.payment/delivery`.
- payment: unpaid→paid/failed; paid→refunded/partially_refunded; partially_refunded→refunded; failed→paid/unpaid.
- delivery: pending→confirmed/on_delivery/cancelled/failed; confirmed→packed/picked_up/on_delivery/cancelled/failed;
  packed→picked_up/cancelled; picked_up→on_delivery/cancelled; on_delivery→delivered/failed/returned;
  delivered→completed/returned. (cabang legacy confirmed→picked_up & pending/confirmed→on_delivery dipertahankan.)
- Ilegal → `ValidationException`. Setiap transisi menulis `OrderNote` internal;
  perubahan delivery juga menulis `DeliveryHistory` (label via `deliveryLabel()`).
- Helper: `transitionPayment()`, `transitionDelivery()`, `canPayment()`, `canDelivery()`.

## Refactor tanpa ubah perilaku valid

- `OrderObserver@updated`: label via state service + dedupe 60 detik (state service sudah
  menulis history; observer hanya menambal update langsung). Tracking branch dipertahankan.
- `Refund\RefundService::approve`: transisi payment via state service (refunded/partially_refunded).
  Baris `refund_approved` di delivery_histories informasional, di luar guarded map.
- `Shipment::syncToOrder`: tidak berubah (history `shipment_*` informasional, di luar map).
- `Customer\OrderController@receive`: via `transitionDelivery(..., 'delivered')`.

## Cancel pelanggan

`Customer\OrderController@cancel` — syarat: milik sendiri + payment unpaid +
delivery pending/confirmed. Release stok per baris lalu transisi ke cancelled.
BUTUH ROUTE (integrator, routes/web.php forbidden bagi saya):
`Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')`
di grup `account` + auth.

## Guest checkout

DITUNDA eksplisit: guest cart persist di session + merge saat login; checkout tetap auth-required
(keputusan sadar agar tidak rework identitas/ownership order).

## RISIKO / follow-up

1. Reserve satu stock-row per baris — order multi-gudang besar bisa gagal parsial (rollback + retry).
2. Cancel dari admin/Filament belum release stok otomatis (hanya cancel pelanggan).
3. Webhook pembayaran yang set payment_status langsung harus migrasi ke OrderStateService.
4. `carts.session_id` TIDAK dibuat — guest cart = session, bukan DB (lihat ECOMMERCE.md).
