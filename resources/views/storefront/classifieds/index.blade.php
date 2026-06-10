@extends('layouts.storefront')

@section('title', 'Iklan Baris')

@section('content')
<div class="bg-gradient-to-br from-brand-50 to-white">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl font-bold text-stone-900 mb-3">
                <i class="fas fa-newspaper text-brand-600 mr-2"></i>Iklan Baris
            </h1>
            <p class="text-stone-500 text-lg max-w-2xl mx-auto">
                Temukan berbagai produk dan jasa dari penjual terpercaya di seluruh Indonesia.
            </p>
        </div>

        @if($classifieds->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($classifieds as $item)
                    <a href="{{ route('classifieds.show', $item->slug ?? $item->id) }}" class="block bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden card-lift group">
                        <div class="relative h-48 bg-stone-100 overflow-hidden">
                            @if($item->thumbnail_img)
                                <img src="{{ asset($item->thumbnail_img) }}" alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <i class="fas fa-image text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-brand-700 text-sm font-bold px-3 py-1 rounded-full">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </div>
                            @if($item->condition)
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur text-stone-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $item->condition }}
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-semibold text-stone-900 mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                                {{ $item->name }}
                            </h3>

                            <div class="flex items-center gap-3 text-sm text-stone-500 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user text-xs"></i>
                                    {{ $item->user->name }}
                                </span>
                                @if($item->location)
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-xs"></i>
                                        {{ $item->location }}
                                    </span>
                                @endif
                            </div>

                            <span class="inline-block text-xs bg-brand-50 text-brand-600 px-2.5 py-1 rounded-full font-medium">
                                {{ $item->category->name }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $classifieds->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-newspaper text-6xl text-stone-300 mb-4 block"></i>
                <p class="text-stone-500 text-lg">Belum ada iklan baris.</p>
                <p class="text-stone-400 text-sm mt-1">Silakan kembali lagi nanti.</p>
            </div>
        @endif
    </div>
</div>
@endsection
