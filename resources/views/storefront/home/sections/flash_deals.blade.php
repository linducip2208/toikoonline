@if($settings['flash_deal'] === '1' && $flashDeals->count())
@foreach($flashDeals as $deal)
@if($deal->flashDealProducts->count())
@php
    $dealEnd = $deal->end_date;
    $dealProducts = $deal->flashDealProducts->map(function($fdp) {
        $product = $fdp->product;
        if(!$product) return null;
        $effPrice = $fdp->discount_type === 'percent' ? $product->unit_price * (1 - $fdp->discount / 100) : $product->unit_price - $fdp->discount;
        return (object)[
            'id' => $product->id, 'name' => $product->name, 'slug' => $product->slug,
            'price' => $product->unit_price, 'effectivePrice' => max(0, $effPrice),
            'discountPercent' => $fdp->discount_type === 'percent' ? round($fdp->discount) : round(($fdp->discount / $product->unit_price) * 100),
            'thumbnail' => $product->thumbnail_img, 'num_of_sale' => $product->num_of_sale,
        ];
    })->filter()->take(6);
@endphp
@if($dealProducts->count())
<section class="py-14 bg-gradient-to-r from-red-50 via-white to-red-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
            <div class="flex items-center gap-4 reveal">
                <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900">
                    <i class="fas fa-bolt text-red-500 mr-2"></i>{{ $deal->title }}
                </h2>
                <div class="flash-timer-box flex items-center gap-3 px-4 py-2 rounded-lg" x-data="flashTimer({{ $dealEnd }})">
                    <span class="text-[11px] font-semibold text-red-600">Berakhir dalam:</span>
                    <div class="flex gap-1.5 text-red-700 font-mono font-bold text-sm">
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(hours)">00</span><span class="text-red-400">:</span>
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(minutes)">00</span><span class="text-red-400">:</span>
                        <span class="bg-red-100 px-1.5 py-0.5 rounded" x-text="pad(seconds)">00</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('flash-deals.show', $deal->slug) }}" class="text-brand-600 text-sm font-semibold hover:underline reveal">Lihat Semua <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($dealProducts as $dProduct)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100 card-lift reveal group cursor-pointer">
                <a href="{{ route('products.show', $dProduct->slug) }}" class="block relative">
                    <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-40 flex items-center justify-center product-image">
                        @if($dProduct->thumbnail)
                        <img src="{{ asset($dProduct->thumbnail) }}" alt="{{ $dProduct->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <i class="fas fa-box text-4xl text-brand-200 group-hover:scale-110 transition-transform duration-500"></i>
                        @endif
                    </div>
                    <div class="discount-badge absolute top-2 left-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">-{{ $dProduct->discountPercent }}%</div>
                </a>
                <div class="p-3">
                    <a href="{{ route('products.show', $dProduct->slug) }}" class="text-xs font-semibold text-stone-800 truncate mb-1 block hover:text-brand-600">{{ $dProduct->name }}</a>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-bold text-brand-600 text-sm">Rp {{ number_format($dProduct->effectivePrice, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-stone-400 line-through">Rp {{ number_format($dProduct->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-400 to-red-500 h-full rounded-full" style="width:{{ min(100, $dProduct->num_of_sale / 20) }}%"></div>
                    </div>
                    <p class="text-[10px] text-stone-400 mt-1">{{ number_format($dProduct->num_of_sale) }}+ terjual</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
@break
@endforeach
@endif
