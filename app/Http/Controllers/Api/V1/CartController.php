<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CartRequest;
use App\Http\Resources\Api\V1\CartResource;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Cart::with('product')->where('user_id', request()->user()->id)->get();

        return response()->json([
            'success' => true,
            'data' => CartResource::collection($items)->resolve(),
            'subtotal' => $items->sum(fn ($i) => (float) $i->price * (int) $i->quantity),
        ]);
    }

    public function store(CartRequest $request): JsonResponse
    {
        $product = Product::published()->approved()->findOrFail($request->product_id);
        $price = ProductResource::effectivePrice($product);

        $item = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id, 'variation' => $request->variation],
            ['price' => $price, 'quantity' => \DB::raw("quantity + {$request->quantity}") , 'tax' => 0, 'shipping_cost' => 0, 'owner_id' => $request->user()->id]
        );

        if ($item->wasRecentlyCreated) {
            $item->update(['quantity' => $request->quantity]);
        }

        return response()->json(['success' => true, 'data' => (new CartResource($item->fresh('product')))->resolve()], 201);
    }

    public function update(CartRequest $request, int $id): JsonResponse
    {
        $item = Cart::where('user_id', $request->user()->id)->findOrFail($id);
        $item->update(['quantity' => $request->quantity]);

        return response()->json(['success' => true, 'data' => (new CartResource($item->fresh('product')))->resolve()]);
    }

    public function destroy(int $id): JsonResponse
    {
        Cart::where('user_id', request()->user()->id)->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Item removed.']);
    }
}
