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
        'packed' => ['id' => 'Dikemas', 'en' => 'Packed'],
        'picked_up' => ['id' => 'Diambil', 'en' => 'Picked up'],
        'on_delivery' => ['id' => 'Dalam Pengiriman', 'en' => 'On delivery'],
        'delivered' => ['id' => 'Terkirim', 'en' => 'Delivered'],
        'completed' => ['id' => 'Selesai', 'en' => 'Completed'],
        'cancelled' => ['id' => 'Dibatalkan', 'en' => 'Cancelled'],
        'failed' => ['id' => 'Gagal', 'en' => 'Failed'],
        'returned' => ['id' => 'Diretur', 'en' => 'Returned'],
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
    // Guarded transition map — consumed by App\Services\Order\OrderStateService.
    // Legacy jumps kept valid: confirmed→picked_up, pending/confirmed→on_delivery.
    'transitions' => [
        'payment' => [
            'unpaid' => ['paid', 'failed'],
            'paid' => ['refunded', 'partially_refunded'],
            'partially_refunded' => ['refunded'],
            'failed' => ['paid', 'unpaid'],
            'refunded' => [],
        ],
        'delivery' => [
            'pending' => ['confirmed', 'on_delivery', 'cancelled', 'failed'],
            'confirmed' => ['packed', 'picked_up', 'on_delivery', 'cancelled', 'failed'],
            'packed' => ['picked_up', 'cancelled'],
            'picked_up' => ['on_delivery', 'cancelled'],
            'on_delivery' => ['delivered', 'failed', 'returned'],
            'delivered' => ['completed', 'returned'],
            'completed' => [],
            'cancelled' => [],
            'failed' => [],
            'returned' => [],
        ],
    ],
];
