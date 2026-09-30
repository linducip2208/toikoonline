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

            if (!$service->verifyCallback($gateway, $payload)) {
                Log::warning('Payment webhook: signature verification failed', [
                    'gateway_id' => $gatewayId,
                    'order_id' => $payload['order_id'] ?? 'unknown',
                ]);
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
                'pending' => 'unpaid',
                'deny' => 'unpaid',
                'cancel' => 'unpaid',
                'expire' => 'unpaid',
                'failure' => 'unpaid',
                'refund' => 'refunded',
                'partial_refund' => 'refunded',
            ];

            $mappedStatus = $paymentStatusMap[$transactionStatus] ?? 'unpaid';

            $wasPaid = $order->payment_status === 'paid';
            $order->update([
                'payment_status' => $mappedStatus,
                'payment_details' => json_encode($payload),
            ]);

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
            }

            Log::info('Payment webhook: order updated', [
                'order_code' => $orderCode,
                'transaction_status' => $transactionStatus,
                'payment_status' => $mappedStatus,
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Payment webhook error: ' . $e->getMessage(), [
                'gateway_id' => $gatewayId,
                'payload' => $payload,
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
