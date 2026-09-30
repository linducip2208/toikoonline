@extends('layouts.storefront')

@section('title', 'Semua Kategori — TokoOnline')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-stone-600 font-medium">Kategori</span>
    </nav>

    <h1 class="font-display text-3xl font-bold text-stone-900 mb-2"><i class="fas fa-th-large text-brand-500 mr-2"></i>Semua Kategori</h1>
    <p class="text-stone-500 mb-8">Jelajahi semua kategori produk yang tersedia.</p>

    @if($categories->count())
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($categories as $cat)
        <a href="{{ route('categories.show', $cat->slug) }}"
           class="bg-white rounded-2xl p-6 border border-stone-100 card-lift hover:border-brand-200 group">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mb-4 group-hover:bg-brand-100 transition-colors">
                @if($cat->icon)
                <i class="fas fa-{{ $cat->icon }} text-brand-500 text-xl"></i>
                @else
                <i class="fas fa-folder text-brand-500 text-xl"></i>
                @endif
            </div>
            <h3 class="font-semibold text-base text-stone-800 mb-1">{{ $cat->name }}</h3>
            <p class="text-sm text-stone-400">{{ $cat->products_count }} produk</p>
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
        <i class="fas fa-th-large text-6xl text-stone-200 mb-4"></i>
        <h3 class="text-lg font-semibold text-stone-500">Tidak ada kategori</h3>
    </div>
    @endif
</div>
@endsection
