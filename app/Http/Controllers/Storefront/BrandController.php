<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount(['products' => function ($q) {
            $q->published()->approved();
        }])->orderBy('name')->paginate(24);

        return view('storefront.brands', compact('brands'));
    }
}
