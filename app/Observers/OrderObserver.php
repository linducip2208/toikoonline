<?php

namespace App\Observers;

use App\Models\DeliveryHistory;
use App\Models\Order;

/**
 * Setiap perubahan resi / status kirim otomatis jadi timeline
 * yang tampil di /account/orders/{order} (Lacak Pengiriman).
 */
class OrderObserver
{
    public function updated(Order $order): void
    {
        // Resi diisi / diganti admin
        if ($order->wasChanged('tracking_number') && $order->tracking_number) {
            DeliveryHistory::create([
                'order_id' => $order->id,
                'delivery_status' => 'on_delivery',
                'status' => 'Paket dikirim — resi '.strtoupper($order->courier ?? '').' '.$order->tracking_number,
                'note' => 'Lacak berkala di halaman ini.',
            ]);
            if ($order->delivery_status === 'pending' || $order->delivery_status === 'confirmed') {
                $order->forceFill(['delivery_status' => 'on_delivery'])->saveQuietly();
            }
        }

        // Status kirim berubah manual
        if ($order->wasChanged('delivery_status')) {
            $labels = [
                'confirmed' => 'Pesanan dikonfirmasi penjual',
                'picked_up' => 'Paket diambil kurir',
                'on_delivery' => 'Paket dalam perjalanan',
                'delivered' => 'Paket diterima — terima kasih!',
                'cancelled' => 'Pengiriman dibatalkan',
            ];
            DeliveryHistory::create([
                'order_id' => $order->id,
                'delivery_status' => $order->delivery_status,
                'status' => $labels[$order->delivery_status] ?? $order->delivery_status,
            ]);
        }
    }
}
