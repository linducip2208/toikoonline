<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Wishlist::with('product')->where('user_id', request()->user()->id)->get();

        return response()->json(['success' => true, 'data' => WishlistResource::collection($items)->resolve()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $item = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['success' => true, 'data' => (new WishlistResource($item->load('product')))->resolve()], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        Wishlist::where('user_id', request()->user()->id)->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Removed from wishlist.']);
    }
}
