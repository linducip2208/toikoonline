<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|string|max:128',
            'brand' => 'nullable|string|max:128',
            'search' => 'nullable|string|max:128',
            'sort' => 'nullable|in:latest,cheapest,expensive,popular,rating',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Product::published()->approved()->with(['category', 'brand']);

        if ($request->filled('category')) {
            $slug = $request->category;
            $query->where(function ($q) use ($slug) {
                $q->whereHas('categories', fn ($qq) => $qq->where('slug', $slug))
                    ->orWhereHas('category', fn ($qq) => $qq->where('slug', $slug));
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->brand));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('tags', 'like', "%{$s}%"));
        }

        if ($request->filled('min_price')) {
            $query->where('unit_price', '>=', (int) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('unit_price', '<=', (int) $request->max_price);
        }

        match ($request->input('sort', 'latest')) {
            'cheapest' => $query->orderBy('unit_price', 'asc'),
            'expensive' => $query->orderBy('unit_price', 'desc'),
            'popular' => $query->orderBy('num_of_sale', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            default => $query->latest(),
        };

        $page = $query->paginate((int) $request->input('per_page', 15));

        return ProductResource::collection($page)->response();
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::published()->approved()
            ->with(['category', 'brand', 'reviews' => fn ($q) => $q->where('status', true)->latest()->limit(10)])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => array_merge(
                (new ProductResource($product))->resolve(),
                [
                    'description' => $product->description,
                    'weight' => (float) ($product->weight ?? 0),
                    'reviews' => $product->reviews->map(fn ($r) => [
                        'rating' => (int) $r->rating,
                        'comment' => $r->comment,
                        'reviewer' => $r->user?->name ?? $r->custom_reviewer_name,
                    ])->all(),
                ]
            ),
        ]);
    }
}
