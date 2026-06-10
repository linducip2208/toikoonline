@extends('layouts.storefront')

@section('title', 'Belanja Mudah, Harga Terbaik — TokoOnline')
@section('meta_description', 'Temukan ribuan produk berkualitas dengan harga bersaing dan pengiriman cepat ke seluruh Indonesia. Belanja online aman, nyaman, dan terpercaya hanya di TokoOnline.')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #312e81 0%, #4338ca 30%, #6366f1 60%, #a21caf 100%);
    }
    .hero-gradient::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(168,85,247,.2) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(99,102,241,.25) 0%, transparent 50%),
            radial-gradient(circle at 50% 50%, rgba(236,72,153,.1) 0%, transparent 60%);
        pointer-events: none;
    }
    .browser-mock {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 32px 64px -16px rgba(0,0,0,.35);
        border: 2px solid rgba(255,255,255,.15);
    }
    .browser-dots span {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .flash-timer-box {
        background: rgba(239,68,68,.15);
        border: 1px solid rgba(239,68,68,.3);
    }
    .product-card:hover .product-image img {
        transform: scale(1.08);
    }
    .product-image img {
        transition: transform .5s cubic-bezier(.16,1,.3,1);
    }
    .discount-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    .star-gold { color: #f59e0b; }
    .btn-gradient {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        transition: transform .2s, box-shadow .2s;
    }
    .btn-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px -6px rgba(99,102,241,.45);
    }

    @media (max-width: 640px) {
        .hero-headline { font-size: 2rem; }
    }
</style>
@endpush

@section('content')
{{-- Hero Section --}}
<section class="hero-gradient relative overflow-hidden pt-16 pb-24 lg:pt-24 lg:pb-32">
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 text-6xl animate-float-slow opacity-30">🛍️</div>
        <div class="absolute top-40 right-16 text-5xl animate-float-slow-delayed opacity-25">📦</div>
        <div class="absolute bottom-20 left-1/3 text-4xl animate-float-slow-delayed-2 opacity-25">🏷️</div>
        <div class="absolute top-1/3 right-1/4 text-5xl animate-float-slow opacity-20">🚚</div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-white reveal">
                <h1 class="font-display text-5xl lg:text-6xl font-extrabold leading-tight mb-6 hero-headline">
                    Belanja Mudah,<br>Harga Terbaik
                </h1>
                <p class="text-lg text-brand-100/90 leading-relaxed mb-9 max-w-lg">
                    Temukan ribuan produk berkualitas dengan harga bersaing dan pengiriman cepat ke seluruh Indonesia. Belanja online aman, nyaman, dan terpercaya.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}"
                       class="btn-gradient inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-white font-semibold text-sm shadow-lg shadow-brand-600/30">
                        <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                    </a>
                    <a href="#"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl border-2 border-white/30 text-white font-semibold text-sm
                              hover:bg-white/10 transition-colors backdrop-blur-sm">
                        <i class="fas fa-th-large"></i> Lihat Koleksi
                    </a>
                </div>
            </div>
            <div class="hidden lg:block reveal">
                <div class="browser-mock bg-white">
                    <div class="bg-stone-200 flex items-center gap-1.5 px-4 py-2.5 browser-dots">
                        <span class="bg-red-400"></span>
                        <span class="bg-yellow-400"></span>
                        <span class="bg-green-400"></span>
                        <div class="ml-2 bg-white rounded-full px-4 py-0.5 text-[10px] text-stone-400 flex-1 max-w-xs">tokoonline.id/products</div>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3 bg-stone-50">
                        <div class="bg-white rounded-lg p-2.5 shadow-sm">
                            <div class="bg-gradient-to-br from-brand-100 to-accent-100 rounded-md h-28 mb-2 flex items-center justify-center">
                                <i class="fas fa-headphones text-3xl text-brand-400"></i>
                            </div>
                            <p class="text-[10px] font-semibold text-stone-800 truncate">Headphone Bluetooth</p>
                            <p class="text-[11px] font-bold text-brand-600">Rp 199.000</p>
                            <span class="discount-badge text-white text-[8px] px-1.5 py-0.5 rounded">-25%</span>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 shadow-sm">
                            <div class="bg-gradient-to-br from-warm-100 to-warm-200 rounded-md h-28 mb-2 flex items-center justify-center">
                                <i class="fas fa-tshirt text-3xl text-warm-400"></i>
                            </div>
                            <p class="text-[10px] font-semibold text-stone-800 truncate">Kaos Polos Premium</p>
                            <p class="text-[11px] font-bold text-brand-600">Rp 89.000</p>
                            <span class="discount-badge text-white text-[8px] px-1.5 py-0.5 rounded">-15%</span>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 shadow-sm">
                            <div class="bg-gradient-to-br from-green-100 to-emerald-200 rounded-md h-28 mb-2 flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-3xl text-green-500"></i>
                            </div>
                            <p class="text-[10px] font-semibold text-stone-800 truncate">Smartphone X10</p>
                            <p class="text-[11px] font-bold text-brand-600">Rp 2.499.000</p>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 shadow-sm">
                            <div class="bg-gradient-to-br from-blue-100 to-indigo-200 rounded-md h-28 mb-2 flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-3xl text-blue-500"></i>
                            </div>
                            <p class="text-[10px] font-semibold text-stone-800 truncate">Sepatu Running</p>
                            <p class="text-[11px] font-bold text-brand-600">Rp 349.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Trust Strip --}}
<section class="py-8 bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3 p-3 reveal">
                <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-shipping-fast text-green-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-800">Pengiriman Cepat</p>
                    <p class="text-[11px] text-stone-500">Sampai 2-3 hari</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 reveal">
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-800">Pembayaran Aman</p>
                    <p class="text-[11px] text-stone-500">100% terenkripsi</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 reveal">
                <div class="w-11 h-11 rounded-xl bg-warm-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-medal text-warm-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-800">Garansi Produk</p>
                    <p class="text-[11px] text-stone-500">Garansi 7 hari</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 reveal">
                <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-headset text-purple-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-800">Support 24/7</p>
                    <p class="text-[11px] text-stone-500">Siap membantu Anda</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Flash Deals --}}
<section class="py-14 bg-gradient-to-r from-red-50 via-white to-red-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
            <div class="flex items-center gap-4 reveal">
                <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900">
                    <i class="fas fa-bolt text-red-500 mr-2"></i>Flash Deal
                </h2>
                <div class="flash-timer-box flex items-center gap-3 px-4 py-2 rounded-lg" x-data="flashTimer()">
                    <span class="text-[11px] font-semibold text-red-600">Berakhir dalam:</span>
                    <div class="flex gap-1.5 text-red-700 font-mono font-bold text-sm">
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(hours)">00</span>
                        <span class="text-red-400">:</span>
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(minutes)">00</span>
                        <span class="text-red-400">:</span>
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(seconds)">00</span>
                    </div>
                </div>
            </div>
            <a href="#" class="text-brand-600 text-sm font-semibold hover:underline reveal">Lihat Semua <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $flashProducts = [
                    ['name' => 'TWS Earbuds Pro', 'price' => 299000, 'discounted' => 149000, 'discount' => 50, 'img' => 'headphones', 'sold' => 85],
                    ['name' => 'Smartband X1', 'price' => 499000, 'discounted' => 249000, 'discount' => 50, 'img' => 'heart-pulse', 'sold' => 72],
                    ['name' => 'Kaos Premium', 'price' => 149000, 'discounted' => 79000, 'discount' => 47, 'img' => 'tshirt', 'sold' => 91],
                    ['name' => 'Tas Ransel', 'price' => 399000, 'discounted' => 199000, 'discount' => 50, 'img' => 'briefcase', 'sold' => 63],
                    ['name' => 'Jam Tangan', 'price' => 599000, 'discounted' => 299000, 'discount' => 50, 'img' => 'clock', 'sold' => 78],
                    ['name' => 'Sepatu Sneakers', 'price' => 449000, 'discounted' => 249000, 'discount' => 44, 'img' => 'shoe-prints', 'sold' => 88],
                ];
            @endphp
            @foreach ($flashProducts as $product)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100 card-lift reveal group cursor-pointer">
                <div class="relative">
                    <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-40 flex items-center justify-center product-image">
                        <i class="fas fa-{{ $product['img'] }} text-4xl text-brand-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                        -{{ $product['discount'] }}%
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-xs font-semibold text-stone-800 truncate mb-1">{{ $product['name'] }}</p>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-bold text-brand-600 text-sm">Rp {{ number_format($product['discounted'], 0, ',', '.') }}</span>
                        <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-400 to-red-500 h-full rounded-full" style="width: {{ $product['sold'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-stone-400 mt-1">Terjual {{ $product['sold'] }}%</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Categories --}}
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">Kategori Populer</h2>
            <a href="#" class="text-brand-600 text-sm font-semibold hover:underline reveal">Semua Kategori <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @php
                $categories = [
                    ['name' => 'Elektronik', 'count' => '1.250+ Produk', 'icon' => 'laptop', 'color' => 'blue'],
                    ['name' => 'Fashion Pria', 'count' => '890+ Produk', 'icon' => 'tshirt', 'color' => 'indigo'],
                    ['name' => 'Fashion Wanita', 'count' => '1.100+ Produk', 'icon' => 'female', 'color' => 'pink'],
                    ['name' => 'Rumah Tangga', 'count' => '760+ Produk', 'icon' => 'home', 'color' => 'emerald'],
                    ['name' => 'Kesehatan', 'count' => '520+ Produk', 'icon' => 'heartbeat', 'color' => 'red'],
                    ['name' => 'Olahraga', 'count' => '640+ Produk', 'icon' => 'running', 'color' => 'orange'],
                    ['name' => 'Makanan & Minuman', 'count' => '430+ Produk', 'icon' => 'utensils', 'color' => 'amber'],
                    ['name' => 'Otomotif', 'count' => '380+ Produk', 'icon' => 'car', 'color' => 'slate'],
                ];
            @endphp
            @foreach ($categories as $cat)
            <a href="#" class="bg-white rounded-2xl p-5 border border-stone-100 card-lift reveal group hover:border-brand-200">
                <div class="w-12 h-12 rounded-xl bg-{{ $cat['color'] }}-100 flex items-center justify-center mb-3 group-hover:bg-{{ $cat['color'] }}-200 transition-colors">
                    <i class="fas fa-{{ $cat['icon'] }} text-{{ $cat['color'] }}-600 text-lg"></i>
                </div>
                <h3 class="font-semibold text-sm text-stone-800 mb-1">{{ $cat['name'] }}</h3>
                <p class="text-[11px] text-stone-400">{{ $cat['count'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">Produk Pilihan</h2>
            <a href="{{ route('products.index') }}" class="text-brand-600 text-sm font-semibold hover:underline reveal">Lihat Semua <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @php
                $products = [
                    ['name' => 'Wireless Headphone ANC Pro', 'price' => 599000, 'discounted' => 399000, 'discount' => 33, 'rating' => 4.8, 'sold' => '2.3rb', 'img' => 'headphones', 'color' => 'brand'],
                    ['name' => 'Kaos Katun Premium Pria', 'price' => 149000, 'discounted' => 99000, 'discount' => 34, 'rating' => 4.6, 'sold' => '5.1rb', 'img' => 'tshirt', 'color' => 'warm'],
                    ['name' => 'Tas Selempang Kulit Sintetis', 'price' => 349000, 'discounted' => 249000, 'discount' => 29, 'rating' => 4.7, 'sold' => '1.8rb', 'img' => 'briefcase', 'color' => 'amber'],
                    ['name' => 'Sepatu Running Ultralight', 'price' => 499000, 'discounted' => 379000, 'discount' => 24, 'rating' => 4.5, 'sold' => '3.2rb', 'img' => 'shoe-prints', 'color' => 'blue'],
                    ['name' => 'Blender Portable Mini', 'price' => 249000, 'discounted' => 179000, 'discount' => 28, 'rating' => 4.4, 'sold' => '4.6rb', 'img' => 'blender', 'color' => 'green'],
                    ['name' => 'Jam Tangan Digital Unisex', 'price' => 399000, 'discounted' => 299000, 'discount' => 25, 'rating' => 4.6, 'sold' => '2.9rb', 'img' => 'clock', 'color' => 'purple'],
                    ['name' => 'Power Bank 20000mAh', 'price' => 299000, 'discounted' => 220000, 'discount' => 26, 'rating' => 4.7, 'sold' => '6.3rb', 'img' => 'battery-full', 'color' => 'brand'],
                    ['name' => 'Set Peralatan Dapur 12pc', 'price' => 449000, 'discounted' => 349000, 'discount' => 22, 'rating' => 4.5, 'sold' => '1.4rb', 'img' => 'utensils', 'color' => 'red'],
                ];
            @endphp
            @foreach ($products as $product)
            <div class="bg-stone-50 rounded-2xl overflow-hidden card-lift reveal group cursor-pointer border border-stone-100 hover:border-brand-200">
                <div class="relative product-image">
                    <div class="bg-gradient-to-br from-{{ $product['color'] }}-50 to-{{ $product['color'] }}-100 h-48 flex items-center justify-center">
                        <i class="fas fa-{{ $product['img'] }} text-5xl text-{{ $product['color'] }}-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    @if($product['discount'] > 0)
                    <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                        -{{ $product['discount'] }}%
                    </div>
                    @endif
                    <button class="absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center
                                   text-stone-400 hover:text-red-500 transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                        <i class="far fa-heart text-xs"></i>
                    </button>
                </div>
                <div class="p-3.5">
                    <p class="text-xs font-semibold text-stone-800 line-clamp-2 mb-2 leading-relaxed">{{ $product['name'] }}</p>
                    <div class="flex items-center gap-1 mb-1.5">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-[10px] {{ $i < floor($product['rating']) ? 'star-gold' : 'text-stone-300' }}"></i>
                        @endfor
                        <span class="text-[10px] text-stone-400 ml-1">({{ $product['sold'] }})</span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-bold text-brand-600">Rp {{ number_format($product['discounted'], 0, ',', '.') }}</span>
                        <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                    </div>
                    <button @click="$store.cart.add({ id: {{ $loop->index + 100 }}, name: '{{ $product['name'] }}', price: {{ $product['discounted'] }}, image: '' })"
                            class="w-full py-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-semibold rounded-lg
                                   hover:from-brand-600 hover:to-brand-700 transition-all hover:shadow-lg hover:shadow-brand-500/25">
                        <i class="fas fa-cart-plus mr-1"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Best Sellers --}}
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">Terlaris Minggu Ini</h2>
            <a href="#" class="text-brand-600 text-sm font-semibold hover:underline reveal">Lihat Semua <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @php
                $bestSellers = [
                    ['name' => 'Smartphone X100 Pro 5G', 'price' => 3999000, 'discounted' => 3499000, 'discount' => 13, 'rating' => 4.9, 'sold' => '12.5rb', 'img' => 'mobile-alt', 'color' => 'slate'],
                    ['name' => 'Charger GaN 65W USB-C', 'price' => 299000, 'discounted' => 199000, 'discount' => 33, 'rating' => 4.8, 'sold' => '8.9rb', 'img' => 'plug', 'color' => 'brand'],
                    ['name' => 'Sandal Slides Pria', 'price' => 129000, 'discounted' => 79000, 'discount' => 39, 'rating' => 4.5, 'sold' => '15.2rb', 'img' => 'socks', 'color' => 'warm'],
                    ['name' => 'Botol Minum Stainless 750ml', 'price' => 189000, 'discounted' => 139000, 'discount' => 26, 'rating' => 4.6, 'sold' => '7.8rb', 'img' => 'flask', 'color' => 'emerald'],
                    ['name' => 'Handuk Microfiber Cepat Kering', 'price' => 89900, 'discounted' => 59000, 'discount' => 34, 'rating' => 4.4, 'sold' => '20.1rb', 'img' => 'bath', 'color' => 'blue'],
                    ['name' => 'Kabel Data USB-C 2m', 'price' => 79000, 'discounted' => 45000, 'discount' => 43, 'rating' => 4.7, 'sold' => '18.3rb', 'img' => 'usb', 'color' => 'brand'],
                    ['name' => 'Masker Wajah Organik Sheet', 'price' => 49000, 'discounted' => 29000, 'discount' => 41, 'rating' => 4.6, 'sold' => '25.7rb', 'img' => 'leaf', 'color' => 'green'],
                    ['name' => 'Lampu LED Sensor 12W', 'price' => 99000, 'discounted' => 69000, 'discount' => 30, 'rating' => 4.5, 'sold' => '11.4rb', 'img' => 'lightbulb', 'color' => 'amber'],
                ];
            @endphp
            @foreach ($bestSellers as $product)
            <div class="bg-white rounded-2xl overflow-hidden card-lift reveal group cursor-pointer border border-stone-100 hover:border-brand-200">
                <div class="relative product-image">
                    <div class="bg-gradient-to-br from-{{ $product['color'] }}-50 to-{{ $product['color'] }}-100 h-48 flex items-center justify-center">
                        <i class="fas fa-{{ $product['img'] }} text-5xl text-{{ $product['color'] }}-300 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                        -{{ $product['discount'] }}%
                    </div>
                    <span class="absolute top-2 right-2 bg-white text-[10px] font-bold text-stone-600 px-2 py-0.5 rounded-full shadow-sm">
                        <i class="fas fa-fire text-red-500 text-[9px] mr-0.5"></i>Best
                    </span>
                </div>
                <div class="p-3.5">
                    <p class="text-xs font-semibold text-stone-800 line-clamp-2 mb-2">{{ $product['name'] }}</p>
                    <div class="flex items-center gap-1 mb-1.5">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-[10px] {{ $i < floor($product['rating']) ? 'star-gold' : 'text-stone-300' }}"></i>
                        @endfor
                        <span class="text-[10px] text-stone-400 ml-1">({{ $product['sold'] }})</span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-bold text-brand-600">Rp {{ number_format($product['discounted'], 0, ',', '.') }}</span>
                        <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                    </div>
                    <button @click="$store.cart.add({ id: {{ $loop->index + 200 }}, name: '{{ $product['name'] }}', price: {{ $product['discounted'] }}, image: '' })"
                            class="w-full py-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-semibold rounded-lg
                                   hover:from-brand-600 hover:to-brand-700 transition-all hover:shadow-lg hover:shadow-brand-500/25">
                        <i class="fas fa-cart-plus mr-1"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Top Brands --}}
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 mb-8 reveal text-center">Brand Terpercaya</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
            @php
                $brands = [
                    ['name' => 'Samsung', 'icon' => 'mobile-alt', 'color' => 'blue'],
                    ['name' => 'Apple', 'icon' => 'apple-alt', 'color' => 'slate'],
                    ['name' => 'Nike', 'icon' => 'running', 'color' => 'orange'],
                    ['name' => 'Adidas', 'icon' => 'tshirt', 'color' => 'stone'],
                    ['name' => 'Sony', 'icon' => 'headphones', 'color' => 'brand'],
                    ['name' => 'Xiaomi', 'icon' => 'android', 'color' => 'green'],
                    ['name' => 'Uniqlo', 'icon' => 'tag', 'color' => 'red'],
                    ['name' => 'Logitech', 'icon' => 'mouse', 'color' => 'cyan'],
                ];
            @endphp
            @foreach ($brands as $brand)
            <a href="#" class="bg-stone-50 rounded-2xl p-4 border border-stone-100 reveal card-lift
                              flex flex-col items-center justify-center gap-2 hover:border-brand-200 h-28">
                <div class="w-10 h-10 rounded-full bg-{{ $brand['color'] }}-100 flex items-center justify-center">
                    <i class="fas fa-{{ $brand['icon'] }} text-{{ $brand['color'] }}-500 text-sm"></i>
                </div>
                <span class="text-[11px] font-semibold text-stone-700">{{ $brand['name'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Newsletter --}}
<section class="py-16 bg-gradient-to-r from-brand-600 via-brand-700 to-accent-700">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-display text-3xl font-bold text-white mb-3">Dapatkan Penawaran Terbaik</h2>
        <p class="text-brand-100/80 mb-8">Subscribe newsletter kami dan dapatkan diskon hingga 50% untuk pembelanjaan pertama Anda.</p>
        <form class="flex gap-3 max-w-md mx-auto">
            <div class="flex-1 relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                <input type="email" placeholder="Masukkan email Anda"
                       class="w-full pl-10 pr-4 py-3 rounded-xl border-0 text-sm bg-white/10 text-white placeholder:text-white/50
                              focus:outline-none focus:ring-2 focus:ring-white/30 backdrop-blur-sm">
            </div>
            <button type="submit"
                    class="px-6 py-3 bg-white text-brand-700 font-semibold text-sm rounded-xl hover:bg-brand-50 transition-colors shrink-0">
                Subscribe
            </button>
        </form>
    </div>
</section>

{{-- Final CTA --}}
<section class="py-20 bg-gradient-to-br from-stone-900 via-stone-900 to-brand-950 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 -left-20 w-80 h-80 bg-brand-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 right-10 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-display text-4xl lg:text-5xl font-bold text-white mb-4">Siap Mulai Belanja?</h2>
        <p class="text-stone-400 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
            Jelajahi jutaan produk dari ribuan penjual terpercaya. Pengiriman cepat, pembayaran aman, dan garansi produk.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('products.index') }}"
               class="btn-gradient px-8 py-4 rounded-xl text-white font-semibold text-sm shadow-xl shadow-brand-600/30 inline-flex items-center gap-2">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja Sekarang
            </a>
            <a href="#"
               class="px-8 py-4 rounded-xl border-2 border-white/25 text-white font-semibold text-sm
                      hover:bg-white/10 transition-colors backdrop-blur-sm inline-flex items-center gap-2">
                <i class="fas fa-play-circle"></i> Cara Kerja
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function flashTimer() {
        return {
            end: new Date().getTime() + (4 * 3600 * 1000) + (23 * 60 * 1000) + (45 * 1000),
            hours: 0, minutes: 0, seconds: 0,
            pad(n) { return String(n).padStart(2, '0'); },
            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
            },
            tick() {
                const diff = Math.max(0, Math.floor((this.end - new Date().getTime()) / 1000));
                this.hours = Math.floor(diff / 3600);
                this.minutes = Math.floor((diff % 3600) / 60);
                this.seconds = diff % 60;
            }
        }
    }
</script>
@endpush
