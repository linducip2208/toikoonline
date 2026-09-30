<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\ClubPoint;
use App\Models\ClubPointDetail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentLog;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, $gatewayId)
    {
        $gateway = PaymentGatewayConfig::find($gatewayId);

        if (!$gateway || !$gateway->is_active) {
            Log::warning('Payment webhook: gateway not found or inactive', [
                'gateway_id' => $gatewayId,
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'gateway not found'], 404);
        }

        $payload = $request->all();

        try {
            $service = app(PaymentGatewayService::class);

            // Header-based signatures (Stripe/PayPal): expose headers + raw body
            // to verifyCallback via reserved keys. Never logged (see logAttempt).
            $payload['_headers'] = collect($request->headers->all())
                ->mapWithKeys(fn ($v, $k) => [strtolower($k) => $v[0] ?? ''])->all();
            $payload['_raw_body'] = $request->getContent();
            $payload = $this->normalizeGatewayPayload($gateway, $payload);

            if (!$service->verifyCallback($gateway, $payload)) {
                Log::warning('Payment webhook: signature verification failed', [
                    'gateway_id' => $gatewayId,
                    'order_id' => $payload['order_id'] ?? 'unknown',
                ]);
                $this->logAttempt(null, $gatewayId, false, null, $payload['transaction_status'] ?? $payload['status'] ?? null);
                if (!empty($payload['order_id'])) {
                    PaymentTransaction::where('order_code', $payload['order_id'])->increment('failed_signature_count');
                }
                return response()->json(['status' => 'invalid signature'], 403);
            }

            $orderCode = $payload['order_id'] ?? null;
            if (!$orderCode) {
                return response()->json(['status' => 'missing order_id'], 400);
            }

            $order = Order::where('code', $orderCode)->first();
            if (!$order) {
                Log::warning('Payment webhook: order not found', [
                    'order_code' => $orderCode,
                ]);
                return response()->json(['status' => 'order not found'], 404);
            }

            $transactionStatus = $payload['transaction_status'] ?? $payload['status'] ?? null;

            $paymentStatusMap = [
                'capture' => 'paid',
                'settlement' => 'paid',
                'success' => 'paid',
                'paid' => 'paid',
                'completed' => 'paid',
                'paid_off' => 'paid',
                'pending' => 'unpaid',
                'deny' => 'unpaid',
                'cancel' => 'unpaid',
                'expire' => 'unpaid',
                'expired' => 'unpaid',
                'failure' => 'unpaid',
                'failed' => 'unpaid',
                'refund' => 'refunded',
                'partial_refund' => 'refunded',
                'refunded' => 'refunded',
            ];

            $mappedStatus = $paymentStatusMap[$transactionStatus] ?? 'unpaid';

            $wasPaid = $order->payment_status === 'paid';
            $wasFailed = in_array($order->payment_status, ['failed'], true);
            $order->update([
                'payment_status' => $mappedStatus,
                'payment_details' => json_encode($payload),
            ]);

            // Idempotent payment_transactions row (intent may not exist for legacy orders)
            $txn = PaymentTransaction::firstOrCreate(
                ['idempotency_key' => (string) $order->code],
                [
                    'order_id' => $order->id,
                    'gateway_id' => $gateway->id,
                    'order_code' => (string) $order->code,
                    'amount' => (int) round((float) $order->grand_total),
                    'currency' => 'IDR',
                    'status' => 'pending',
                ]
            );
            $txnStatus = $mappedStatus === 'paid' ? 'paid' : ($mappedStatus === 'refunded' ? 'refunded' : 'pending');
            $txn->update([
                'order_id' => $order->id,
                'gateway_id' => $gateway->id,
                'status' => $txnStatus,
                'gateway_reference' => $payload['transaction_id'] ?? $txn->gateway_reference,
                'raw' => $payload,
            ]);
            $this->logAttempt($txn->id, $gatewayId, true, $txnStatus, $transactionStatus);

            // Efek samping saat pertama kali lunas (idempoten via $wasPaid)
            if ($mappedStatus === 'paid' && ! $wasPaid) {
                // 1. Catat pemakaian kupon
                if ($order->coupon_code) {
                    $coupon = Coupon::where('code', $order->coupon_code)->first();
                    if ($coupon) {
                        CouponUsage::firstOrCreate(['user_id' => $order->user_id, 'coupon_id' => $coupon->id]);
                    }
                }
                // 2. Poin loyalty: 1 poin per Rp10.000
                $points = (int) floor(((float) $order->grand_total) / 10000);
                if ($points > 0) {
                    $club = ClubPoint::firstOrCreate(['user_id' => $order->user_id], ['points' => 0, 'converted' => false]);
                    $detail = ClubPointDetail::firstOrCreate(
                        ['order_id' => $order->id],
                        ['club_point_id' => $club->id, 'user_id' => $order->user_id, 'points' => $points, 'converted' => false]
                    );
                    if ($detail->wasRecentlyCreated) {
                        $club->increment('points', $points);
                    }
                }
                // 3. Jejak awal pengiriman
                DeliveryHistory::firstOrCreate(
                    ['order_id' => $order->id, 'delivery_status' => 'confirmed'],
                    ['status' => 'Pembayaran diterima — pesanan dikonfirmasi', 'note' => 'Kode: '.$order->code]
                );
                // 4. Commit reserved stock (Agent 2 contract, guarded + non-fatal)
                if (class_exists(\App\Services\Inventory\InventoryService::class)
                    && method_exists(\App\Services\Inventory\InventoryService::class, 'commit')) {
                    try {
                        $inventory = app(\App\Services\Inventory\InventoryService::class);
                        $order->loadMissing('orderDetails');
                        foreach ($order->orderDetails as $detail) {
                            $stockId = null;
                            if ($detail->variation) {
                                $stockId = \App\Models\ProductStock::where('product_id', $detail->product_id)
                                    ->where('variant', $detail->variation)->value('id');
                            }
                            $inventory->commit((int) $detail->product_id, $stockId ? (int) $stockId : null, (int) $detail->quantity, 'order', $order->id);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Inventory commit failed (non-fatal)', ['order' => $order->code, 'error' => $e->getMessage()]);
                    }
                }
                // 5. Emit OrderPaid (listeners notify + mail + outbound webhook, guarded)
                if (class_exists(\App\Events\OrderPaid::class)) {
                    try {
                        event(new \App\Events\OrderPaid($order));
                    } catch (\Exception $e) {
                        Log::warning('OrderPaid dispatch failed (non-fatal)', ['order' => $order->code, 'error' => $e->getMessage()]);
                    }
                }
            }

            Log::info('Payment webhook: order updated', [
                'order_code' => $orderCode,
                'transaction_status' => $transactionStatus,
                'payment_status' => $mappedStatus,
            ]);

            // payment.failed: only on explicit failure signals, once (idempotent via $wasFailed/$wasPaid).
            $failedSignals = ['deny', 'cancel', 'expire', 'expired', 'failure', 'failed'];
            if (in_array((string) $transactionStatus, $failedSignals, true) && !$wasPaid && !$wasFailed) {
                $order->update(['payment_status' => 'failed']);
                if (class_exists(\App\Events\PaymentFailed::class)) {
                    try {
                        event(new \App\Events\PaymentFailed($order->fresh(), (string) $transactionStatus));
                    } catch (\Exception $e) {
                        Log::warning('PaymentFailed dispatch failed (non-fatal)', ['order' => $orderCode, 'error' => $e->getMessage()]);
                    }
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Payment webhook error: ' . $e->getMessage(), [
                'gateway_id' => $gatewayId,
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Normalize Stripe/PayPal webhook shapes into the shared
     * {order_id, transaction_status|status, transaction_id} envelope the
     * mapping table below understands. Other formats pass through.
     */
    protected function normalizeGatewayPayload(PaymentGatewayConfig $gateway, array $payload): array
    {
        $format = (string) $gateway->gateway_format;

        if ($format === 'stripe-pi' && isset($payload['type'])) {
            $obj = $payload['data']['object'] ?? [];
            $payload['order_id'] = $obj['metadata']['order_id'] ?? $payload['order_id'] ?? null;
            $payload['transaction_id'] = $obj['id'] ?? null;
            $payload['status'] = match ((string) $payload['type']) {
                'payment_intent.succeeded' => 'success',
                'payment_intent.payment_failed', 'payment_intent.canceled' => 'failed',
                default => 'pending',
            };
        }

        if ($format === 'paypal-order' && isset($payload['event_type'])) {
            $resource = $payload['resource'] ?? [];
            $payload['order_id'] = $resource['purchase_units'][0]['reference_id']
                ?? $resource['custom_id']
                ?? $resource['id']
                ?? $payload['order_id'] ?? null;
            $payload['transaction_id'] = $resource['id'] ?? null;
            $payload['status'] = match ((string) $payload['event_type']) {
                'CHECKOUT.ORDER.COMPLETED', 'PAYMENT.CAPTURE.COMPLETED' => 'success',
                'PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.REFUNDED' => 'failed',
                default => 'pending',
            };
        }

        return $payload;
    }

    /**
     * Append-only webhook audit row. Never stores secrets: only validity
     * flags and status strings. Payload itself is stored on the
     * payment_transactions row, not here.
     */
    protected function logAttempt(?int $transactionId, mixed $gatewayId, bool $signatureValid, ?string $mapped, ?string $rawStatus): void
    {
        try {
            PaymentLog::create([
                'payment_transaction_id' => $transactionId,
                'gateway_id_raw' => (string) $gatewayId,
                'event' => 'webhook',
                'signature_valid' => $signatureValid,
                'mapped_status' => $mapped,
                'gateway_status_raw' => $rawStatus ? substr((string) $rawStatus, 0, 64) : null,
            ]);
        } catch (\Exception) {
            // audit must never break the webhook response
        }
    }
}
