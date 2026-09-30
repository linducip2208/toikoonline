<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->published()->approved();
        }])->where('top', true)->orderBy('name')->get();

        return view('storefront.categories', compact('categories'));
    }
}
