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

// Auth routes (Laravel Breeze or custom)
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
Route::get('/api/shipping/track/{waybill}', [App\Http\Controllers\Api\ShippingController::class, 'track'])->name('api.shipping.track');

// License pairing
require base_path('routes/pair-routes.php');


