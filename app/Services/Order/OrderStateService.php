<?php

namespace App\Services\Order;

use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\OrderNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Guarded state machine for payment_status + delivery_status.
 * Transition map lives in config/order_statuses.php under 'transitions'.
 * Every transition writes an internal OrderNote; delivery changes also
 * write a DeliveryHistory row. Illegal jumps throw ValidationException.
 */
class OrderStateService
{
    public function paymentMap(): array
    {
        return config('order_statuses.transitions.payment', []);
    }

    public function deliveryMap(): array
    {
        return config('order_statuses.transitions.delivery', []);
    }

    public function canPayment(string $from, string $to): bool
    {
        return in_array($to, $this->paymentMap()[$from] ?? [], true);
    }

    public function canDelivery(string $from, string $to): bool
    {
        return in_array($to, $this->deliveryMap()[$from] ?? [], true);
    }

    /**
     * @param Order $order
     * @param string|null $paymentTo
     * @param string|null $deliveryTo
     */
    public function transition(Order $order, ?string $paymentTo = null, ?string $deliveryTo = null, ?int $actorId = null, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $paymentTo, $deliveryTo, $actorId, $note) {
            $fromPayment = $order->payment_status;
            $fromDelivery = $order->delivery_status;

            if ($paymentTo !== null && $paymentTo !== $fromPayment) {
                if (! $this->canPayment((string) $fromPayment, $paymentTo)) {
                    throw ValidationException::withMessages([
                        'payment_status' => 'Transisi pembayaran tidak valid: '.$fromPayment.' → '.$paymentTo.'.',
                    ]);
                }
                $order->payment_status = $paymentTo;
            }

            if ($deliveryTo !== null && $deliveryTo !== $fromDelivery) {
                if (! $this->canDelivery((string) $fromDelivery, $deliveryTo)) {
                    throw ValidationException::withMessages([
                        'delivery_status' => 'Transisi pengiriman tidak valid: '.$fromDelivery.' → '.$deliveryTo.'.',
                    ]);
                }
                $order->delivery_status = $deliveryTo;
            }

            $order->save();

            $actorId = $actorId ?? auth()->id();

            if ($paymentTo !== null && $paymentTo !== $fromPayment) {
                OrderNote::create([
                    'order_id' => $order->id,
                    'user_id' => $actorId,
                    'note' => 'Pembayaran: '.$fromPayment.' → '.$paymentTo.($note ? ' — '.$note : ''),
                    'is_internal' => true,
                ]);
            }

            if ($deliveryTo !== null && $deliveryTo !== $fromDelivery) {
                OrderNote::create([
                    'order_id' => $order->id,
                    'user_id' => $actorId,
                    'note' => 'Pengiriman: '.$fromDelivery.' → '.$deliveryTo.($note ? ' — '.$note : ''),
                    'is_internal' => true,
                ]);
                DeliveryHistory::create([
                    'order_id' => $order->id,
                    'delivery_boy_id' => null,
                    'delivery_status' => $deliveryTo,
                    'status' => $this->deliveryLabel($deliveryTo),
                    'note' => $note,
                ]);
            }

            return $order->fresh();
        });
    }

    public function transitionPayment(Order $order, string $to, ?int $actorId = null, ?string $note = null): Order
    {
        return $this->transition($order, $to, null, $actorId, $note);
    }

    public function transitionDelivery(Order $order, string $to, ?int $actorId = null, ?string $note = null): Order
    {
        return $this->transition($order, null, $to, $actorId, $note);
    }

    public function deliveryLabel(string $status): string
    {
        $labels = [
            'pending' => 'Pesanan menunggu konfirmasi',
            'confirmed' => 'Pesanan dikonfirmasi penjual',
            'packed' => 'Paket dikemas',
            'picked_up' => 'Paket diambil kurir',
            'on_delivery' => 'Paket dalam perjalanan',
            'delivered' => 'Paket diterima — terima kasih!',
            'completed' => 'Pesanan selesai',
            'cancelled' => 'Pengiriman dibatalkan',
            'failed' => 'Pengiriman gagal',
            'returned' => 'Paket diretur',
        ];

        return $labels[$status] ?? $status;
    }
}
