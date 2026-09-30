@extends('layouts.storefront')

@section('title', 'Semua Brand — TokoOnline')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-stone-600 font-medium">Brand</span>
    </nav>

    <h1 class="font-display text-3xl font-bold text-stone-900 mb-2"><i class="fas fa-tags text-brand-500 mr-2"></i>Semua Brand</h1>
    <p class="text-stone-500 mb-8">Jelajahi semua brand yang tersedia di toko kami.</p>

    @if($brands->count())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($brands as $brand)
        <a href="{{ route('brands.show', $brand->slug) }}"
           class="bg-white rounded-2xl p-5 border border-stone-100 card-lift hover:border-brand-200 group text-center">
            @if($brand->logo)
            <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="w-14 h-14 object-contain rounded-full mx-auto mb-3">
            @else
            <div class="w-14 h-14 rounded-full bg-brand-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-100 transition-colors">
                <i class="fas fa-tag text-brand-500 text-xl"></i>
            </div>
            @endif
            <h3 class="font-semibold text-sm text-stone-800 mb-1">{{ $brand->name }}</h3>
            <p class="text-[11px] text-stone-400">{{ $brand->products_count }} produk</p>
        </a>
        @endforeach
    </div>

    @if($brands->hasPages())
    <div class="flex items-center justify-center gap-1 mt-8">
        {{ $brands->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
        <i class="fas fa-tags text-6xl text-stone-200 mb-4"></i>
        <h3 class="text-lg font-semibold text-stone-500">Tidak ada brand</h3>
    </div>
    @endif
</div>
@endsection
