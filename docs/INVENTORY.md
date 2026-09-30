# Inventory — Reservation Contract

`InventoryService::reserve($productId, $stockId, $qty, $refType, $refId)` dan
`::release(...)` SUDAH ADA (row-lock, movement audit) — tidak diubah signature-nya.

## Call sites (milik Agent 2)

- RESERVE: `CheckoutController@store` — per baris `validate()['lines']` setelah
  Order dibuat. `$stockId` = product_stocks row qty terbesar untuk product/variant
  (produk digital tidak di-reserve). Gagal reserve → release semua yang sudah
  ter-reserve + hapus order + hapus detail → user coba lagi.
- RELEASE: `Customer\OrderController@cancel` — per order_detail saat pelanggan
  membatalkan (pending/confirmed + unpaid). Best-effort per baris (try/catch).
- RECEIVE (restock): `Refund\RefundService::approve` — tidak berubah.

## Catatan

- Reserve memakai SATU stock row per baris (qty terbesar), bukan split multi-row.
- Tidak ada kolom reserved_qty; stok fisik berkurang saat reserve (kebijakan existing).
- Cancel admin / gagal bayar: lewat OrderStateService ke cancelled (release manual
  menyusul bila dibutuhkan — lihat RISIKO di ORDERS.md).
