@extends('pseo._layout')

@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $meta['title'],
    'description' => $meta['description'],
    'mainEntity' => [
        '@type' => 'Product',
        'name' => $productA->name,
        'offers' => ['@type' => 'Offer', 'price' => $productA->unit_price, 'priceCurrency' => 'IDR'],
        'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $productA->rating, 'reviewCount' => $productA->reviews()->count()],
    ],
];

$compareFields = [
    'Harga' => ['A' => 'Rp '.number_format($productA->unit_price, 0, ',', '.'), 'B' => 'Rp '.number_format($productB->unit_price, 0, ',', '.')],
    'Rating' => ['A' => number_format($productA->rating, 1).' / 5', 'B' => number_format($productB->rating, 1).' / 5'],
    'Terjual' => ['A' => number_format($productA->num_of_sale ?? 0), 'B' => number_format($productB->num_of_sale ?? 0)],
    'Brand' => ['A' => $productA->brand?->name ?? '-', 'B' => $productB->brand?->name ?? '-'],
    'Kategori' => ['A' => $productA->category?->name ?? '-', 'B' => $productB->category?->name ?? '-'],
    'Stok' => ['A' => $productA->stock_visibility ? 'Tersedia' : 'Cek', 'B' => $productB->stock_visibility ? 'Tersedia' : 'Cek'],
    'Min. Order' => ['A' => $productA->min_qty ?? 1, 'B' => $productB->min_qty ?? 1],
    'Berat' => ['A' => ($productA->weight ?? '-').' g', 'B' => ($productB->weight ?? '-').' g'],
    'Cash on Delivery' => ['A' => $productA->cash_on_delivery ? 'Ya' : 'Tidak', 'B' => $productB->cash_on_delivery ? 'Ya' : 'Tidak'],
];

$winner_price = $productA->unit_price <= $productB->unit_price ? 'A' : 'B';
$winner_rating = $productA->rating >= $productB->rating ? 'A' : 'B';
$winner_sales = ($productA->num_of_sale ?? 0) >= ($productB->num_of_sale ?? 0) ? 'A' : 'B';
@endphp

@section('content')
<div class="bg-gradient-to-br from-brand-900 via-brand-800 to-accent-900 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">Perbandingan Produk</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            {{ $productA->name }} vs {{ $productB->name }}
        </h1>
        <p class="text-brand-200 text-lg mt-4 max-w-2xl mx-auto leading-relaxed">
            Bandingkan spesifikasi, harga, rating, dan review. Temukan mana yang paling cocok untuk kebutuhan Anda.
        </p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white rounded-2xl border border-stone-200 p-6 text-center">
            @if($productA->thumbnail)
                <img src="{{ asset($productA->thumbnail->file_name) }}" alt="{{ $productA->name }}" class="w-40 h-40 object-cover rounded-xl mx-auto" loading="lazy">
            @else
                <div class="w-40 h-40 bg-stone-100 rounded-xl mx-auto flex items-center justify-center text-stone-300">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif
            <h2 class="font-semibold text-lg mt-4">{{ $productA->name }}</h2>
            <p class="text-2xl font-extrabold text-brand-700 mt-2">Rp {{ number_format($productA->unit_price, 0, ',', '.') }}</p>
            <div class="flex justify-center text-warm-400 text-sm mt-1">
                @for($s = 0; $s < 5; $s++)
                    <i class="fas {{ $s < floor($productA->rating) ? 'fa-star' : ($s < $productA->rating ? 'fa-star-half-alt' : 'fa-star') }} {{ $s < $productA->rating ? '' : 'text-stone-300' }}"></i>
                @endfor
                <span class="text-stone-600 ml-1">{{ number_format($productA->rating, 1) }}</span>
            </div>
            <a href="{{ url("/products/{$productA->slug}") }}"
               class="inline-block mt-4 w-full py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold rounded-xl
                      hover:shadow-lg hover:shadow-brand-500/25 transition-all hover:-translate-y-0.5">
                Lihat Detail
            </a>
        </div>

        <div class="flex items-center justify-center">
            <div class="text-center">
                <span class="w-16 h-16 rounded-full bg-brand-50 border-2 border-brand-200 flex items-center justify-center mx-auto">
                    <span class="font-display text-2xl font-extrabold text-brand-600">VS</span>
                </span>
                <p class="text-xs text-stone-400 mt-2">Perbandingan</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-6 text-center">
            @if($productB->thumbnail)
                <img src="{{ asset($productB->thumbnail->file_name) }}" alt="{{ $productB->name }}" class="w-40 h-40 object-cover rounded-xl mx-auto" loading="lazy">
            @else
                <div class="w-40 h-40 bg-stone-100 rounded-xl mx-auto flex items-center justify-center text-stone-300">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif
            <h2 class="font-semibold text-lg mt-4">{{ $productB->name }}</h2>
            <p class="text-2xl font-extrabold text-brand-700 mt-2">Rp {{ number_format($productB->unit_price, 0, ',', '.') }}</p>
            <div class="flex justify-center text-warm-400 text-sm mt-1">
                @for($s = 0; $s < 5; $s++)
                    <i class="fas {{ $s < floor($productB->rating) ? 'fa-star' : ($s < $productB->rating ? 'fa-star-half-alt' : 'fa-star') }} {{ $s < $productB->rating ? '' : 'text-stone-300' }}"></i>
                @endfor
                <span class="text-stone-600 ml-1">{{ number_format($productB->rating, 1) }}</span>
            </div>
            <a href="{{ url("/products/{$productB->slug}") }}"
               class="inline-block mt-4 w-full py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold rounded-xl
                      hover:shadow-lg hover:shadow-brand-500/25 transition-all hover:-translate-y-0.5">
                Lihat Detail
            </a>
        </div>
    </div>

    <div class="mt-10 bg-white rounded-2xl border border-stone-200 overflow-hidden">
        <h2 class="font-display text-xl font-bold text-stone-900 p-6 border-b border-stone-100">Tabel Perbandingan</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50">
                        <th class="text-left px-6 py-3 font-semibold text-stone-600 w-40">Spesifikasi</th>
                        <th class="text-center px-6 py-3 font-semibold text-brand-700">
                            {{ Str::limit($productA->name, 30) }}
                        </th>
                        <th class="text-center px-6 py-3 font-semibold text-accent-700">
                            {{ Str::limit($productB->name, 30) }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($compareFields as $label => $values)
                    <tr class="hover:bg-stone-50/50">
                        <td class="px-6 py-3.5 font-medium text-stone-700">{{ $label }}</td>
                        <td class="text-center px-6 py-3.5 text-stone-600">{{ $values['A'] }}</td>
                        <td class="text-center px-6 py-3.5 text-stone-600">{{ $values['B'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
            <i class="fas fa-trophy text-green-600 text-2xl mb-2"></i>
            <p class="font-semibold text-green-800">Harga Terbaik</p>
            <p class="text-sm text-green-600 mt-1">
                {{ $winner_price === 'A' ? $productA->name : $productB->name }}
            </p>
        </div>
        <div class="bg-warm-50 border border-warm-200 rounded-2xl p-6 text-center">
            <i class="fas fa-star text-warm-500 text-2xl mb-2"></i>
            <p class="font-semibold text-warm-800">Rating Tertinggi</p>
            <p class="text-sm text-warm-600 mt-1">
                {{ $winner_rating === 'A' ? $productA->name : $productB->name }}
            </p>
        </div>
        <div class="bg-brand-50 border border-brand-200 rounded-2xl p-6 text-center">
            <i class="fas fa-fire text-brand-500 text-2xl mb-2"></i>
            <p class="font-semibold text-brand-800">Paling Laris</p>
            <p class="text-sm text-brand-600 mt-1">
                {{ $winner_sales === 'A' ? $productA->name : $productB->name }}
            </p>
        </div>
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 pb-12">
    <div class="bg-white rounded-2xl border border-stone-200 p-8 lg:p-10">
        <h2 class="font-display text-2xl font-bold text-stone-900 mb-4">Kesimpulan: {{ $productA->name }} vs {{ $productB->name }}, Mana yang Lebih Worth It?</h2>
        <div class="prose prose-stone max-w-none text-stone-600 leading-relaxed">
            <p>
                Setelah membandingkan {{ $productA->name }} dan {{ $productB->name }} secara head-to-head, keputusan akhir tetap kembali pada kebutuhan dan budget Anda.
            </p>
            <h3 class="text-lg font-semibold text-stone-800 mt-4">Pilih {{ $productA->name }} jika:</h3>
            <ul class="list-disc pl-5 space-y-1">
                <li>Anda mencari produk dengan rating {{ number_format($productA->rating, 1) }} dari pelanggan</li>
                <li>Budget Anda sekitar Rp {{ number_format($productA->unit_price, 0, ',', '.') }}</li>
                <li>Anda menginginkan brand {{ $productA->brand?->name ?? 'terpercaya' }}</li>
            </ul>
            <h3 class="text-lg font-semibold text-stone-800 mt-4">Pilih {{ $productB->name }} jika:</h3>
            <ul class="list-disc pl-5 space-y-1">
                <li>Anda mencari produk dengan rating {{ number_format($productB->rating, 1) }} dari pelanggan</li>
                <li>Budget Anda sekitar Rp {{ number_format($productB->unit_price, 0, ',', '.') }}</li>
                <li>Anda menginginkan brand {{ $productB->brand?->name ?? 'terpercaya' }}</li>
            </ul>
            <p class="mt-4">Apapun pilihan Anda, TokoOnline menjamin produk original dengan garansi penuh dan pengiriman cepat ke seluruh Indonesia.</p>
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
