<?php

return [
    'payment' => [
        'unpaid' => ['id' => 'Belum Dibayar', 'en' => 'Unpaid'],
        'paid' => ['id' => 'Dibayar', 'en' => 'Paid'],
        'refunded' => ['id' => 'Direfund', 'en' => 'Refunded'],
        'partially_refunded' => ['id' => 'Direfund Sebagian', 'en' => 'Partially refunded'],
        'failed' => ['id' => 'Gagal', 'en' => 'Failed'],
    ],
    'delivery' => [
        'pending' => ['id' => 'Menunggu', 'en' => 'Pending'],
        'confirmed' => ['id' => 'Dikonfirmasi', 'en' => 'Confirmed'],
        'picked_up' => ['id' => 'Diambil', 'en' => 'Picked up'],
        'on_delivery' => ['id' => 'Dalam Pengiriman', 'en' => 'On delivery'],
        'delivered' => ['id' => 'Terkirim', 'en' => 'Delivered'],
        'cancelled' => ['id' => 'Dibatalkan', 'en' => 'Cancelled'],
    ],
    'purchase_order' => [
        'draft' => ['id' => 'Draf', 'en' => 'Draft'],
        'ordered' => ['id' => 'Dipesan', 'en' => 'Ordered'],
        'partially_received' => ['id' => 'Diterima Sebagian', 'en' => 'Partially received'],
        'received' => ['id' => 'Diterima', 'en' => 'Received'],
        'cancelled' => ['id' => 'Dibatalkan', 'en' => 'Cancelled'],
    ],
    'shipment' => [
        'packed' => ['id' => 'Dikemas', 'en' => 'Packed'],
        'shipped' => ['id' => 'Dikirim', 'en' => 'Shipped'],
        'delivered' => ['id' => 'Terkirim', 'en' => 'Delivered'],
    ],
    'refund' => [
        'pending' => ['id' => 'Menunggu', 'en' => 'Pending'],
        'approved' => ['id' => 'Disetujui', 'en' => 'Approved'],
        'refunded' => ['id' => 'Direfund', 'en' => 'Refunded'],
        'rejected' => ['id' => 'Ditolak', 'en' => 'Rejected'],
    ],
    'return_reasons' => [
        'damaged' => ['id' => 'Barang rusak/cacat', 'en' => 'Damaged/defective item'],
        'wrong_item' => ['id' => 'Barang salah / tidak sesuai', 'en' => 'Wrong item received'],
        'not_as_described' => ['id' => 'Tidak sesuai deskripsi', 'en' => 'Not as described'],
        'late_delivery' => ['id' => 'Terlambat tiba', 'en' => 'Late delivery'],
        'changed_mind' => ['id' => 'Berubah pikiran', 'en' => 'Changed mind'],
        'other' => ['id' => 'Lainnya', 'en' => 'Other'],
    ],
];
