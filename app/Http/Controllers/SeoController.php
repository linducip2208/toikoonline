<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * /best-{category} — Top 10 products in a category
     */
    public function bestCategory($category)
    {
        $cat = Category::where('slug', $category)->firstOrFail();

        $products = Product::published()->approved()
            ->whereHas('categories', fn($q) => $q->where('category_id', $cat->id))
            ->orderBy('num_of_sale', 'desc')
            ->take(10)
            ->get();

        $meta = [
            'title' => "10 Produk {$cat->name} Terbaik " . date('Y'),
            'description' => "Daftar produk {$cat->name} terbaik dan paling laris tahun " . date('Y') . ". Bandingkan harga, rating dan review sebelum beli.",
        ];

        return view('pseo.best-category', compact('cat', 'products', 'meta'));
    }

    /**
     * /best-{category}-{year} — Top 10 products in a category for a specific year
     */
    public function bestCategoryYear($category, $year)
    {
        $cat = Category::where('slug', $category)->firstOrFail();

        $products = Product::published()->approved()
            ->whereHas('categories', fn($q) => $q->where('category_id', $cat->id))
            ->orderBy('num_of_sale', 'desc')
            ->take(10)
            ->get();

        $meta = [
            'title' => "10 Produk {$cat->name} Terbaik {$year}",
            'description' => "Daftar produk {$cat->name} terbaik dan paling laris tahun {$year}. Lihat perbandingan harga, rating, dan review pelanggan.",
        ];

        return view('pseo.best-category', compact('cat', 'products', 'meta', 'year'));
    }

    /**
     * /alternatif-{slug} — Alternatives to a specific product
     */
    public function alternative($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $categoryIds = $product->categories->pluck('id');

        $alternatives = Product::published()->approved()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', fn($q) => $q->whereIn('category_id', $categoryIds))
            ->inRandomOrder()
            ->take(10)
            ->get();

        $meta = [
            'title' => "10 Alternatif {$product->name} — Produk Serupa Terbaik",
            'description' => "Cari alternatif {$product->name}? Lihat daftar 10 produk serupa dengan kualitas dan harga terbaik. Bandingkan sebelum beli.",
        ];

        return view('pseo.alternatives', compact('product', 'alternatives', 'meta'));
    }

    /**
     * /bandingkan/{a}-vs-{b} — Head-to-head product comparison
     */
    public function compare($a, $b)
    {
        $productA = Product::where('slug', $a)->firstOrFail();
        $productB = Product::where('slug', $b)->firstOrFail();

        $meta = [
            'title' => "{$productA->name} vs {$productB->name} — Perbandingan Lengkap",
            'description' => "Bandingkan {$productA->name} dan {$productB->name}. Lihat perbedaan harga, fitur, rating, dan review. Mana yang lebih worth it?",
        ];

        return view('pseo.compare', compact('productA', 'productB', 'meta'));
    }

    /**
     * /beli-aplikasi-toko-online — Source code sales landing page
     */
    public function buySourceCode()
    {
        $meta = [
            'title' => 'Beli Aplikasi Toko Online / Source Code E-Commerce',
            'description' => 'Beli source code aplikasi toko online siap pakai. Laravel + MySQL. Whitelabel ready, bisa ganti nama, logo, warna sesuka hati.',
        ];

        return view('pseo.buy-source-code', compact('meta'));
    }
}
