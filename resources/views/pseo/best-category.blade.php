@extends('pseo._layout')

@php
$year = $year ?? date('Y');
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => "10 Produk {$cat->name} Terbaik {$year}",
    'description' => $meta['description'],
    'numberOfItems' => count($products),
    'itemListElement' => $products->map(function($p, $i) {
        return [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $p->name,
            'url' => url("/products/{$p->slug}"),
            'image' => $p->thumbnail ? asset($p->thumbnail->file_name) : null,
        ];
    })->toArray(),
];
@endphp

@section('content')
<div class="bg-gradient-to-br from-brand-900 via-brand-800 to-accent-900 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">{{ __('pseo.rec_badge') }}</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            {{ __('pseo.best_title', ['cat' => $cat->name, 'year' => $year]) }}
        </h1>
        <p class="text-brand-200 text-lg mt-4 max-w-2xl mx-auto leading-relaxed">
            {{ __('pseo.best_intro', ['cat' => $cat->name, 'year' => $year]) }}
        </p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-12">
    @if($products->isEmpty())
        <div class="text-center py-20 text-stone-400">
            <i class="fas fa-box-open text-6xl mb-4 block"></i>
            <p class="text-lg font-medium">{{ __('pseo.best_empty') }}</p>
            <a href="{{ url('/products') }}" class="text-brand-600 hover:underline mt-2 inline-block">{{ __('pseo.browse_all') }} &rarr;</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($products as $index => $product)
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift flex flex-col sm:flex-row gap-6 items-start">
                <div class="flex items-center gap-5 shrink-0">
                    <span class="w-10 h-10 rounded-full bg-brand-50 text-brand-700 font-extrabold text-lg flex items-center justify-center flex-shrink-0 border-2 border-brand-200">
                        {{ $index + 1 }}
                    </span>
                    <div class="w-24 h-24 rounded-xl bg-stone-100 overflow-hidden flex-shrink-0">
                        @if($product->thumbnail)
                            <img src="{{ asset($product->thumbnail->file_name) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-stone-300">
                                <i class="fas fa-image text-3xl"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ url("/products/{$product->slug}") }}" class="hover:text-brand-600 transition-colors">
                        <h2 class="font-semibold text-lg text-stone-900 leading-snug">{{ $product->name }}</h2>
                    </a>
                    <div class="flex items-center gap-3 mt-1.5">
                        <div class="flex text-warm-400 text-sm">
                            @for($s = 0; $s < 5; $s++)
                                <i class="fas {{ $s < floor($product->rating) ? 'fa-star' : ($s < $product->rating ? 'fa-star-half-alt' : 'fa-star') }} {{ $s < $product->rating ? '' : 'text-stone-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-xs text-stone-500">{{ number_format($product->rating, 1) }}</span>
                        <span class="text-xs text-stone-400">|</span>
                        <span class="text-xs text-stone-500">{{ number_format($product->num_of_sale ?? 0) }} {{ __('common.sold') }}</span>
                    </div>
                    <p class="text-sm text-stone-500 mt-2 line-clamp-2">{{ Str::limit(strip_tags($product->description ?? ''), 150) }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-2xl font-extrabold text-brand-700">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</p>
                    @if($product->discount && $product->discount > 0)
                        <p class="text-xs text-red-500 mt-0.5">
                            <span class="bg-red-50 text-red-600 font-semibold px-2 py-0.5 rounded-full">
                                -{{ $product->discount_type === 'percent' ? $product->discount.'%' : 'Rp '.number_format($product->discount,0,',','.') }}
                            </span>
                        </p>
                    @endif
                    <a href="{{ url("/products/{$product->slug}") }}"
                       class="inline-block mt-3 px-5 py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold rounded-xl
                              hover:shadow-lg hover:shadow-brand-500/25 transition-all hover:-translate-y-0.5">
                        {{ __('pseo.view_detail') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<section class="max-w-6xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-stone-200 p-8 lg:p-10">
        <h2 class="font-display text-2xl font-bold text-stone-900 mb-4">{{ __('pseo.best_why', ['cat' => $cat->name]) }}</h2>
        <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed">
            <p>
                {{ __('pseo.best_why_p1', ['cat' => $cat->name]) }}
            </p>
            <p class="mt-3">
                {{ __('pseo.best_why_p2') }}
            </p>
            <ul class="list-disc pl-5 mt-3 space-y-1.5">
                <li><strong>{{ __('pseo.g1_t') }}</strong> — {{ __('pseo.g1_d') }}</li>
                <li><strong>{{ __('pseo.g2_t') }}</strong> — {{ __('pseo.g2_d') }}</li>
                <li><strong>{{ __('pseo.g3_t') }}</strong> — {{ __('pseo.g3_d') }}</li>
                <li><strong>{{ __('pseo.g4_t') }}</strong> — {{ __('pseo.g4_d') }}</li>
                <li><strong>{{ __('pseo.g5_t') }}</strong> — {{ __('pseo.g5_d') }}</li>
                <li><strong>{{ __('pseo.g6_t') }}</strong> — {{ __('pseo.g6_d') }}</li>
            </ul>
            <p class="mt-4">
                {{ __('pseo.best_outro', ['cat' => $cat->name]) }}
            </p>
        </div>
    </div>
</section>

<section class="bg-gradient-to-r from-brand-700 to-accent-700 text-white py-12">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p class="text-brand-200 text-sm font-semibold tracking-wider uppercase mb-2">{{ __('pseo.sc_badge') }}</p>
        <h2 class="font-display text-3xl font-extrabold mb-3">{{ __('pseo.sc_title') }}</h2>
        <p class="text-brand-100 text-lg mb-6 max-w-2xl mx-auto">
            {{ __('pseo.sc_sub_b') }}
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="{{ route('seo.buy-source-code') }}" class="px-8 py-3.5 bg-white text-brand-700 font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5">
                <i class="fas fa-shopping-cart mr-2"></i>{{ __('pseo.buy_source') }}
            </a>
            <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20aplikasi%20TokoOnline"
               target="_blank" rel="noopener"
               class="px-8 py-3.5 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 hover:shadow-xl transition-all hover:-translate-y-0.5">
                <i class="fab fa-whatsapp mr-2"></i>{{ __('pseo.chat_wa') }}
            </a>
        </div>
    </div>
</section>
@endsection
