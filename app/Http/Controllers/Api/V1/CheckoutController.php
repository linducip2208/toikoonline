<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Requests\Api\V1\ShippingQuoteRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentGatewayConfig;
use App\Services\CouponService;
use App\Services\Payment\PaymentGatewayService;
use App\Services\Shipping\ShippingManager;
use App\Services\Shipping\ShippingMethodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Minimal checkout validation is duplicated inline here (Agent 2 owns
 * CheckoutService for web). Convergence: both use CouponService::apply
 * and PaymentGatewayService::createIntent; unify on CheckoutService when
 * the web flow stabilizes.
 */
class CheckoutController extends Controller
{
    public function quote(ShippingQuoteRequest $request, ShippingManager $manager, ShippingMethodService $tables): JsonResponse
    {
        $origin = $request->input('origin') ?: $manager->defaultOrigin();

        if (!$origin) {
            return response()->json(['success' => false, 'message' => 'Store origin not configured.'], 422);
        }

        $rates = $manager->cachedQuote($origin, (string) $request->destination, (int) $request->weight, (string) $request->input('couriers', ''));

        // Table-rate zones merged with live provider quotes (sorted by cost).
        $rates = $tables->mergeWithLive($rates, [
            'city' => (string) $request->destination,
            'postcode' => (string) $request->input('postcode', ''),
            'state' => (string) $request->input('state', ''),
            'country' => (string) $request->input('country', 'ID'),
        ], (int) $request->weight, (int) $request->input('subtotal', 0));

        return response()->json([
            'success' => true,
            'data' => $rates,
            'pickup_points' => $manager->pickupPoints(),
        ]);
    }

    public function place(CheckoutRequest $request, PaymentGatewayService $payments, CouponService $coupons): JsonResponse
    {
        $user = $request->user();
        $items = Cart::with('product')->where('user_id', $user->id)->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty.'], 422);
        }

        $gateway = PaymentGatewayConfig::where('is_active', true)->findOrFail($request->payment_gateway_id);

        $subtotal = (int) round($items->sum(fn ($i) => (float) $i->price * (int) $i->quantity));
        $shipping = (int) ($request->input('shipping_cost', 0));

        $applied = $coupons->apply($request->coupon_code, $user->id, $subtotal);
        if ($request->coupon_code && $applied['error']) {
            return response()->json(['success' => false, 'message' => $applied['error']], 422);
        }
        $discount = (int) round($applied['discount']);

        $grandTotal = max(0, $subtotal - $discount + $shipping);

        $order = DB::transaction(function () use ($request, $user, $items, $subtotal, $shipping, $discount, $grandTotal, $gateway) {
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'additional_info' => $request->additional_info,
                'shipping_method' => $request->shipping_method,
                'delivery_status' => 'pending',
                'payment_type' => $gateway->gateway_format,
                'payment_status' => 'unpaid',
                'code' => 'ORD-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -5)),
                'date' => now()->timestamp,
                'coupon_discount' => $discount,
                'grand_total' => $grandTotal,
                'coupon_code' => $request->coupon_code,
                'shipping_cost' => $shipping,
                'courier' => $request->courier,
                'pickup_point_id' => $request->pickup_point_id,
                'order_from' => 'api',
            ]);

            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'seller_id' => $item->product?->user_id,
                    'product_id' => $item->product_id,
                    'variation' => $item->variation,
                    'price' => $item->price,
                    'tax' => 0,
                    'shipping_cost' => 0,
                    'quantity' => $item->quantity,
                    'payment_status' => 'unpaid',
                    'delivery_status' => 'pending',
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            return $order;
        });

        $intent = $payments->createIntent($gateway, $order->id, $grandTotal);

        if (class_exists(\App\Events\OrderCreated::class)) {
            try {
                event(new \App\Events\OrderCreated($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('OrderCreated dispatch failed (non-fatal)', ['order' => $order->code, 'error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => (new OrderResource($order))->resolve(),
            'payment' => [
                'redirect_url' => $intent['redirect_url'] ?? null,
                'token' => $intent['token'] ?? null,
                'status' => $intent['success'] ?? false ? 'pending' : 'failed',
            ],
        ], 201);
    }

    public function paymentStatus(string $code, PaymentGatewayService $payments): JsonResponse
    {
        $order = Order::where('code', $code)->where('user_id', request()->user()->id)->firstOrFail();
        $txn = \App\Models\PaymentTransaction::where('order_code', $order->code)->latest()->first();

        if ($txn && in_array($txn->status, ['intent', 'pending'], true) && $txn->gateway) {
            $payments->reconcileTransaction($txn);
            $order->refresh();
        }

        return response()->json(['success' => true, 'data' => [
            'order_code' => $order->code,
            'payment_status' => $order->payment_status,
            'transaction_status' => $txn?->status,
            'redirect_url' => $txn?->redirect_url,
        ]]);
    }
}
