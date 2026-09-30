<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Compare;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StorefrontApiController extends Controller
{
    public function quickView(Product $product)
    {
        $product->load('category', 'brand', 'stocks');

        $photos = is_array($product->photos) ? $product->photos : json_decode($product->photos ?? '[]', true);
        $effPrice = $product->unit_price;

        if ($product->discount && $product->discount > 0) {
            $effPrice = $product->discount_type === 'percent'
                ? $product->unit_price * (1 - $product->discount / 100)
                : $product->unit_price - $product->discount;
        }

        $discPercent = $product->unit_price > 0 ? round(($product->unit_price - $effPrice) / $product->unit_price * 100) : 0;

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (int) $product->unit_price,
            'effective_price' => (int) max(0, $effPrice),
            'discount_percent' => $discPercent,
            'has_discount' => $discPercent > 0,
            'thumbnail' => $product->thumbnail_img ? asset($product->thumbnail_img) : null,
            'photos' => array_map(fn($p) => asset($p), $photos),
            'category' => $product->category?->name,
            'brand' => $product->brand?->name,
            'rating' => (float) ($product->rating ?? 0),
            'num_of_sale' => (int) ($product->num_of_sale ?? 0),
            'variant_product' => (bool) $product->variant_product,
            'stocks' => $product->stocks->map(fn($s) => [
                'id' => $s->id,
                'variant' => $s->variant,
                'price' => (int) ($s->price ?: $effPrice),
                'qty' => (int) $s->qty,
                'color_code' => $s->color_code,
            ]),
            'description' => \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 200),
        ]);
    }

    public function wishlistToggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['wishlisted' => false, 'message' => 'Silakan login terlebih dahulu'], 401);
        }

        $productId = $request->input('product_id');

        $existing = Wishlist::where('user_id', auth()->id())->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['wishlisted' => false, 'count' => Wishlist::where('user_id', auth()->id())->count()]);
        }

        Wishlist::create(['user_id' => auth()->id(), 'product_id' => $productId]);
        return response()->json(['wishlisted' => true, 'count' => Wishlist::where('user_id', auth()->id())->count()]);
    }

    public function wishlistStatus()
    {
        if (!auth()->check()) {
            return response()->json(['ids' => [], 'count' => 0]);
        }

        $ids = Wishlist::where('user_id', auth()->id())->pluck('product_id');
        return response()->json(['ids' => $ids, 'count' => $ids->count()]);
    }

    public function compareToggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['compared' => false, 'message' => 'Silakan login'], 401);
        }

        $productId = $request->input('product_id');

        $existing = Compare::where('user_id', auth()->id())->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['compared' => false, 'count' => Compare::where('user_id', auth()->id())->count()]);
        }

        $count = Compare::where('user_id', auth()->id())->count();
        if ($count >= 4) {
            return response()->json(['compared' => false, 'message' => 'Maksimal 4 produk'], 422);
        }

        Compare::create(['user_id' => auth()->id(), 'product_id' => $productId]);
        return response()->json(['compared' => true, 'count' => Compare::where('user_id', auth()->id())->count()]);
    }

    public function compareStatus()
    {
        if (!auth()->check()) {
            return response()->json(['ids' => [], 'count' => 0]);
        }

        $ids = Compare::where('user_id', auth()->id())->pluck('product_id');
        return response()->json(['ids' => $ids, 'count' => $ids->count()]);
    }

    public function reviewStore(Request $request)
    {
        if (!auth()->check()) {
            return back()->with('error', 'Silakan login untuk memberikan ulasan.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:500',
        ]);

        Review::create([
            'type' => 'product',
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => true,
            'viewed' => false,
        ]);

        // Update product rating average
        $avg = Review::where('product_id', $validated['product_id'])->where('status', true)->avg('rating');
        Product::where('id', $validated['product_id'])->update(['rating' => $avg]);

        return back()->with('success', 'Ulasan berhasil dikirim! Terima kasih.');
    }
}
