<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\FlashDeal;
use App\Models\Slider;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\BusinessSetting;
use App\Models\CmsSection;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('status', true)->orderBy('position')->get();
        $featuredCategories = Category::where('featured', true)->take(10)->get();
        $allCategories = Category::where('top', true)->orderBy('name')->get();
        $flashDeals = FlashDeal::where('status', true)->with('flashDealProducts.product')->get();
        $featuredProducts = Product::published()->approved()->where('featured', true)->take(12)->latest()->get();
        $bestSellers = Product::published()->approved()->orderBy('num_of_sale', 'desc')->take(12)->get();
        $todaysDealProducts = Product::published()->approved()->where('todays_deal', true)->take(8)->get();
        $newProducts = Product::published()->approved()->latest()->take(8)->get();
        $brands = Brand::where('top', true)->take(8)->get();
        $banners1 = Banner::where('status', true)->where('type', 'banner1')->orderBy('position')->get();
        $banners2 = Banner::where('status', true)->where('type', 'banner2')->orderBy('position')->get();
        $banners3 = Banner::where('status', true)->where('type', 'banner3')->orderBy('position')->get();
        $coupons = Coupon::where('status', true)->where('end_date', '>=', now()->timestamp)->take(4)->get();

        $categoryProducts = Category::where('featured', true)->with(['products' => function ($q) {
            $q->published()->approved()->take(6);
        }])->take(4)->get();

        // CMS versi kita: urutan + on/off homepage dari DB.
        // sort_order di /admin → Homepage Sections mengatur posisi;
        // is_active=false menyembunyikan section. Key yang belum ada
        // di DB tetap tampil di posisi default agar fresh install aman.
        $defaultOrder = ['hero', 'voucher_rail', 'flash_deals', 'todays_deal', 'banners_1', 'categories', 'featured_products', 'banners_2', 'best_sellers', 'banners_3', 'category_products', 'new_products', 'coupons', 'brands', 'newsletter', 'final_cta'];
        try {
            $dbSections = CmsSection::ordered()->get()->keyBy('key');
            $cmsSectionOrder = [];
            foreach ($dbSections as $row) {
                if ($row->is_active && in_array($row->key, $defaultOrder, true)) {
                    $cmsSectionOrder[] = $row->key;
                }
            }
            foreach ($defaultOrder as $k) {
                if (! isset($dbSections[$k]) && ! in_array($k, $cmsSectionOrder, true)) {
                    $cmsSectionOrder[] = $k;
                }
            }
        } catch (\Exception) {
            $cmsSectionOrder = $defaultOrder;
        }

        $settings = [
            'best_selling' => BusinessSetting::getValue('best_selling', '1'),
            'coupon_system' => BusinessSetting::getValue('coupon_system', '0'),
            'flash_deal' => BusinessSetting::getValue('flash_deal', '1'),
            'todays_deal' => BusinessSetting::getValue('todays_deal', '1'),
            'featured_products' => BusinessSetting::getValue('featured_products', '1'),
            'featured_categories' => BusinessSetting::getValue('featured_categories', '1'),
            'newsletter' => BusinessSetting::getValue('newsletter', '1'),
            'top_brands' => BusinessSetting::getValue('top_brands', '1'),
            'new_products' => BusinessSetting::getValue('new_products', '1'),
            'home_banner1' => BusinessSetting::getValue('home_banner1', '1'),
            'home_banner2' => BusinessSetting::getValue('home_banner2', '1'),
            'home_banner3' => BusinessSetting::getValue('home_banner3', '1'),
            'category_products' => BusinessSetting::getValue('category_products', '1'),
        ];

        return view('storefront.home', compact(
            'sliders', 'featuredCategories', 'allCategories', 'flashDeals',
            'featuredProducts', 'bestSellers', 'todaysDealProducts',
            'newProducts', 'brands', 'banners1', 'banners2', 'banners3',
            'coupons', 'categoryProducts', 'settings', 'cmsSectionOrder'
        ));
    }
}
