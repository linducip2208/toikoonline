<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('storefront.checkout', compact(
            'cartItems', 'addresses', 'defaultAddress', 'billingAddress',
            'subtotal', 'totalTax', 'totalShipping', 'grandTotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'billing_address' => 'nullable|array',
            'payment_type' => 'required|string',
            'shipping_method' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $totalTax = $cartItems->sum(fn($item) => $item->tax * $item->quantity);
        $totalShipping = $cartItems->sum(fn($item) => $item->shipping_cost * $item->quantity);
        $grandTotal = $subtotal + $totalTax + $totalShipping;

        $order = Order::create([
            'user_id' => Auth::id(),
            'shipping_address' => json_encode($request->input('shipping_address')),
            'billing_address' => json_encode($request->input('billing_address')),
            'shipping_method' => $request->input('shipping_method'),
            'additional_info' => $request->input('additional_info'),
            'payment_type' => $request->input('payment_type'),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'code' => date('Ymd') . '-' . Str::random(6),
            'date' => now()->timestamp,
            'grand_total' => $grandTotal,
            'tax_amount' => $totalTax,
            'order_from' => 'web',
            'view' => false,
            'delivery_viewed' => false,
            'payment_status_viewed' => false,
            'commission_calculated' => false,
            'manual_payment' => false,
            'coupon_discount' => 0,
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

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderDetails.product');

        return view('storefront.checkout-success', compact('order'));
    }
}
