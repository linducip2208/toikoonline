@extends('customer.layout')
@section('title', __('customer.wishlist_title'))
@section('page-title', __('customer.wishlist_title'))

@section('content')
<div>
    <h1 class="text-2xl font-extrabold text-stone-900 mb-6">{{ __('customer.wishlist_title') }}</h1>

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" role="list" aria-label="Daftar wishlist">
            @foreach($wishlists as $item)
                @php $product = $item->product; @endphp
                @if($product)
                    <div role="listitem" class="bg-white border border-stone-200 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                        <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square bg-stone-100 overflow-hidden" aria-label="Lihat {{ $product->name }}">
                            @if($product->thumbnail_img)
                                <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <span class="w-full h-full flex items-center justify-center text-3xl font-extrabold text-stone-300" aria-hidden="true">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                            @endif
                        </a>
                        <div class="p-4">
                            <a href="{{ route('products.show', $product->slug) }}" class="block font-semibold text-sm text-stone-900 hover:text-brand-600 transition line-clamp-2 min-h-[2.5rem]">
                                {{ $product->name }}
                            </a>
                            <p class="text-brand-600 font-bold mt-1.5">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
    @else
        <div class="text-center py-16" role="status">
            <div class="text-6xl mb-4" aria-hidden="true">🤍</div>
            <h2 class="text-xl font-bold text-stone-800 mb-2">{{ __('customer.wishlist_empty') }}</h2>
            <p class="text-stone-500 mb-6">{{ __('customer.wishlist_empty_hint') }}</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center min-h-[44px] gap-2 px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                {{ __('customer.browse_products') }}
            </a>
        </div>
    @endif
</div>
@endsection
