<?php

namespace App\Providers;

use App\Models\CmsSection;
use App\Models\DynamicPopup;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Blog;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Policies\CategoryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\LicenseClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);

        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);

        RateLimiter::for('login', fn(Request $r) => Limit::perMinute(5)
            ->by(strtolower($r->input('email', '').'|'.$r->ip()))
            ->response(fn() => back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi 1 menit.'])));

        // CMS versi kita: share popup + menu + footer pages ke semua storefront view.
        // Dibungkus try/catch + cache agar aman saat migrate/fresh install.
        try {
            View::composer('layouts.storefront', function ($view) {
                $popup = Cache::remember('cms:popup', now()->addHour(), function () {
                    if (! Schema::hasTable('dynamic_popups')) return null;
                    return DynamicPopup::where('status', true)->latest()->first();
                });
                $menusByLoc = Cache::remember('cms:menus:all', now()->addHour(), function () {
                    if (! Schema::hasTable('menus')) return [];
                    return [
                        'header' => Menu::forLocation('header'),
                        'mobile' => Menu::forLocation('mobile'),
                        'footer_shop' => Menu::forLocation('footer_shop'),
                        'footer_help' => Menu::forLocation('footer_help'),
                    ];
                });
                $headerMenus = $menusByLoc['header'] ?? collect();
                $mobileMenus = ($menusByLoc['mobile'] ?? collect())->count() ? $menusByLoc['mobile'] : $headerMenus;
                $footerShopMenus = $menusByLoc['footer_shop'] ?? collect();
                $footerHelpMenus = $menusByLoc['footer_help'] ?? collect();
                $footerPages = Cache::remember('cms:pages:footer', now()->addHour(), function () {
                    if (! Schema::hasTable('pages')) return collect();
                    return Page::active()->where('show_in_footer', true)->orderBy('title')->take(8)->get();
                });
                $view->with(compact('popup', 'headerMenus', 'mobileMenus', 'footerShopMenus', 'footerHelpMenus', 'footerPages'));
            });
        } catch (\Exception) {
            // abaikan saat tabel belum ada
        }
    }
}
