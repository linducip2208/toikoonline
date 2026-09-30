@extends('layouts.storefront')

@section('title', 'Flash Deal — ' . $deal->title . ' — TokoOnline')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-stone-600 font-medium">Flash Deal: {{ $deal->title }}</span>
    </nav>

    <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/4 blur-3xl"></div>
        <div class="relative">
            <h1 class="font-display text-3xl lg:text-4xl font-bold mb-2"><i class="fas fa-bolt mr-2"></i>{{ $deal->title }}</h1>
            <p class="text-white/80 text-lg mb-4">Jangan sampai kehabisan! Diskon besar-besaran untuk produk pilihan.</p>
            <div class="flash-timer-box inline-flex items-center gap-3 px-5 py-2.5 rounded-lg bg-white/15 border border-white/20" x-data="flashTimer({{ $deal->end_date }})">
                <span class="text-sm font-semibold">Berakhir dalam:</span>
                <div class="flex gap-1.5 text-lg font-mono font-bold">
                    <span class="bg-white/20 px-2 py-1 rounded" x-text="pad(hours)">00</span><span>:</span>
                    <span class="bg-white/20 px-2 py-1 rounded" x-text="pad(minutes)">00</span><span>:</span>
                    <span class="bg-white/20 px-2 py-1 rounded" x-text="pad(seconds)">00</span>
                </div>
            </div>
        </div>
    </div>

    @if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl overflow-hidden card-lift border border-stone-100 hover:border-red-200 group">
            <a href="{{ route('products.show', $product->slug) }}" class="block relative">
                <div class="bg-gradient-to-br from-red-50 to-orange-50 h-48 flex items-center justify-center overflow-hidden">
                    @if($product->thumbnail_img)
                        <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                    @else
                        <i class="fas fa-box text-5xl text-red-200 group-hover:scale-110 transition-transform duration-500"></i>
                    @endif
                </div>
                @php
                    $discPct = $product->deal_discount_type === 'percent' ? round($product->deal_discount) : round(($product->deal_discount / $product->unit_price) * 100);
                @endphp
                <div class="discount-badge absolute top-2 left-2 text-white text-xs font-bold px-2.5 py-1 rounded-lg" style="background:linear-gradient(135deg,#ef4444,#dc2626);">-{{ $discPct }}%</div>
            </a>
            <div class="p-3.5">
                <a href="{{ route('products.show', $product->slug) }}" class="text-sm font-semibold text-stone-800 line-clamp-2 mb-2 block hover:text-brand-600">{{ $product->name }}</a>
                <div class="flex items-center gap-2 mb-3">
                    <span class="font-bold text-red-600 text-base">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                    <span class="text-xs text-stone-400 line-through">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</span>
                </div>
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="price" value="{{ $product->effective_price }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="w-full py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-xl hover:shadow-lg hover:shadow-red-500/30 transition-all">
                        <i class="fas fa-bolt mr-1"></i> Beli Sekarang
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
        <i class="fas fa-box-open text-6xl text-stone-200 mb-4"></i>
        <h3 class="text-lg font-semibold text-stone-500">Tidak ada produk flash deal</h3>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function flashTimer(endTimestamp) {
        return {
            end: endTimestamp * 1000, hours: 0, minutes: 0, seconds: 0, now: Date.now(),
            pad(n) { return String(n).padStart(2, '0'); },
            init() { this.tick(); this.interval = setInterval(() => { this.now = Date.now(); this.tick(); }, 1000); },
            tick() { const diff = Math.max(0, Math.floor((this.end - this.now) / 1000)); this.hours = Math.floor(diff / 3600); this.minutes = Math.floor((diff % 3600) / 60); this.seconds = diff % 60; }
        }
    }
</script>
@endpush
