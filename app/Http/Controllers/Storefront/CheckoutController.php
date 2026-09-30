<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Address;
use App\Models\PaymentGatewayConfig;
use App\Services\Checkout\CheckoutService;
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

        $subtotal = (int) round($cartItems->sum(fn($item) => (float) $item->price * (int) $item->quantity));
        $totalTax = (int) round($cartItems->sum(fn($item) => (float) $item->tax * (int) $item->quantity));
        $totalShipping = (int) round($cartItems->sum(fn($item) => (float) $item->shipping_cost * (int) $item->quantity));
        $grandTotal = max(0, $subtotal + $totalTax + $totalShipping);

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
        // Definisi kanal di config/payment.php (bukan hardcode).
        $gateways = PaymentGatewayConfig::active()->orderBy('sort_order')->get();
        $paymentChannels = config('payment.channels', []);

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

        // Hardening: revalidasi harga dari products + price-list perusahaan +
        // ketersediaan stok SEBELUM order dibuat.
        try {
            $checked = app(CheckoutService::class)->validate(
                $cartItems,
                Auth::user(),
                (array) $request->input('shipping_address', [])
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->with('error', implode(' ', $e->validator->errors()->all()));
        }

        $subtotal = (int) $checked['subtotal'];
        $totalTax = (int) $checked['tax'];
        // Ongkir dari pilihan kurir di step pengiriman (bukan lagi 0 / dummy)
        $shippingCost = (int) round((float) $request->input('shipping_cost', 0));

        // Kupon divalidasi ulang di server (jangan percaya angka dari client)
        $couponResult = app(CouponService::class)->apply($request->input('coupon_code'), Auth::id(), (float) $subtotal);
        if ($request->input('coupon_code') && $couponResult['error']) {
            return back()->withInput()->with('error', $couponResult['error']);
        }
        $couponDiscount = (int) round((float) $couponResult['discount']);

        // Diskon grup pelanggan aktif (persen dari subtotal, tidak pernah di bawah 0).
        $groupDiscount = (int) ($checked['group_discount'] ?? 0);

        // Promotion engine (aturan otomatis: kategori/BOGO/tier/gratis ongkir).
        // Guarded: promo tidak boleh menggagalkan checkout.
        $promoDiscount = 0;
        $promoNames = [];
        try {
            if (class_exists(\App\Services\Promotion\PromotionService::class)) {
                $promoLines = [];
                foreach ($checked['lines'] as $line) {
                    $p = \App\Models\Product::find($line['product_id']);
                    $promoLines[] = [
                        'product_id' => $line['product_id'],
                        'category_id' => $p?->category_id,
                        'price' => $line['unit_price'],
                        'qty' => $line['quantity'],
                    ];
                }
                $promo = app(\App\Services\Promotion\PromotionService::class)
                    ->evaluate($promoLines, Auth::id(), $subtotal);
                $promoDiscount = (int) max(0, $promo['total_discount'] ?? 0);
                $promoNames = array_values((array) ($promo['applied'] ?? []));
                if (! empty($promo['free_shipping'])) {
                    $promoDiscount += $shippingCost;
                    $shippingCost = 0;
                } elseif (! empty($promo['shipping_discount'])) {
                    $cut = min($shippingCost, (int) $promo['shipping_discount']);
                    $shippingCost -= $cut;
                    $promoDiscount += $cut;
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Promotion evaluate failed (non-fatal)', ['error' => $e->getMessage()]);
            $promoDiscount = 0;
            $promoNames = [];
        }

        // Biaya layanan kanal dari config (bukan hardcode) — ikut ditagih & tercatat.
        $channelFees = collect(config('payment.channels', []))->keyBy('code');
        $paymentFee = (int) ($channelFees->get($request->input('payment_type'))['fee'] ?? 0);

        $grandTotal = max(0, $subtotal + $totalTax + $shippingCost + $paymentFee - $couponDiscount - $groupDiscount - $promoDiscount);

        $companyId = Auth::user()?->companies()->where('companies.is_active', true)->orderByDesc('company_user.id')->value('companies.id');

        $order = Order::create([
            'user_id' => Auth::id(),
            'company_id' => $companyId,
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
            'payment_fee' => $paymentFee,
            'order_from' => 'web',
            'view' => false,
            'delivery_viewed' => false,
            'payment_status_viewed' => false,
            'commission_calculated' => false,
            'manual_payment' => in_array($request->input('payment_type'), ['cod', 'manual']),
            'coupon_code' => $couponResult['coupon']?->code,
            'coupon_discount' => $couponDiscount,
            'promo_discount' => $promoDiscount,
            'promo_names' => $promoNames ? implode(', ', array_slice($promoNames, 0, 5)) : null,
            'discount' => $groupDiscount,
        ]);

        $inventory = app(\App\Services\Inventory\InventoryService::class);
        $reserved = [];
        try {
            foreach ($checked['lines'] as $line) {
                $cartItem = $cartItems->first(fn ($c) => (int) $c->product_id === (int) $line['product_id'] && ($c->variation ?? null) === ($line['variation'] ?? null));
                OrderDetail::create([
                    'order_id' => $order->id,
                    'seller_id' => $cartItem?->product?->user_id,
                    'product_id' => $line['product_id'],
                    'variation' => $line['variation'],
                    'price' => $line['unit_price'],
                    'tax' => $line['tax'],
                    'shipping_cost' => $cartItem?->shipping_cost ?? 0,
                    'quantity' => $line['quantity'],
                    'payment_status' => 'unpaid',
                    'delivery_status' => 'pending',
                    'shipping_type' => $request->input('shipping_method', 'home_delivery'),
                ]);

                $product = Product::find($line['product_id']);
                if ($product) {
                    $product->increment('num_of_sale', $line['quantity']);
                }

                // Reservasi stok per baris (integer qty, stock row dikunci di service).
                if ($product && ! $product->digital) {
                    $stockId = \App\Models\ProductStock::where('product_id', $line['product_id'])
                        ->when($line['variation'] !== null && $line['variation'] !== '', fn ($q) => $q->where('variant', $line['variation']))
                        ->orderByDesc('qty')
                        ->value('id');
                    if ($stockId) {
                        $inventory->reserve($line['product_id'], $stockId, $line['quantity'], 'order', $order->id);
                        $reserved[] = ['product_id' => $line['product_id'], 'stock_id' => $stockId, 'qty' => $line['quantity']];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Kegagalan reservasi: kembalikan yang sudah ter-reserve lalu batalkan order.
            foreach ($reserved as $r) {
                try {
                    $inventory->release($r['product_id'], $r['stock_id'], $r['qty'], 'order', $order->id);
                } catch (\Throwable) {
                }
            }
            $order->orderDetails()->delete();
            $order->delete();
            Log::warning('Checkout reserve failed, order rolled back: '.$e->getMessage());
            return back()->withInput()->with('error', 'Stok berubah saat checkout, silakan coba lagi.');
        }

        Cart::where('user_id', Auth::id())->delete();

        // Event order.created untuk automation + webhook (guarded, non-fatal).
        if (class_exists(\App\Events\OrderCreated::class)) {
            try {
                event(new \App\Events\OrderCreated($order));
            } catch (\Throwable $e) {
                Log::warning('OrderCreated dispatch failed (non-fatal)', ['order' => $order->code, 'error' => $e->getMessage()]);
            }
        }

        // Buat transaksi payment gateway (Midtrans Snap prioritas, fallback manual/COD tetap sukses)
        $snapToken = null;
        $snapRedirect = null;
        try {
            $gateway = PaymentGatewayConfig::active()->orderBy('sort_order')->first();
            $isOnlineChannel = ! in_array($request->input('payment_type'), ['cod', 'manual', 'wallet']);
            if ($gateway && $isOnlineChannel) {
                $service = app(PaymentGatewayService::class);
                // Itemisasi SEIMBANG: Midtrans menolak jika jumlah item != gross_amount.
                // Produk + pajak + ongkir + fee sebagai item positif, diskon sebagai negatif.
                $items = $order->orderDetails->map(fn($d) => [
                    'id' => 'P-'.(string) $d->product_id,
                    'price' => (int) $d->price,
                    'quantity' => (int) $d->quantity,
                    'name' => substr($d->product->name ?? 'Produk', 0, 50),
                ])->all();
                $extra = [
                    ['id' => 'TAX', 'price' => (int) $order->tax_amount, 'quantity' => 1, 'name' => 'Pajak'],
                    ['id' => 'SHIPPING', 'price' => (int) $order->shipping_cost, 'quantity' => 1, 'name' => substr('Ongkir '.($order->courier ?? ''), 0, 50)],
                    ['id' => 'FEE', 'price' => (int) $order->payment_fee, 'quantity' => 1, 'name' => 'Biaya layanan'],
                    ['id' => 'DISC-COUPON', 'price' => -(int) $order->coupon_discount, 'quantity' => 1, 'name' => 'Kupon '.($order->coupon_code ?? '')],
                    ['id' => 'DISC-GROUP', 'price' => -(int) $order->discount, 'quantity' => 1, 'name' => 'Diskon pelanggan'],
                    ['id' => 'DISC-PROMO', 'price' => -(int) $order->promo_discount, 'quantity' => 1, 'name' => 'Promo otomatis'],
                ];
                foreach ($extra as $row) {
                    if ($row['price'] !== 0) {
                        $items[] = $row;
                    }
                }
                // Pengaman: jumlah item HARUS sama dengan gross_amount.
                $itemsSum = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
                if ($grandTotal <= 0 || $itemsSum !== (int) $grandTotal) {
                    Log::warning('Payment skipped: item/gross mismatch', ['order' => $order->code, 'sum' => $itemsSum, 'gross' => $grandTotal]);
                    $order->update(['payment_details' => json_encode(['skipped' => 'item_gross_mismatch'])]);
                } else {
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
