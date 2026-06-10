@extends('layouts.storefront')

@section('title', $product->meta_title ?: $product->name . ' — ' . config('app.name'))
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160))
@section('og_image', $product->thumbnail_img ? asset($product->thumbnail_img) : asset('marketing/products/placeholder.jpg'))

@push('styles')
<style>
    .product-main-image {
        aspect-ratio: 1;
        background: linear-gradient(135deg, #eef2ff 0%, #fae8ff 100%);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }
    .product-main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s cubic-bezier(.16,1,.3,1);
    }
    .product-main-image:hover img { transform: scale(1.15); }
    .thumbnail-btn {
        width: 68px;
        height: 68px;
        border-radius: 10px;
        border: 2px solid transparent;
        cursor: pointer;
        overflow: hidden;
        transition: border-color .2s;
        flex-shrink: 0;
    }
    .thumbnail-btn.active { border-color: #6366f1; }
    .thumbnail-btn:hover { border-color: #c7d2fe; }
    .thumbnail-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .variant-btn {
        padding: 8px 16px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #57534e;
        transition: all .2s;
        background: white;
        white-space: nowrap;
    }
    .variant-btn:hover { border-color: #c7d2fe; background: #eef2ff; }
    .variant-btn.active { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; font-weight: 600; }
    .variant-btn:disabled { opacity: .4; cursor: not-allowed; }
    .quantity-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        transition: all .15s;
    }
    .quantity-btn:hover { background: #f9fafb; border-color: #d1d5db; }
    .quantity-btn:active { background: #eef2ff; }
    .tab-btn.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
    }
    .review-star { color: #f59e0b; }
    .review-bar {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }
    .review-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
        border-radius: 3px;
    }
    .discount-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    @media (max-width: 1023px) {
        .product-main-image { aspect-ratio: 4/3; }
    }
</style>
@endpush

@php
    $effectivePrice = $product->unit_price - ($product->discount ?? 0);
    if ($product->discount_type === 'percent') {
        $effectivePrice = $product->unit_price * (1 - ($product->discount / 100));
    }
    $hasDiscount = ($effectivePrice < $product->unit_price && $product->unit_price > 0);
    $discountPercent = $product->unit_price > 0 ? round(($product->unit_price - $effectivePrice) / $product->unit_price * 100) : 0;
    $variantStocks = $product->variant_product ? $product->stocks : collect([]);
    $totalStock = $variantStocks->sum('qty') ?: 0;
    $mainImage = $product->thumbnail_img;
    $additionalImages = is_array($product->photos) ? $product->photos : json_decode($product->photos ?? '[]', true);
    $allImages = array_filter(array_merge($mainImage ? [$mainImage] : [], $additionalImages));
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6" x-data="productDetail(@json([
    'id' => $product->id,
    'name' => $product->name,
    'price' => $effectivePrice,
    'originalPrice' => $product->unit_price,
    'variantProduct' => $product->variant_product,
    'stocks' => $variantStocks->map(fn($s) => [
        'id' => $s->id,
        'variant' => $s->variant,
        'sku' => $s->sku,
        'price' => $s->price ?: $effectivePrice,
        'qty' => $s->qty,
        'image' => $s->image,
        'color_code' => $s->color_code,
    ])->toArray(),
    'images' => $allImages,
]))">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        @if($product->category)
            <a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-brand-600 transition-colors">{{ $product->category->name }}</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
        @endif
        <span class="text-stone-600 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    {{-- Product Detail --}}
    <div class="grid lg:grid-cols-2 gap-8 mb-12">
        {{-- Left: Image Gallery --}}
        <div class="reveal">
            <div class="product-main-image flex items-center justify-center mb-4 relative bg-gradient-to-br from-brand-50 to-accent-50">
                @if($mainImage)
                    <img :src="currentImage || '{{ asset($mainImage) }}'" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-box text-[120px] text-brand-200"></i>
                @endif
                @if($hasDiscount)
                    <span class="discount-badge absolute top-4 left-4 text-white text-xs font-bold px-3 py-1 rounded-lg">
                        -{{ $discountPercent }}%
                    </span>
                @endif
                <button class="absolute top-4 right-4 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center
                               text-stone-400 hover:text-red-500 transition-colors shadow-sm"
                        @click="toggleWishlist()">
                    <i class="far fa-heart text-sm"></i>
                </button>
            </div>

            {{-- Thumbnails --}}
            @if(count($allImages) > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($allImages as $i => $img)
                <div class="thumbnail-btn {{ $i === 0 ? 'active' : '' }} bg-gradient-to-br from-brand-50 to-accent-50 flex items-center justify-center"
                     @click="currentImage = '{{ asset($img) }}'; document.querySelectorAll('.thumbnail-btn').forEach(el => el.classList.remove('active')); $el.classList.add('active')">
                    <img src="{{ asset($img) }}" alt="Thumbnail {{ $i+1 }}" loading="lazy">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Product Info --}}
        <div class="reveal">
            <h1 class="text-xl lg:text-2xl font-bold text-stone-900 mb-3 leading-snug">
                {{ $product->name }}
            </h1>

            {{-- Rating + Sold --}}
            <div class="flex items-center gap-3 mb-4 flex-wrap">
                @php $avgRating = $product->reviews->avg('rating') ?? $product->rating; $reviewCount = $product->reviews->count() ?: 0; @endphp
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-sm {{ $i <= round($avgRating) ? 'review-star' : 'text-stone-300' }}"></i>
                    @endfor
                    <span class="font-bold text-stone-700 ml-1.5">{{ number_format($avgRating, 1) }}</span>
                </div>
                @if($reviewCount > 0)
                <span class="text-stone-300">|</span>
                <a href="#reviews" class="text-brand-600 text-sm hover:underline">{{ number_format($reviewCount) }} Ulasan</a>
                @endif
                @if($product->num_of_sale > 0)
                <span class="text-stone-300">|</span>
                <span class="text-sm text-stone-500">{{ number_format($product->num_of_sale) }}+ Terjual</span>
                @endif
            </div>

            {{-- Price --}}
            <div class="bg-stone-50 rounded-2xl p-5 mb-5">
                <div class="flex items-baseline gap-3 mb-1">
                    <span class="text-3xl font-extrabold text-brand-600">
                        Rp {{ number_format($effectivePrice, 0, ',', '.') }}
                    </span>
                    @if($hasDiscount)
                    <span class="text-stone-400 line-through text-lg">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</span>
                    <span class="discount-badge text-white text-xs font-bold px-2.5 py-1 rounded-lg">-{{ $discountPercent }}%</span>
                    @endif
                </div>
                @if($hasDiscount)
                <p class="text-xs text-stone-500">Hemat <span class="font-semibold text-red-500">Rp {{ number_format($product->unit_price - $effectivePrice, 0, ',', '.') }}</span></p>
                @endif
            </div>

            {{-- Short Description --}}
            @if($product->description)
            <div class="text-sm text-stone-600 leading-relaxed mb-5 prose prose-sm max-w-none line-clamp-3">
                {!! Str::limit(strip_tags($product->description), 300) !!}
            </div>
            @endif

            {{-- Variant Selector --}}
            @if($product->variant_product && $variantStocks->count() > 0)
            <div class="mb-5">
                <h4 class="text-xs font-semibold text-stone-700 mb-2.5">
                    Pilih Varian: <span class="text-stone-500 font-normal" x-text="selectedVariant ? selectedVariant.variant : 'Pilih varian'"></span>
                </h4>
                <div class="flex gap-2 flex-wrap">
                    @foreach($variantStocks as $stock)
                    <button @click="selectVariant({{ $stock->id }})"
                            :class="selectedVariant?.id === {{ $stock->id }} ? 'variant-btn active' : 'variant-btn'"
                            :disabled="{{ $stock->qty <= 0 ? 'true' : 'false' }}">
                        @if($stock->color_code)
                        <span class="inline-block w-3 h-3 rounded-full mr-1.5 align-middle" style="background: {{ $stock->color_code }}; vertical-align: -1px;"></span>
                        @endif
                        {{ $stock->variant }}
                        @if($stock->price && $stock->price != $effectivePrice)
                        <span class="text-[10px] text-stone-400 ml-1">(Rp {{ number_format($stock->price, 0, ',', '.') }})</span>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity + Add to Cart --}}
            <div class="flex items-center gap-3 mb-5 flex-wrap" x-data="{ qty: 1 }">
                <div class="flex items-center gap-0 border border-stone-200 rounded-xl overflow-hidden">
                    <button @click="qty = Math.max(1, qty - 1)" class="quantity-btn rounded-none border-0">−</button>
                    <input type="number" x-model="qty" min="1" max="99"
                           class="w-14 text-center text-sm font-semibold text-stone-800 border-x border-stone-200 py-2
                                  focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <button @click="qty = Math.min(99, qty + 1)" class="quantity-btn rounded-none border-0">+</button>
                </div>
                <span class="text-xs text-stone-400">
                    Stok:
                    @if($product->variant_product)
                        <span class="font-semibold" :class="availableStock > 0 ? 'text-green-600' : 'text-red-500'" x-text="availableStock > 0 ? 'Tersedia (' + availableStock + ' unit)' : 'Habis'"></span>
                    @else
                        <span class="font-semibold {{ $totalStock > 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $totalStock > 0 ? 'Tersedia (' . $totalStock . ' unit)' : 'Habis' }}
                        </span>
                    @endif
                </span>
            </div>

            <div class="flex gap-3 flex-wrap">
                <form action="{{ route('cart.add') }}" method="POST" class="flex-1 min-w-[200px]">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="price" :value="selectedPrice">
                    <input type="hidden" name="variation" :value="selectedVariant?.variant || ''">
                    <input type="hidden" name="quantity" x-model="qty">
                    <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-semibold rounded-xl
                                   hover:from-brand-600 hover:to-brand-700 transition-all hover:shadow-xl hover:shadow-brand-500/30 text-sm">
                        <i class="fas fa-shopping-cart mr-2"></i> Tambah ke Keranjang
                    </button>
                </form>
                <button @click="toggleWishlist()"
                        class="px-5 py-3.5 border-2 border-stone-200 text-stone-600 hover:text-red-500 hover:border-red-200 rounded-xl
                               font-semibold text-sm transition-colors">
                    <i class="far fa-heart mr-1.5"></i> Wishlist
                </button>
            </div>

            {{-- Share --}}
            <div class="flex items-center gap-3 mt-5 pt-4 border-t border-stone-100">
                <span class="text-xs text-stone-500">Bagikan:</span>
                <div class="flex gap-2">
                    <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center text-blue-600 transition-colors text-sm">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}" target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 flex items-center justify-center text-sky-500 transition-colors text-sm">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($product->name . ' — ' . url()->current()) }}" target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-green-50 hover:bg-green-100 flex items-center justify-center text-green-600 transition-colors text-sm">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <button @click="navigator.clipboard.writeText(window.location.href)"
                       class="w-8 h-8 rounded-lg bg-stone-100 hover:bg-stone-200 flex items-center justify-center text-stone-500 transition-colors text-sm">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>

            {{-- Seller + Info Cards --}}
            <div class="grid sm:grid-cols-2 gap-3 mt-6">
                @if($product->brand)
                <div class="bg-stone-50 rounded-xl p-4 flex items-center gap-3 border border-stone-100">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-tag text-brand-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-stone-800">{{ $product->brand->name }}</p>
                        <p class="text-[10px] text-stone-400">Brand Resmi</p>
                    </div>
                    <a href="{{ route('products.brand', $product->brand->slug) }}" class="ml-auto text-[10px] text-brand-600 font-semibold hover:underline shrink-0">Lihat</a>
                </div>
                @endif
                <div class="bg-green-50 rounded-xl p-4 flex items-center gap-3 border border-green-100">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-shield-alt text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-800">Produk Original</p>
                        <p class="text-[10px] text-green-600">Garansi & Pengembalian</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-lg ml-auto"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs: Description, Reviews --}}
    <div class="bg-white rounded-2xl border border-stone-100 mb-12 overflow-hidden reveal" x-data="{ tab: 'description' }">
        <div class="flex border-b border-stone-100 overflow-x-auto">
            <button @click="tab = 'description'"
                    :class="tab === 'description' ? 'tab-btn active' : 'text-stone-500 hover:text-stone-700 border-b-2 border-transparent'"
                    class="px-5 py-3.5 text-sm font-semibold transition-colors whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-align-left text-xs"></i> Deskripsi
            </button>
            <button @click="tab = 'reviews'"
                    :class="tab === 'reviews' ? 'tab-btn active' : 'text-stone-500 hover:text-stone-700 border-b-2 border-transparent'"
                    class="px-5 py-3.5 text-sm font-semibold transition-colors whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-star text-xs"></i> Ulasan
                @if($reviewCount > 0)
                <span class="text-[10px] text-stone-400">({{ number_format($reviewCount) }})</span>
                @endif
            </button>
        </div>

        <div class="p-6 lg:p-8">
            {{-- Description Tab --}}
            <div x-show="tab === 'description'" class="prose prose-sm max-w-none text-stone-600">
                @if($product->description)
                    {!! $product->description !!}
                @else
                    <p class="text-stone-400 italic">Belum ada deskripsi untuk produk ini.</p>
                @endif

                @if($product->tags)
                <div class="mt-6 pt-4 border-t border-stone-100">
                    <h4 class="text-xs font-semibold text-stone-500 mb-2">Tags:</h4>
                    <div class="flex gap-1.5 flex-wrap">
                        @foreach(explode(',', $product->tags) as $tag)
                        <span class="px-2.5 py-1 bg-stone-100 text-stone-600 text-[11px] rounded-md">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Reviews Tab --}}
            <div x-show="tab === 'reviews'" id="reviews">
                @if($reviewCount > 0)
                @php
                    $ratingDistribution = [];
                    for($i = 5; $i >= 1; $i--) {
                        $count = $product->reviews->where('rating', $i)->count();
                        $ratingDistribution[$i] = $reviewCount > 0 ? round($count / $reviewCount * 100) : 0;
                    }
                @endphp
                <div class="grid md:grid-cols-3 gap-8 mb-8">
                    <div class="text-center">
                        <p class="text-5xl font-extrabold text-stone-900 mb-1">{{ number_format($avgRating, 1) }}</p>
                        <div class="flex items-center justify-center gap-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star review-star text-lg {{ $i <= round($avgRating) ? '' : 'text-stone-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-stone-400">{{ number_format($reviewCount) }} ulasan</p>
                    </div>
                    <div class="col-span-2 space-y-2">
                        @foreach($ratingDistribution as $star => $pct)
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-8 text-right text-stone-600">{{ $star }}</span>
                            <i class="fas fa-star text-[10px] review-star"></i>
                            <div class="flex-1 review-bar">
                                <div class="review-bar-fill" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-10 text-stone-400">{{ $pct }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($product->reviews->take(5) as $review)
                    <div class="border-b border-stone-100 pb-4">
                        <div class="flex items-center gap-2 mb-1.5">
                            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 text-xs font-bold">
                                {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-stone-800">{{ $review->user?->name ?? 'Anonim' }}</p>
                                <p class="text-[10px] text-stone-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="ml-auto flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-[10px] {{ $i <= $review->rating ? 'review-star' : 'text-stone-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-stone-600 leading-relaxed">{{ $review->comment ?? 'Tidak ada komentar.' }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <i class="far fa-comment-dots text-5xl text-stone-200 mb-3"></i>
                    <p class="text-stone-500 text-sm">Belum ada ulasan untuk produk ini.</p>
                    <p class="text-stone-400 text-xs mt-1">Jadilah yang pertama memberikan ulasan!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
    <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display text-xl lg:text-2xl font-bold text-stone-900 reveal">Produk Terkait</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($relatedProducts as $related)
            <div class="bg-white rounded-2xl overflow-hidden card-lift border border-stone-100 hover:border-brand-200 group cursor-pointer reveal">
                <a href="{{ route('products.show', $related->slug) }}" class="block">
                    <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-40 flex items-center justify-center">
                        @if($related->thumbnail_img)
                            <img src="{{ asset($related->thumbnail_img) }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <i class="fas fa-box text-4xl text-brand-200 group-hover:scale-110 transition-transform duration-500"></i>
                        @endif
                    </div>
                </a>
                <div class="p-3">
                    <a href="{{ route('products.show', $related->slug) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 mb-1.5 block hover:text-brand-600">
                        {{ $related->name }}
                    </a>
                    <div class="flex items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-[9px] {{ $i <= round($related->rating) ? 'review-star' : 'text-stone-300' }}"></i>
                        @endfor
                    </div>
                    <span class="font-bold text-brand-600 text-sm">Rp {{ number_format($related->unit_price, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function productDetail(config) {
        return {
            currentImage: config.images[0] ? `/${config.images[0]}` : null,
            selectedVariant: null,
            selectedPrice: config.price,
            availableStock: {{ $totalStock }},
            stocks: config.stocks,

            selectVariant(stockId) {
                const stock = this.stocks.find(s => s.id === stockId);
                if (!stock || stock.qty <= 0) return;
                this.selectedVariant = stock;
                this.selectedPrice = stock.price || config.price;
                this.availableStock = stock.qty;
            },
            toggleWishlist() {
                alert('Fitur wishlist akan segera hadir!');
            },
            get selectedPriceFormatted() {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(this.selectedPrice);
            }
        }
    }
</script>

{{-- JSON-LD Product Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "description": "{{ \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 300) }}",
    "image": "{{ $mainImage ? asset($mainImage) : '' }}",
    "sku": "{{ $variantStocks->first()?->sku ?? '' }}",
    @if($product->brand)
    "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand->name }}"
    },
    @endif
    "offers": {
        "@type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "IDR",
        "price": "{{ $effectivePrice }}",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "{{ $totalStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
    }
    @if($reviewCount > 0)
    ,
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ number_format($avgRating, 1) }}",
        "reviewCount": "{{ $reviewCount }}",
        "bestRating": "5",
        "worstRating": "1"
    }
    @endif
}
</script>
@endpush
