<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Compare;
use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index()
    {
        $compares = Compare::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->get();

        return view('storefront.compare', compact('compares'));
    }

    public function toggle(Request $request)
    {
        $productId = $request->input('product_id');

        $existing = Compare::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Produk dihapus dari perbandingan.');
        }

        $count = Compare::where('user_id', auth()->id())->count();
        if ($count >= 4) {
            return back()->with('error', 'Maksimal 4 produk untuk dibandingkan.');
        }

        Compare::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Produk ditambahkan ke perbandingan.');
    }

    public function remove(Request $request)
    {
        Compare::where('user_id', auth()->id())
            ->where('product_id', $request->input('product_id'))
            ->delete();

        return back()->with('success', 'Produk dihapus dari perbandingan.');
    }
}
