<?php

/**
 * Kanal pembayaran storefront — BUKAN hardcode di controller.
 * fee: integer IDR per transaksi. gateway_format null = manual (tanpa gateway).
 * Ubah/ tambah kanal di sini atau via PaymentGatewayConfig (admin).
 */
return [
    'channels' => [
        ['code' => 'qris_auto', 'name' => 'QRIS (Otomatis)', 'bank' => 'QRIS', 'desc' => 'Scan semua e-wallet & m-banking, fee termurah', 'fee' => 0, 'gateway_format' => 'midtrans-snap'],
        ['code' => 'va_auto', 'name' => 'Virtual Account Bank', 'bank' => 'VA', 'desc' => 'BCA / Mandiri / BNI / BRI verifikasi otomatis', 'fee' => 4000, 'gateway_format' => 'midtrans-snap'],
        ['code' => 'gopay', 'name' => 'GoPay / OVO / DANA', 'bank' => 'EW', 'desc' => 'E-wallet instan', 'fee' => 0, 'gateway_format' => 'midtrans-snap'],
        ['code' => 'cod', 'name' => 'COD (Bayar di Tempat)', 'bank' => 'COD', 'desc' => 'Bayar saat barang diterima', 'fee' => 5000, 'gateway_format' => null],
        ['code' => 'manual', 'name' => 'Transfer Manual BCA', 'bank' => 'BCA', 'desc' => 'Upload bukti, verifikasi admin', 'fee' => 0, 'gateway_format' => null],
    ],
];
