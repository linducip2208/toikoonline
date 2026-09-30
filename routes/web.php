<?php

use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\BlogController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Admin login — custom two-column branded view (GET) + manual auth (POST)
Route::get('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('filament.admin.auth.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);

// Marketing landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{slug}', [ProductController::class, 'category'])->name('categories.show');
Route::get('/brands/{slug}', [ProductController::class, 'brand'])->name('brands.show');

// Cart & Checkout
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success')->middleware('auth');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');

// Static pages
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Customer portal (auth required)
Route::middleware(['auth'])->prefix('account')->name('customer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Search
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/api/search/suggest', [ProductController::class, 'suggest'])->name('search.suggest');

// Storefront AJAX APIs
Route::get('/api/product/{product}/quick-view', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'quickView'])->name('api.quick-view');
Route::post('/api/wishlist/toggle', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'wishlistToggle'])->name('api.wishlist.toggle');
Route::get('/api/wishlist/status', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'wishlistStatus'])->name('api.wishlist.status');
Route::post('/api/compare/toggle', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'compareToggle'])->name('api.compare.toggle');
Route::get('/api/compare/status', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'compareStatus'])->name('api.compare.status');
Route::post('/review/store', [\App\Http\Controllers\Storefront\StorefrontApiController::class, 'reviewStore'])->name('review.store');

// Flash Deals
Route::get('/flash-deals/{slug}', [App\Http\Controllers\Storefront\FlashDealController::class, 'show'])->name('flash-deals.show');

// Coupons
Route::get('/coupons', [App\Http\Controllers\Storefront\CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupons/claim', [App\Http\Controllers\Storefront\CouponController::class, 'claim'])->name('coupons.claim');
Route::post('/api/coupon/validate', [App\Http\Controllers\Storefront\CouponController::class, 'validate'])->name('api.coupon.validate')->middleware('auth');

// Compare
Route::get('/compare', [App\Http\Controllers\Storefront\CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/toggle', [App\Http\Controllers\Storefront\CompareController::class, 'toggle'])->name('compare.toggle')->middleware('auth');
Route::post('/compare/remove', [App\Http\Controllers\Storefront\CompareController::class, 'remove'])->name('compare.remove');

// Newsletter
Route::post('/newsletter/subscribe', [App\Http\Controllers\Storefront\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// All Brands
Route::get('/brands', [App\Http\Controllers\Storefront\BrandController::class, 'index'])->name('brands.index');

// All Categories
Route::get('/categories', [App\Http\Controllers\Storefront\CategoryController::class, 'index'])->name('categories.index');

// Auth routes
require __DIR__.'/auth.php';

// PSEO Routes
Route::get('/best-{category}', [SeoController::class, 'bestCategory'])->name('seo.best-category');
Route::get('/best-{category}-{year}', [SeoController::class, 'bestCategoryYear'])->name('seo.best-category-year');
Route::get('/alternatif-{slug}', [SeoController::class, 'alternative'])->name('seo.alternative');
Route::get('/bandingkan/{a}-vs-{b}', [SeoController::class, 'compare'])->name('seo.compare');
Route::get('/beli-aplikasi-toko-online', [SeoController::class, 'buySourceCode'])->name('seo.buy-source-code');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Payment webhook
Route::post('/webhooks/payment/{gatewayId}', [\App\Http\Controllers\Payment\WebhookController::class, 'handle'])->name('webhook.payment');

// Docs page
Route::get('/docs', fn() => view('pseo.docs'))->name('docs');

// RSS Feed
Route::get('/blog/feed.xml', [App\Http\Controllers\BlogRssController::class, 'index'])->name('blog.rss');

// Auction Routes
Route::get('/lelang', [App\Http\Controllers\Storefront\AuctionController::class, 'index'])->name('auctions.index');
Route::get('/lelang/{auction}', [App\Http\Controllers\Storefront\AuctionController::class, 'show'])->name('auctions.show');
Route::post('/lelang/{auction}/bid', [App\Http\Controllers\Storefront\AuctionController::class, 'bid'])->name('auctions.bid')->middleware('auth');

// Classified Ads Routes
Route::get('/iklan', [App\Http\Controllers\Storefront\ClassifiedController::class, 'index'])->name('classifieds.index');
Route::get('/iklan/{slug}', [App\Http\Controllers\Storefront\ClassifiedController::class, 'show'])->name('classifieds.show');

// Shipping API
Route::get('/api/shipping/cost', [App\Http\Controllers\Api\ShippingController::class, 'cost'])->name('api.shipping.cost');
Route::get('/api/shipping/areas', [App\Http\Controllers\Api\ShippingController::class, 'areas'])->name('api.shipping.areas');
Route::get('/api/shipping/track/{waybill}', [App\Http\Controllers\Api\ShippingController::class, 'track'])->name('api.shipping.track');

// License pairing
require base_path('routes/pair-routes.php');


