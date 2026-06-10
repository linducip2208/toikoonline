@extends('layouts.storefront')

@section('title', ($title ?? 'Daftar Produk') . ' — ' . config('app.name'))
@section('meta_description', 'Jelajahi katalog produk lengkap ' . config('app.name') . '. Filter berdasarkan kategori, harga, brand, dan rating. Temukan produk terbaik untuk kebutuhan Anda.')

@push('styles')
<style>
    .star-gold { color: #f59e0b; }
    .filter-checkbox:checked + label {
        background: #eef2ff;
        color: #4f46e5;
        border-color: #4f46e5;
    }
    .sidebar-backdrop { display: none; }
    @media (max-width: 1023px) {
        .sidebar-backdrop.open { display: block; }
        .sidebar { transform: translateX(-100%); transition: transform .3s cubic-bezier(.16,1,.3,1); position: fixed; top: 0; left: 0; z-index: 50; height: 100vh; overflow-y: auto; }
        .sidebar.open { transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<div x-data="productListing()" class="max-w-7xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        @if(isset($category))
            <span class="text-stone-600 font-medium">{{ $category->name }}</span>
        @elseif(isset($brand))
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-stone-400">Brand</span>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-stone-600 font-medium">{{ $brand->name }}</span>
        @elseif(isset($query))
            <span class="text-stone-600 font-medium">Pencarian: "{{ $query }}"</span>
        @else
            <span class="text-stone-600 font-medium">Produk</span>
        @endif
    </nav>

    <div class="flex gap-6">

        {{-- Sidebar Overlay (Mobile) --}}
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="absolute inset-0 bg-stone-900/50"></div>
        </div>

        {{-- Left Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="sidebar w-72 lg:w-64 shrink-0 bg-white rounded-2xl border border-stone-200 lg:border-stone-100
                      h-[calc(100vh-5rem)] lg:h-fit overflow-y-auto shadow-xl lg:shadow-sm p-5
                      lg:translate-x-0 lg:relative lg:sticky lg:top-20">
            <div class="flex items-center justify-between mb-5 lg:hidden">
                <h4 class="font-semibold text-stone-800">Filter</h4>
                <button @click="sidebarOpen = false" class="text-stone-400 hover:text-stone-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Category Filter --}}
            @if($categories->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-3">Kategori</h4>
                <div class="space-y-1">
                    @foreach($categories as $cat)
                    <a href="{{ route('products.category', $cat->slug) }}"
                       class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-stone-50 text-sm transition-colors
                              {{ (isset($category) && $category->id === $cat->id) ? 'text-brand-600 bg-brand-50 font-medium' : 'text-stone-700' }}">
                        <span class="flex-1">{{ $cat->name }}</span>
                        <i class="fas fa-chevron-right text-[8px] opacity-40"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-3">Harga</h4>
                <form method="GET" action="{{ url()->current() }}" id="priceFilter">
                    <div class="flex gap-2 mb-3">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}"
                               class="w-full px-3 py-2 border border-stone-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                        <span class="text-stone-300 self-center">—</span>
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}"
                               class="w-full px-3 py-2 border border-stone-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                    </div>
                    <button type="submit" class="w-full py-2 bg-brand-600 text-white text-xs font-semibold rounded-xl hover:bg-brand-700 transition-colors">
                        Terapkan
                    </button>
                </form>
            </div>

            {{-- Brand Filter --}}
            @if($brands->count() > 0)
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-3">Brand</h4>
                <div class="space-y-1 max-h-44 overflow-y-auto">
                    @foreach($brands as $br)
                    <a href="{{ route('products.brand', $br->slug) }}"
                       class="flex items-center gap-2 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-stone-50 text-sm transition-colors
                              {{ (isset($brand) && $brand->id === $br->id) ? 'text-brand-600 bg-brand-50 font-medium' : 'text-stone-700' }}">
                        <span>{{ $br->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <a href="{{ route('products.index') }}"
               class="block w-full py-2 text-center text-stone-500 text-xs hover:text-stone-700 transition-colors">
                Reset Filter
            </a>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">

            {{-- Top Bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5 bg-white rounded-2xl border border-stone-100 p-3 lg:p-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-stone-600 hover:text-brand-600 p-1">
                        <i class="fas fa-filter text-lg"></i>
                    </button>
                    <span class="text-xs text-stone-500">
                        Menampilkan <span class="font-semibold text-stone-800">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> dari
                        <span class="font-semibold text-stone-800">{{ number_format($products->total()) }}</span> produk
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <select name="sort" onchange="window.location.href = updateQueryString('sort', this.value)"
                            class="text-xs border border-stone-200 rounded-lg px-3 py-2 bg-white text-stone-700
                                   focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                        @php $sortOptions = [
                            'latest' => 'Terbaru',
                            'price-asc' => 'Harga Rendah - Tinggi',
                            'price-desc' => 'Harga Tinggi - Rendah',
                            'popular' => 'Terlaris',
                            'rating' => 'Rating Tertinggi',
                        ]; @endphp
                        @foreach($sortOptions as $key => $label)
                        <option value="{{ $key }}" {{ ($sort ?? 'latest') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Product Grid --}}
            @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                <div class="bg-white rounded-2xl overflow-hidden card-lift border border-stone-100 hover:border-brand-200 group cursor-pointer reveal">
                    <div class="relative product-image">
                        <a href="{{ route('products.show', $product->slug) }}">
                            <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-44 flex items-center justify-center overflow-hidden">
                                @if($product->thumbnail_img)
                                    <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                @else
                                    <i class="fas fa-box text-4xl text-brand-200 group-hover:scale-110 transition-transform duration-500"></i>
                                @endif
                            </div>
                        </a>
                        @php
                            $effPrice = $product->unit_price - ($product->discount ?? 0);
                            if(($product->discount_type ?? '') === 'percent') {
                                $effPrice = $product->unit_price * (1 - ($product->discount / 100));
                            }
                            $hasDisc = $effPrice < $product->unit_price && $product->unit_price > 0;
                            $discPct = $product->unit_price > 0 ? round(($product->unit_price - $effPrice) / $product->unit_price * 100) : 0;
                        @endphp
                        @if($hasDisc)
                        <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md"
                             style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            -{{ $discPct }}%
                        </div>
                        @endif
                        <button class="absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center
                                       text-stone-400 hover:text-red-500 transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                            <i class="far fa-heart text-xs"></i>
                        </button>
                    </div>
                    <div class="p-3">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 mb-1.5 block hover:text-brand-600 transition-colors leading-relaxed">
                            {{ $product->name }}
                        </a>
                        <div class="flex items-center gap-1 mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-[9px] {{ $i <= round($product->rating) ? 'star-gold' : 'text-stone-300' }}"></i>
                            @endfor
                            @if($product->num_of_sale > 0)
                            <span class="text-[10px] text-stone-400 ml-1">| {{ number_format($product->num_of_sale) }} terjual</span>
                            @endif
                        </div>
                        @if($product->category)
                        <p class="text-[10px] text-stone-400 mb-2">{{ $product->category->name }}</p>
                        @endif
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <span class="font-bold text-brand-600 text-sm">Rp {{ number_format($effPrice, 0, ',', '.') }}</span>
                            @if($hasDisc)
                            <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="price" value="{{ $effPrice }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                    class="w-full py-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-[11px] font-semibold rounded-lg
                                           hover:from-brand-600 hover:to-brand-700 transition-all hover:shadow-lg hover:shadow-brand-500/25">
                                <i class="fas fa-cart-plus mr-1"></i> Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="flex items-center justify-center gap-1 mt-8">
                @if($products->onFirstPage())
                    <span class="w-9 h-9 rounded-lg border border-stone-200 text-stone-300 flex items-center justify-center text-sm cursor-not-allowed">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </span>
                @else
                    <a href="{{ $products->previousPageUrl() }}" class="w-9 h-9 rounded-lg border border-stone-200 text-stone-400 flex items-center justify-center text-sm hover:bg-stone-50">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </a>
                @endif

                @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="w-9 h-9 rounded-lg text-sm font-semibold flex items-center justify-center
                              {{ $page === $products->currentPage() ? 'bg-brand-600 text-white' : 'border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="w-9 h-9 rounded-lg border border-stone-200 text-stone-400 flex items-center justify-center text-sm hover:bg-stone-50">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <span class="w-9 h-9 rounded-lg border border-stone-200 text-stone-300 flex items-center justify-center text-sm cursor-not-allowed">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </span>
                @endif
            </div>
            @endif

            @else
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
                <i class="fas fa-box-open text-6xl text-stone-200 mb-4"></i>
                <h3 class="text-lg font-semibold text-stone-500 mb-2">Tidak ada produk ditemukan</h3>
                <p class="text-sm text-stone-400 mb-6">Coba ubah filter atau kata kunci pencarian Anda.</p>
                <a href="{{ route('products.index') }}" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors">
                    Lihat Semua Produk
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function productListing() {
        return {
            sidebarOpen: false,
        }
    }
    function updateQueryString(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        return url.toString();
    }
</script>
@endpush
