@extends('layouts.storefront')

@section('title', request('q') ? 'Hasil Pencarian: ' . request('q') . ' — TokoOnline' : 'Cari Produk — TokoOnline')
@section('meta_description', 'Cari produk impian Anda di TokoOnline. Temukan berbagai produk berkualitas dari ribuan penjual terpercaya.')

@push('styles')
<style>
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
        box-shadow: 0 8px 24px -6px rgba(99,102,241,.4);
    }
</style>
@endpush

@section('content')
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-stone-800 font-medium">Pencarian</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Search bar --}}
    <div class="mb-8">
        <form action="{{ url()->current() }}" class="relative max-w-2xl">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-stone-400 text-lg"></i>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Cari produk, brand, atau kategori..."
                   class="w-full pl-14 pr-6 py-4 rounded-2xl border border-stone-300 bg-white text-lg
                          focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                          shadow-sm transition-all">
            <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 btn-gradient text-white px-6 py-2.5 rounded-xl text-sm font-semibold">
                Cari
            </button>
        </form>
    </div>

    @if(request('q'))
        <h1 class="font-display text-2xl sm:text-3xl font-bold text-stone-900 mb-2">
            Hasil pencarian untuk: <span class="text-brand-600">"{{ request('q') }}"</span>
        </h1>

        @if(isset($products) && $products->count() > 0)
            <p class="text-stone-500 mb-6">Ditemukan <strong class="text-stone-700">{{ $products->total() }}</strong> produk</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($products as $product)
                    <div class="product-card bg-white border border-stone-200 rounded-2xl overflow-hidden card-lift reveal">
                        <a href="{{ route('products.show', $product->slug) }}" class="product-image block aspect-square bg-stone-100 overflow-hidden relative">
                            @if($product->thumbnail_img)
                                <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <i class="fas fa-image text-5xl"></i>
                                </div>
                            @endif
                            @if($product->discount > 0)
                                <span class="discount-badge absolute top-3 left-3 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    -{{ $product->discount_type === 'percent' ? $product->discount . '%' : 'Rp' }}
                                </span>
                            @endif
                            @if($product->todays_deal)
                                <span class="absolute top-3 right-3 bg-warm-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">
                                    HOT
                                </span>
                            @endif
                        </a>
                        <div class="p-3 sm:p-4">
                            @if($product->brand)
                                <span class="text-[10px] text-brand-500 font-medium uppercase tracking-wide">{{ $product->brand->name }}</span>
                            @endif
                            <a href="{{ route('products.show', $product->slug) }}" class="block mt-1">
                                <h3 class="font-medium text-stone-900 text-sm leading-snug line-clamp-2 hover:text-brand-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                            </a>
                            <div class="flex items-center gap-1 mt-1.5">
                                <div class="flex items-center text-[9px] star-gold">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="{{ $i < round($product->rating ?? 0) ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="text-[10px] text-stone-400">({{ $product->reviews_count ?? 0 }})</span>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-stone-900 text-sm sm:text-base">
                                    Rp {{ number_format($product->unit_price, 0, ',', '.') }}
                                </span>
                                <button onclick="addToSearchCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->unit_price }})"
                                        class="w-8 h-8 sm:w-9 sm:h-9 btn-gradient text-white rounded-lg flex items-center justify-center text-xs sm:text-sm hover:shadow-lg transition-all">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                            @if($product->num_of_sale > 0)
                                <p class="text-[10px] text-stone-400 mt-1.5">{{ number_format($product->num_of_sale, 0, ',', '.') }} terjual</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->appends(['q' => request('q')])->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-stone-100 flex items-center justify-center">
                    <i class="fas fa-search text-4xl text-stone-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-stone-700 mb-2">Tidak ada hasil untuk "{{ request('q') }}"</h3>
                <p class="text-stone-400 text-sm mb-8">Coba kata kunci lain atau jelajahi kategori di bawah ini.</p>

                <div class="max-w-md mx-auto">
                    <p class="text-sm font-medium text-stone-700 mb-3">Saran pencarian:</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="?q=smartphone" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                            Smartphone
                        </a>
                        <a href="?q=sepatu" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                            Sepatu
                        </a>
                        <a href="?q=tas" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                            Tas
                        </a>
                        <a href="?q=baju" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                            Baju
                        </a>
                        <a href="?q=elektronik" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                            Elektronik
                        </a>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('products.index') }}" class="btn-gradient text-white px-8 py-3 rounded-xl text-sm font-semibold inline-block">
                        <i class="fas fa-th-large mr-2"></i>Lihat Semua Produk
                    </a>
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-20">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-brand-50 flex items-center justify-center">
                <i class="fas fa-search text-4xl text-brand-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-stone-700 mb-2">Cari produk impian Anda</h3>
            <p class="text-stone-400 text-sm mb-8">Ketik kata kunci di atas untuk mulai mencari produk.</p>

            <div class="max-w-md mx-auto">
                <p class="text-sm font-medium text-stone-700 mb-3">Pencarian populer:</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="?q=smartphone" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                        Smartphone
                    </a>
                    <a href="?q=sepatu" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                        Sepatu
                    </a>
                    <a href="?q=tas" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                        Tas
                    </a>
                    <a href="?q=baju" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                        Baju
                    </a>
                    <a href="?q=elektronik" class="px-4 py-2 rounded-xl border border-stone-200 bg-white text-sm text-stone-600 hover:border-brand-400 hover:text-brand-600 transition-colors">
                        Elektronik
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function addToSearchCart(id, name, price) {
        Alpine.store('cart').add({ id, name, price, qty: 1 });
    }
</script>
@endpush
@endsection
