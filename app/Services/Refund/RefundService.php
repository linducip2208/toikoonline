<?php

namespace App\Services\Refund;

use App\Models\DeliveryHistory;
use App\Models\OrderNote;
use App\Models\RefundRequest;
use App\Services\Inventory\InventoryService;
use App\Services\Order\OrderStateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    public function __construct(protected InventoryService $inventory, protected OrderStateService $states) {}

    public function approve(RefundRequest $refund, ?int $staffId = null, ?string $note = null): RefundRequest
    {
        return DB::transaction(function () use ($refund, $staffId, $note) {
            $refund->loadMissing(['order', 'orderDetail.product', 'orderDetail.product.stocks']);

            if (! in_array($refund->refund_status, ['pending'], true)) {
                throw ValidationException::withMessages([
                    'refund_status' => __('commerce.refund_invalid_state'),
                ]);
            }

            $detail = $refund->orderDetail;
            $amount = (int) ($refund->refund_amount ?? (($detail?->price ?? 0) * ($detail?->quantity ?? 0)));

            $refund->update([
                'refund_status' => 'approved',
                'refund_staff_id' => $staffId ?? auth()->id(),
            ]);

            if ($detail && $detail->product_id) {
                $stockId = $detail->product->stocks
                    ->when($detail->variation, fn ($c) => $c->where('variant', $detail->variation))
                    ->first()?->id;

                if ($stockId) {
                    $this->inventory->receive(
                        $detail->product_id,
                        $stockId,
                        (int) $detail->quantity,
                        'refund_request',
                        $refund->id,
                        'Restock from approved refund #'.$refund->id
                    );
                }
            }

            $order = $refund->order;
            if ($order) {
                $totalRefunded = (int) RefundRequest::where('order_id', $order->id)
                    ->whereIn('refund_status', ['approved', 'refunded'])
                    ->sum('refund_amount');
                $orderTotal = (int) $order->grand_total;

                $target = $totalRefunded >= $orderTotal && $orderTotal > 0
                    ? 'refunded'
                    : 'partially_refunded';

                // Guarded payment transition (audit OrderNote written by state service).
                if ($order->payment_status !== $target) {
                    $this->states->transitionPayment($order->fresh(), $target, $staffId ?? auth()->id(), 'Refund #'.$refund->id.' disetujui');
                    $order = $order->fresh();
                }

                DeliveryHistory::create([
                    'order_id' => $order->id,
                    'order_detail_id' => $detail?->id,
                    'delivery_boy_id' => null,
                    'delivery_status' => 'refund_approved',
                    'status' => 'Refund disetujui (Rp '.number_format($amount, 0, ',', '.').')',
                    'note' => $note,
                ]);

                OrderNote::create([
                    'order_id' => $order->id,
                    'user_id' => $staffId ?? auth()->id(),
                    'note' => 'Refund #'.$refund->id.' disetujui: Rp '.number_format($amount, 0, ',', '.').($note ? ' — '.$note : ''),
                    'is_internal' => true,
                ]);
            }

            $refund->update(['refund_status' => 'refunded']);

            return $refund->fresh();
        });
    }

    public function reject(RefundRequest $refund, string $reason, ?int $staffId = null): RefundRequest
    {
        if (! in_array($refund->refund_status, ['pending'], true)) {
            throw ValidationException::withMessages([
                'refund_status' => __('commerce.refund_invalid_state'),
            ]);
        }

        $refund->update([
            'refund_status' => 'rejected',
            'reject_reason' => $reason,
            'refund_staff_id' => $staffId ?? auth()->id(),
        ]);

        if ($refund->order_id) {
            OrderNote::create([
                'order_id' => $refund->order_id,
                'user_id' => $staffId ?? auth()->id(),
                'note' => 'Refund #'.$refund->id.' ditolak: '.$reason,
                'is_internal' => true,
            ]);
        }

        return $refund->fresh();
    }
}
