@extends('pseo._layout')

@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => "10 Alternatif {$product->name}",
    'description' => $meta['description'],
    'numberOfItems' => count($alternatives),
    'itemListElement' => $alternatives->map(function($p, $i) {
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
    <div class="max-w-6xl mx-auto px-4">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">Alternatif Produk</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            10 Alternatif {{ $product->name }}
        </h1>
        <p class="text-brand-200 text-lg mt-4 max-w-2xl leading-relaxed">
            Sedang mencari alternatif {{ $product->name }}? Berikut 10 produk serupa dengan kualitas dan harga terbaik yang bisa Anda pertimbangkan.
        </p>
    </div>
</div>

@if($alternatives->isEmpty())
<div class="max-w-6xl mx-auto px-4 py-20 text-center text-stone-400">
    <i class="fas fa-search text-6xl mb-4 block"></i>
    <p class="text-lg font-medium">Maaf, belum ada produk alternatif yang tersedia.</p>
    <a href="{{ url('/products') }}" class="text-brand-600 hover:underline mt-2 inline-block">Jelajahi semua produk &rarr;</a>
</div>
@else
<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($alternatives as $alt)
        <a href="{{ url("/products/{$alt->slug}") }}" class="group bg-white rounded-2xl border border-stone-200 p-5 card-lift hover:border-brand-200 block">
            <div class="aspect-square rounded-xl bg-stone-100 overflow-hidden mb-4">
                @if($alt->thumbnail)
                    <img src="{{ asset($alt->thumbnail->file_name) }}" alt="{{ $alt->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                @else
                    <div class="w-full h-full flex items-center justify-center text-stone-300">
                        <i class="fas fa-image text-4xl"></i>
                    </div>
                @endif
            </div>
            <h3 class="font-semibold text-stone-900 group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug">{{ $alt->name }}</h3>
            <div class="flex items-center gap-2 mt-1.5">
                <div class="flex text-warm-400 text-xs">
                    @for($s = 0; $s < 5; $s++)
                        <i class="fas {{ $s < floor($alt->rating) ? 'fa-star' : ($s < $alt->rating ? 'fa-star-half-alt' : 'fa-star') }} {{ $s < $alt->rating ? '' : 'text-stone-300' }}"></i>
                    @endfor
                </div>
                <span class="text-xs text-stone-500">{{ number_format($alt->rating, 1) }}</span>
            </div>
            <p class="text-lg font-extrabold text-brand-700 mt-2">Rp {{ number_format($alt->unit_price, 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400 mt-1">{{ number_format($alt->num_of_sale ?? 0) }} terjual</p>
        </a>
        @endforeach
    </div>
</div>
@endif

<section class="max-w-6xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-stone-200 p-8 lg:p-10">
        <h2 class="font-display text-2xl font-bold text-stone-900 mb-4">Kenapa Perlu Alternatif {{ $product->name }}?</h2>
        <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed">
            <p>
                {{ $product->name }} adalah produk yang populer, namun bisa jadi tidak selalu cocok untuk semua orang. Harga mungkin di luar budget, stok terbatas, atau fiturnya kurang sesuai kebutuhan Anda.
            </p>
            <p class="mt-3">Berikut alasan kenapa Anda sebaiknya mempertimbangkan alternatif:</p>
            <ul class="list-disc pl-5 mt-3 space-y-1.5">
                <li><strong>Harga Lebih Terjangkau</strong> — Alternatif seringkali menawarkan fitur serupa dengan harga lebih rendah.</li>
                <li><strong>Fitur Lebih Lengkap</strong> — Beberapa produk alternatif justru hadir dengan fitur tambahan.</li>
                <li><strong>Stok Tersedia</strong> — Jika produk utama habis, alternatif bisa jadi solusi cepat.</li>
                <li><strong>Review Berbeda</strong> — Setiap produk punya kelebihan dan kekurangan; bandingkan review untuk keputusan terbaik.</li>
                <li><strong>Brand Lain</strong> — Eksplorasi brand lain yang mungkin lebih cocok dengan preferensi Anda.</li>
            </ul>
            <p class="mt-4">
                Semua alternatif di atas tersedia di TokoOnline dengan jaminan original, harga kompetitif, pengiriman cepat, dan layanan customer support 24/7.
            </p>
        </div>
    </div>
</section>

<section class="bg-gradient-to-r from-brand-700 to-accent-700 text-white py-12">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p class="text-brand-200 text-sm font-semibold tracking-wider uppercase mb-2">Source Code Application</p>
        <h2 class="font-display text-3xl font-extrabold mb-3">Punya Aplikasi Toko Online Sendiri</h2>
        <p class="text-brand-100 text-lg mb-6 max-w-2xl mx-auto">
            Source code aplikasi toko online siap pakai. Whitelabel — bisa ganti nama, logo, warna sesuka hati.
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="{{ route('seo.buy-source-code') }}" class="px-8 py-3.5 bg-white text-brand-700 font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5">
                <i class="fas fa-shopping-cart mr-2"></i>Beli Source Code
            </a>
            <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20aplikasi%20TokoOnline"
               target="_blank" rel="noopener"
               class="px-8 py-3.5 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 hover:shadow-xl transition-all hover:-translate-y-0.5">
                <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp
            </a>
        </div>
    </div>
</section>
@endsection
