<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ShippingQuoteController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    // Public
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/products', [\App\Http\Controllers\Api\V1\ProductController::class, 'index'])->name('api.v1.products.index');
    Route::get('/products/{slug}', [\App\Http\Controllers\Api\V1\ProductController::class, 'show'])->name('api.v1.products.show');
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/blogs', [BlogController::class, 'index'])->name('api.v1.blogs.index');
    Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('api.v1.blogs.show');
    Route::post('/shipping/quote', [ShippingQuoteController::class, 'quote']);

    // Authenticated (Sanctum, customer owns own data only)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);

        Route::post('/checkout/quote', [CheckoutController::class, 'quote']);
        Route::post('/checkout/place', [CheckoutController::class, 'place']);
        Route::get('/payment/{code}', [CheckoutController::class, 'paymentStatus']);

        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::post('/coupons/validate', [CouponController::class, 'validate']);
    });
});
