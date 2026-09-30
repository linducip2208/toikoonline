<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Address;
use App\Models\PaymentGatewayConfig;
use App\Services\CouponService;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $addresses = Address::where('user_id', Auth::id())->get();
        $defaultAddress = $addresses->where('set_default', true)->first();
        $billingAddress = $addresses->where('set_billing', true)->first();

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $totalTax = $cartItems->sum(fn($item) => $item->tax * $item->quantity);
        $totalShipping = $cartItems->sum(fn($item) => $item->shipping_cost * $item->quantity);
        $grandTotal = $subtotal + $totalTax + $totalShipping;

        // Data nyata untuk wizard checkout (dulu dummy Alpine)
        $items = $cartItems->map(fn($item) => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'name' => $item->product->name ?? 'Produk',
            'variant' => $item->variation,
            'price' => (int) $item->price,
            'qty' => (int) $item->quantity,
            'image' => $item->product->thumbnail_img ? asset($item->product->thumbnail_img) : null,
        ])->values();
        $totalWeight = $cartItems->sum(fn($item) => (($item->product->weight ?? 500)) * $item->quantity);
        $addressList = $addresses->map(fn($a) => [
            'id' => $a->id,
            'label' => trim(($a->address ?? '').' '.($a->city->name ?? '').' '.$a->postal_code),
            'full' => trim(($a->address ?? '').', '.($a->city->name ?? '').' '.$a->postal_code.' — '.$a->phone),
            'name' => Auth::user()->name ?? '',
            'phone' => $a->phone ?? '',
            'address' => $a->address ?? '',
            'city' => $a->city->name ?? '',
            'postal' => $a->postal_code ?? '',
            'area_id' => $a->area_id,
            'set_default' => (bool) $a->set_default,
        ])->values();

        // Gudang asal untuk hitung ongkir (BusinessSetting, fallback .env)
        $warehouse = [
            'city_id' => BusinessSetting::getValue('warehouse_city_id', env('SHOP_ORIGIN_CITY_ID', '')),
            'area_id' => BusinessSetting::getValue('warehouse_area_id', env('SHOP_ORIGIN_AREA_ID', '')),
        ];

        // Gateway aktif untuk step pembayaran (channel: QRIS / VA / GoPay / COD)
        // Kategori channel agar user tidak bingung pilih 10 gateway sekaligus.
        $gateways = PaymentGatewayConfig::active()->orderBy('sort_order')->get();
        $paymentChannels = [
            ['code' => 'qris_auto', 'name' => 'QRIS (Otomatis)', 'bank' => 'QRIS', 'desc' => 'Scan semua e-wallet & m-banking, fee termurah', 'fee' => 0, 'gateway_format' => 'midtrans-snap'],
            ['code' => 'va_auto', 'name' => 'Virtual Account Bank', 'bank' => 'VA', 'desc' => 'BCA / Mandiri / BNI / BRI verifikasi otomatis', 'fee' => 4000, 'gateway_format' => 'midtrans-snap'],
            ['code' => 'gopay', 'name' => 'GoPay / OVO / DANA', 'bank' => 'EW', 'desc' => 'E-wallet instan', 'fee' => 0, 'gateway_format' => 'midtrans-snap'],
            ['code' => 'cod', 'name' => 'COD (Bayar di Tempat)', 'bank' => 'COD', 'desc' => 'Bayar saat barang diterima', 'fee' => 5000, 'gateway_format' => null],
            ['code' => 'manual', 'name' => 'Transfer Manual BCA', 'bank' => 'BCA', 'desc' => 'Upload bukti, verifikasi admin', 'fee' => 0, 'gateway_format' => null],
        ];

        return view('storefront.checkout', compact(
            'cartItems', 'addresses', 'defaultAddress', 'billingAddress',
            'subtotal', 'totalTax', 'totalShipping', 'grandTotal',
            'gateways', 'paymentChannels', 'items', 'totalWeight', 'addressList', 'warehouse'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:100',
            'shipping_address.phone' => 'required|string|max:20',
            'billing_address' => 'nullable|array',
            'payment_type' => 'required|string',
            'shipping_method' => 'nullable|string|max:100',
            'shipping_cost' => 'nullable|numeric|min:0|max:10000000',
            'destination_area_id' => 'nullable|string|max:50',
            'courier' => 'nullable|string|max:30',
            'coupon_code' => 'nullable|string|max:50',
            'additional_info' => 'nullable|string',
        ]);

        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $totalTax = $cartItems->sum(fn($item) => $item->tax * $item->quantity);
        // Ongkir dari pilihan kurir di step pengiriman (bukan lagi 0 / dummy)
        $shippingCost = (int) $request->input('shipping_cost', 0);

        // Kupon divalidasi ulang di server (jangan percaya angka dari client)
        $couponResult = app(CouponService::class)->apply($request->input('coupon_code'), Auth::id(), (float) $subtotal);
        if ($request->input('coupon_code') && $couponResult['error']) {
            return back()->withInput()->with('error', $couponResult['error']);
        }
        $couponDiscount = (int) $couponResult['discount'];

        $grandTotal = max(0, $subtotal + $totalTax + $shippingCost - $couponDiscount);

        $order = Order::create([
            'user_id' => Auth::id(),
            'shipping_address' => json_encode($request->input('shipping_address')),
            'billing_address' => json_encode($request->input('billing_address')),
            'shipping_method' => $request->input('shipping_method'),
            'courier' => $request->input('courier'),
            'additional_info' => $request->input('additional_info'),
            'payment_type' => $request->input('payment_type'),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'code' => date('Ymd') . '-' . Str::upper(Str::random(6)),
            'date' => now()->timestamp,
            'grand_total' => $grandTotal,
            'tax_amount' => $totalTax,
            'shipping_cost' => $shippingCost,
            'order_from' => 'web',
            'view' => false,
            'delivery_viewed' => false,
            'payment_status_viewed' => false,
            'commission_calculated' => false,
            'manual_payment' => in_array($request->input('payment_type'), ['cod', 'manual']),
            'coupon_code' => $couponResult['coupon']?->code,
            'coupon_discount' => $couponDiscount,
            'discount' => 0,
        ]);

        foreach ($cartItems as $cartItem) {
            OrderDetail::create([
                'order_id' => $order->id,
                'seller_id' => $cartItem->product->user_id,
                'product_id' => $cartItem->product_id,
                'variation' => $cartItem->variation,
                'price' => $cartItem->price,
                'tax' => $cartItem->tax,
                'shipping_cost' => $cartItem->shipping_cost,
                'quantity' => $cartItem->quantity,
                'payment_status' => 'unpaid',
                'delivery_status' => 'pending',
                'shipping_type' => $request->input('shipping_method', 'home_delivery'),
            ]);

            $cartItem->product->increment('num_of_sale', $cartItem->quantity);
        }

        Cart::where('user_id', Auth::id())->delete();

        // Buat transaksi payment gateway (Midtrans Snap prioritas, fallback manual/COD tetap sukses)
        $snapToken = null;
        $snapRedirect = null;
        try {
            $gateway = PaymentGatewayConfig::active()->orderBy('sort_order')->first();
            $isOnlineChannel = ! in_array($request->input('payment_type'), ['cod', 'manual', 'wallet']);
            if ($gateway && $isOnlineChannel) {
                $service = app(PaymentGatewayService::class);
                $items = $order->orderDetails->map(fn($d) => [
                    'id' => (string) $d->product_id,
                    'price' => (int) $d->price,
                    'quantity' => (int) $d->quantity,
                    'name' => substr($d->product->name ?? 'Produk', 0, 50),
                ])->all();
                $result = $service->createPayment($gateway, [
                    'order_id' => $order->code,
                    'amount' => (int) $grandTotal,
                    'customer' => [
                        'first_name' => Auth::user()->name ?? 'Pelanggan',
                        'email' => Auth::user()->email ?? '',
                    ],
                    'items' => $items,
                    'channels' => $request->input('payment_channel') ? [$request->input('payment_channel')] : [],
                ]);
                if (! empty($result['success'])) {
                    $snapToken = $result['token'] ?? null;
                    $snapRedirect = $result['redirect_url'] ?? null;
                    $order->update(['payment_details' => json_encode($result['raw'] ?? $result)]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Payment create failed, lanjut manual: '.$e->getMessage(), ['order' => $order->code]);
        }

        if ($snapRedirect) {
            return redirect()->away($snapRedirect);
        }

        return redirect()->route('checkout.success', $order)->with('snap_token', $snapToken);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderDetails.product');

        $snapToken = session('snap_token');

        return view('storefront.checkout-success', compact('order', 'snapToken'));
    }
}
