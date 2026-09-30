<?php

namespace App\Observers;

use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Services\Order\OrderStateService;

/**
 * Setiap perubahan resi / status kirim otomatis jadi timeline
 * yang tampil di /account/orders/{order} (Lacak Pengiriman).
 *
 * Label terpusat di OrderStateService::deliveryLabel(). Observer ini hanya
 * menulis baris yang BELUM ditulis state service (dedupe 60 detik) sehingga
 * update langsung via $order->update() tetap tercatat tanpa dobel.
 */
class OrderObserver
{
    public function updated(Order $order): void
    {
        $states = app(OrderStateService::class);

        // Resi diisi / diganti admin
        if ($order->wasChanged('tracking_number') && $order->tracking_number) {
            $this->recordOnce($order, 'on_delivery', 'Paket dikirim — resi '.strtoupper($order->courier ?? '').' '.$order->tracking_number, 'Lacak berkala di halaman ini.');
            if (in_array($order->delivery_status, ['pending', 'confirmed'], true)) {
                $order->forceFill(['delivery_status' => 'on_delivery'])->saveQuietly();
            }
        }

        // Status kirim berubah manual (di luar OrderStateService)
        if ($order->wasChanged('delivery_status')) {
            $this->recordOnce($order, (string) $order->delivery_status, $states->deliveryLabel((string) $order->delivery_status));
        }
    }

    protected function recordOnce(Order $order, string $deliveryStatus, string $status, ?string $note = null): void
    {
        $recent = DeliveryHistory::where('order_id', $order->id)
            ->where('delivery_status', $deliveryStatus)
            ->where('created_at', '>=', now()->subMinute())
            ->exists();

        if ($recent) {
            return;
        }

        DeliveryHistory::create([
            'order_id' => $order->id,
            'delivery_status' => $deliveryStatus,
            'status' => $status,
            'note' => $note,
        ]);
    }
}
