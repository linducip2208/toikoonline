<div class="bg-stone-50 rounded-2xl overflow-hidden card-lift reveal group cursor-pointer border border-stone-100 hover:border-brand-200" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
    <div class="relative product-image">
        <a href="{{ route('products.show', $product->slug) }}">
            <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-48 flex items-center justify-center overflow-hidden relative">
                @php
                    $hoverImg = null;
                    if ($product->photos) {
                        $photos = is_array($product->photos) ? $product->photos : json_decode($product->photos, true);
                        if (is_array($photos) && count($photos) > 0 && $photos[0] !== $product->thumbnail_img) {
                            $hoverImg = $photos[0];
                        }
                    }
                @endphp
                @if($product->thumbnail_img)
                    <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-all duration-500 ease-out"
                         :class="hover ? 'opacity-0 scale-110' : 'opacity-100 scale-100'"
                         loading="lazy">
                    @if($hoverImg)
                    <img src="{{ asset($hoverImg) }}" alt="{{ $product->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-all duration-500 ease-out"
                         :class="hover ? 'opacity-100 scale-110' : 'opacity-0 scale-100'"
                         loading="lazy">
                    @endif
                @else
                    <i class="fas fa-box text-5xl text-brand-200 group-hover:scale-110 transition-transform duration-500"></i>
                @endif
            </div>
        </a>

        @php
            $effPrice = $product->unit_price;
            $hasDisc = false;
            $discPct = 0;

            if ($product->discount && $product->discount > 0) {
                if ($product->discount_type === 'percent') {
                    $effPrice = $product->unit_price * (1 - $product->discount / 100);
                } else {
                    $effPrice = $product->unit_price - $product->discount;
                }
                $hasDisc = ($effPrice < $product->unit_price && $product->unit_price > 0);
                $discPct = $product->unit_price > 0 ? round(($product->unit_price - $effPrice) / $product->unit_price * 100) : 0;
            }
        @endphp

        @if($hasDisc)
        <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">-{{ $discPct }}%</div>
        @endif

        @if($showNewBadge ?? false)
        <span class="absolute top-2 {{ $hasDisc ? 'left-14' : 'left-2' }} bg-gradient-to-r from-green-500 to-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ __('storefront.badge_new') }}</span>
        @endif

        @if($showBestBadge ?? false)
        <span class="absolute top-2 {{ $hasDisc ? 'left-14' : 'left-2' }} bg-gradient-to-r from-warm-400 to-warm-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
            <i class="fas fa-fire text-[8px] mr-0.5"></i>{{ __('storefront.badge_best') }}
        </span>
        @endif

        {{-- Action Buttons --}}
        <div class="absolute right-2 top-2 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button @click.stop="toggleWishlist({{ $product->id }})"
                    :class="wishlisted({{ $product->id }}) ? 'text-red-500 bg-red-50' : 'text-stone-400 hover:text-red-500 bg-white/90'"
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-colors shadow-sm">
                <i class="text-xs" :class="wishlisted({{ $product->id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
            </button>
            <button @click.stop="toggleCompare({{ $product->id }})"
                    :class="compared({{ $product->id }}) ? 'text-blue-500 bg-blue-50' : 'text-stone-400 hover:text-blue-500 bg-white/90'"
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-colors shadow-sm">
                <i class="fas fa-balance-scale text-xs"></i>
            </button>
            <button @click.stop="openQuickView({{ $product->id }})"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-stone-400 hover:text-brand-600 bg-white/90 transition-colors shadow-sm">
                <i class="fas fa-eye text-xs"></i>
            </button>
        </div>
    </div>
    <div class="p-3.5">
        <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-stone-800 line-clamp-2 mb-2 leading-relaxed block hover:text-brand-600">
            {{ $product->name }}
        </a>
        <div class="flex items-center gap-1 mb-1.5">
            @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star text-[10px] {{ $i <= round($product->rating ?: 0) ? 'star-gold' : 'text-stone-300' }}"></i>
            @endfor
            @if($product->num_of_sale > 0)
            <span class="text-[10px] text-stone-400 ml-1">| {{ number_format($product->num_of_sale) }} {{ __('common.sold') }}</span>
            @endif
        </div>
        <div class="flex items-center gap-1.5 mb-3">
            <span class="font-bold text-brand-600 text-sm">Rp {{ number_format($effPrice, 0, ',', '.') }}</span>
            @if($hasDisc)
            <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</span>
            @endif
        </div>
        @if($product->variant_product)
        <a href="{{ route('products.show', $product->slug) }}"
           class="block w-full py-2 text-center bg-gradient-to-r from-brand-50 to-accent-50 text-brand-700 text-xs font-semibold rounded-lg
                  border border-brand-200 hover:bg-gradient-to-r hover:from-brand-100 hover:to-accent-100 transition-all">
            <i class="fas fa-cog mr-1"></i> {{ __('storefront.choose_variant') }}
        </a>
        @else
        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="price" value="{{ $effPrice }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    class="w-full py-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-semibold rounded-lg
                           hover:from-brand-600 hover:to-brand-700 transition-all hover:shadow-lg hover:shadow-brand-500/25">
                <i class="fas fa-cart-plus mr-1"></i> {{ __('storefront.add_to_cart') }}
            </button>
        </form>
        @endif
    </div>
</div>
