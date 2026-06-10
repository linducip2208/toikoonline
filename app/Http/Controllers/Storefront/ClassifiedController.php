<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\CustomerProduct;

class ClassifiedController extends Controller
{
    public function index()
    {
        $classifieds = CustomerProduct::with(['user', 'category'])
            ->where('status', true)
            ->where('published', true)
            ->latest()
            ->paginate(12);

        return view('storefront.classifieds.index', compact('classifieds'));
    }

    public function show($slug)
    {
        $classified = CustomerProduct::with(['user', 'category'])
            ->where('status', true)
            ->where('published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('storefront.classifieds.show', compact('classified'));
    }
}
