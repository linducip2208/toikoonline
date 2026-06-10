<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'latest');

        $products = Product::published()->approved()->with('category', 'brand');

        if ($request->filled('category')) {
            $products->whereHas('category', fn($q) => $q->where('slug', $request->query('category')));
        }

        if ($request->filled('brand')) {
            $products->whereHas('brand', fn($q) => $q->where('slug', $request->query('brand')));
        }

        if ($request->filled('min_price')) {
            $products->where('unit_price', '>=', $request->query('min_price'));
        }

        if ($request->filled('max_price')) {
            $products->where('unit_price', '<=', $request->query('max_price'));
        }

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price-asc' => ['unit_price', 'asc'],
            'price-desc' => ['unit_price', 'desc'],
            'name-asc' => ['name', 'asc'],
            'name-desc' => ['name', 'desc'],
            'popular' => ['num_of_sale', 'desc'],
            'rating' => ['rating', 'desc'],
        ];

        $order = $sortOptions[$sort] ?? $sortOptions['latest'];
        $products->orderBy($order[0], $order[1]);

        $products = $products->paginate(20)->appends($request->query());

        $categories = Category::where('featured', true)->get();
        $brands = Brand::where('top', true)->get();

        $title = 'Semua Produk';

        return view('storefront.product-listing', compact('products', 'categories', 'brands', 'sort', 'title'));
    }

    public function show($slug)
    {
        $product = Product::published()->approved()->with(['category', 'brand', 'reviews.user', 'stocks'])->where('slug', $slug)->firstOrFail();

        $relatedProducts = Product::published()->approved()->where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(8)->latest()->get();

        $topFromSeller = Product::published()->approved()->where('user_id', $product->user_id)->where('id', '!=', $product->id)->take(4)->latest()->get();

        return view('storefront.product-detail', compact('product', 'relatedProducts', 'topFromSeller'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $sort = $request->query('sort', 'latest');

        $products = Product::published()->approved()->with('category', 'brand')->whereHas('category', fn($q) => $q->where('slug', $slug));

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price-asc' => ['unit_price', 'asc'],
            'price-desc' => ['unit_price', 'desc'],
            'name-asc' => ['name', 'asc'],
            'name-desc' => ['name', 'desc'],
            'popular' => ['num_of_sale', 'desc'],
            'rating' => ['rating', 'desc'],
        ];

        $order = $sortOptions[$sort] ?? $sortOptions['latest'];
        $products->orderBy($order[0], $order[1]);

        $products = $products->paginate(20)->appends($request->query());

        $categories = Category::where('featured', true)->get();
        $brands = Brand::where('top', true)->get();

        $title = 'Kategori: ' . $category->name;

        return view('storefront.product-listing', compact('products', 'categories', 'brands', 'sort', 'title', 'category'));
    }

    public function brand(Request $request, $slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $sort = $request->query('sort', 'latest');

        $products = Product::published()->approved()->with('category', 'brand')->whereHas('brand', fn($q) => $q->where('slug', $slug));

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price-asc' => ['unit_price', 'asc'],
            'price-desc' => ['unit_price', 'desc'],
            'name-asc' => ['name', 'asc'],
            'name-desc' => ['name', 'desc'],
            'popular' => ['num_of_sale', 'desc'],
            'rating' => ['rating', 'desc'],
        ];

        $order = $sortOptions[$sort] ?? $sortOptions['latest'];
        $products->orderBy($order[0], $order[1]);

        $products = $products->paginate(20)->appends($request->query());

        $categories = Category::where('featured', true)->get();
        $brands = Brand::where('top', true)->get();

        $title = 'Brand: ' . $brand->name;

        return view('storefront.product-listing', compact('products', 'categories', 'brands', 'sort', 'title', 'brand'));
    }

    public function search(Request $request)
    {
        $query = $request->query('q');
        $sort = $request->query('sort', 'latest');

        $products = Product::published()->approved()->with('category', 'brand');

        if ($query) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('tags', 'like', "%{$query}%")
                  ->orWhereHas('category', fn($cat) => $cat->where('name', 'like', "%{$query}%"))
                  ->orWhereHas('brand', fn($brand) => $brand->where('name', 'like', "%{$query}%"));
            });
        }

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price-asc' => ['unit_price', 'asc'],
            'price-desc' => ['unit_price', 'desc'],
            'name-asc' => ['name', 'asc'],
            'name-desc' => ['name', 'desc'],
            'popular' => ['num_of_sale', 'desc'],
            'rating' => ['rating', 'desc'],
        ];

        $order = $sortOptions[$sort] ?? $sortOptions['latest'];
        $products->orderBy($order[0], $order[1]);

        $products = $products->paginate(20)->appends($request->query());

        $categories = Category::where('featured', true)->get();
        $brands = Brand::where('top', true)->get();

        $title = $query ? 'Hasil Pencarian: ' . $query : 'Pencarian';

        return view('storefront.product-listing', compact('products', 'categories', 'brands', 'sort', 'title', 'query'));
    }
}
