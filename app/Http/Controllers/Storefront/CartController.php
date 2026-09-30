<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        } else {
            $cartSession = session()->get('cart', []);
            $cartItems = collect($cartSession)->map(function ($item) {
                return (object) [
                    'id' => null,
                    'product_id' => $item['product_id'],
                    'variation' => $item['variation'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax' => $item['tax'] ?? 0,
                    'shipping_cost' => $item['shipping_cost'] ?? 0,
                    'product' => Product::find($item['product_id']),
                ];
            })->filter(fn($item) => $item->product !== null);
        }

        // Payload Alpine dirakit di sini (Blade @json tidak tahan ekspresi bersarang).
        $cartPayload = $cartItems->map(fn($item) => [
            'product_id' => $item->product_id,
            'variation' => $item->variation,
            'name' => $item->product->name ?? 'Produk',
            'price' => (int) $item->price,
            'qty' => (int) $item->quantity,
            'image' => $item->product->thumbnail_img ? asset($item->product->thumbnail_img) : null,
        ])->values()->all();

        return view('storefront.cart', compact('cartItems', 'cartPayload'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1',
            'variation' => 'nullable|string',
            'price' => 'required|numeric',
            'tax' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
        ]);

        $data = [
            'product_id' => $request->input('product_id'),
            'variation' => $request->input('variation'),
            'price' => $request->input('price'),
            'tax' => $request->input('tax', 0),
            'shipping_cost' => $request->input('shipping_cost', 0),
            'quantity' => $request->input('quantity', 1),
        ];

        // Stock-cap: jangan biarkan qty di keranjang melebihi stok gudang.
        $product = Product::find($data['product_id']);
        if ($product && ! $product->digital) {
            $available = $this->availableStock($product->id, $data['variation']);
            if (Auth::check()) {
                $inCart = (int) Cart::where('user_id', Auth::id())
                    ->where('product_id', $data['product_id'])
                    ->where('variation', $data['variation'])
                    ->sum('quantity');
            } else {
                $inCart = (int) collect(session()->get('cart', []))
                    ->where('product_id', $data['product_id'])
                    ->where('variation', $data['variation'])
                    ->sum('quantity');
            }
            if ($inCart + (int) $data['quantity'] > $available) {
                return redirect()->route('cart.index')->with('error', __('commerce.cart_stock_capped', ['qty' => $available]));
            }
        }

        if (Auth::check()) {
            $data['user_id'] = Auth::id();

            $existing = Cart::where('user_id', Auth::id())
                ->where('product_id', $data['product_id'])
                ->where('variation', $data['variation'])
                ->first();

            if ($existing) {
                $existing->increment('quantity', $data['quantity']);
                $existing->update([
                    'price' => $data['price'],
                    'tax' => $data['tax'],
                    'shipping_cost' => $data['shipping_cost'],
                ]);
            } else {
                Cart::create($data);
            }
        } else {
            $cart = session()->get('cart', []);
            $found = false;

            foreach ($cart as &$item) {
                if ($item['product_id'] == $data['product_id'] && ($item['variation'] ?? null) == ($data['variation'] ?? null)) {
                    $item['quantity'] += $data['quantity'];
                    $item['price'] = $data['price'];
                    $item['tax'] = $data['tax'];
                    $item['shipping_cost'] = $data['shipping_cost'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cart[] = $data;
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->input('product_id');
        $variation = $request->input('variation');
        $quantity = $request->input('quantity');

        // Stock-cap saat update qty.
        $product = Product::find($productId);
        if ($product && ! $product->digital && $quantity > 0) {
            $available = $this->availableStock($product->id, $variation);
            if ($quantity > $available) {
                return redirect()->route('cart.index')->with('error', __('commerce.cart_stock_capped', ['qty' => $available]));
            }
        }

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('variation', $variation)
                ->first();

            if ($cartItem) {
                if ($quantity > 0) {
                    $cartItem->update(['quantity' => $quantity]);
                } else {
                    $cartItem->delete();
                }
            }
        } else {
            $cart = session()->get('cart', []);

            foreach ($cart as $index => &$item) {
                if ($item['product_id'] == $productId && ($item['variation'] ?? null) == $variation) {
                    if ($quantity > 0) {
                        $item['quantity'] = $quantity;
                    } else {
                        unset($cart[$index]);
                    }
                    break;
                }
            }

            session()->put('cart', array_values($cart));
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variation' => 'nullable|string',
        ]);

        $productId = $request->input('product_id');
        $variation = $request->input('variation');

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->where('variation', $variation)
                ->delete();
        } else {
            $cart = session()->get('cart', []);

            $cart = array_filter($cart, function ($item) use ($productId, $variation) {
                return !($item['product_id'] == $productId && ($item['variation'] ?? null) == $variation);
            });

            session()->put('cart', array_values($cart));
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    protected function availableStock(int $productId, ?string $variation): int
    {
        $query = ProductStock::where('product_id', $productId);
        if ($variation !== null && $variation !== '') {
            $query->where('variant', $variation);
        }

        return (int) $query->sum('qty');
    }
}
