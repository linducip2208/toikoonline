<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\FlashDeal;
use App\Models\Slider;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('status', true)->orderBy('position')->get();
        $featuredCategories = Category::where('featured', true)->take(8)->get();
        $flashDeals = FlashDeal::where('status', true)->with('flashDealProducts.product')->get();
        $featuredProducts = Product::published()->approved()->whereHas('category', fn($q) => $q->where('featured', true))->take(8)->latest()->get();
        $bestSellers = Product::published()->approved()->orderBy('num_of_sale', 'desc')->take(8)->get();
        $brands = Brand::where('top', true)->take(6)->get();
        $todaysDeal = Product::published()->approved()->where('todays_deal', true)->take(6)->get();

        return view('storefront.home', compact(
            'sliders', 'featuredCategories', 'flashDeals',
            'featuredProducts', 'bestSellers', 'brands', 'todaysDeal'
        ));
    }
}
