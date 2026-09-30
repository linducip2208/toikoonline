@extends('layouts.storefront')

@section('title', __('compare.title') . ' — TokoOnline')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-stone-600 font-medium">{{ __('compare.title') }}</span>
    </nav>

    <h1 class="font-display text-3xl font-bold text-stone-900 mb-2"><i class="fas fa-balance-scale text-brand-500 mr-2"></i>{{ __('compare.title') }}</h1>
    <p class="text-stone-500 mb-8">{{ __('compare.subtitle') }}</p>

    @auth
        @if($compares->count())
        <div class="bg-white rounded-2xl border border-stone-100 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-100">
                        <th class="p-4 text-left text-stone-400 font-medium text-xs uppercase tracking-wider w-40">{{ __('compare.specs') }}</th>
                        @foreach($compares as $compare)
                        <th class="p-4 text-center min-w-[200px]">
                            <button onclick="event.preventDefault(); document.getElementById('remove-{{ $compare->id }}').submit()" class="text-stone-400 hover:text-red-500 float-right">
                                <i class="fas fa-times"></i>
                            </button>
                            <form id="remove-{{ $compare->id }}" action="{{ route('compare.remove') }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $compare->product_id }}">
                            </form>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">{{ __('compare.image') }}</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center">
                            <a href="{{ route('products.show', $compare->product->slug) }}">
                                <div class="bg-gradient-to-br from-brand-50 to-accent-50 h-32 rounded-xl flex items-center justify-center overflow-hidden">
                                    @if($compare->product->thumbnail_img)
                                        <img src="{{ asset($compare->product->thumbnail_img) }}" alt="{{ $compare->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-box text-3xl text-brand-200"></i>
                                    @endif
                                </div>
                            </a>
                        </td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">{{ __('compare.name') }}</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center font-semibold text-stone-800">
                            <a href="{{ route('products.show', $compare->product->slug) }}" class="hover:text-brand-600">{{ $compare->product->name }}</a>
                        </td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">{{ __('compare.price') }}</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center font-bold text-brand-600">Rp {{ number_format($compare->product->unit_price, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">Brand</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center text-stone-600">{{ $compare->product->brand?->name ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">{{ __('compare.category') }}</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center text-stone-600">{{ $compare->product->category?->name ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr class="border-b border-stone-50">
                        <td class="p-4 text-stone-400 text-xs">Rating</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= round($compare->product->rating ?: 0) ? 'star-gold' : 'text-stone-300' }}"></i>
                            @endfor
                            <span class="text-xs text-stone-400 ml-1">({{ $compare->product->num_of_sale ?? 0 }})</span>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="p-4 text-stone-400 text-xs">{{ __('compare.add_to_cart') }}</td>
                        @foreach($compares as $compare)
                        <td class="p-4 text-center">
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $compare->product->id }}">
                                <input type="hidden" name="price" value="{{ $compare->product->unit_price }}">
                                <input type="hidden" name="quantity" value="1">
                                <button class="px-4 py-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-semibold rounded-lg hover:shadow-md transition-all">
                                    <i class="fas fa-cart-plus mr-1"></i> {{ __('compare.cart_short') }}
                                </button>
                            </form>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
            <i class="fas fa-balance-scale text-6xl text-stone-200 mb-4"></i>
            <h3 class="text-lg font-semibold text-stone-500">{{ __('compare.empty_title') }}</h3>
            <p class="text-sm text-stone-400 mt-1 mb-6">{{ __('compare.empty_hint') }}</p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors">{{ __('compare.view_products') }}</a>
        </div>
        @endif
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
        <i class="fas fa-lock text-6xl text-stone-200 mb-4"></i>
        <h3 class="text-lg font-semibold text-stone-500">{{ __('compare.login_title') }}</h3>
        <p class="text-sm text-stone-400 mt-1 mb-6">{{ __('compare.login_hint') }}</p>
        <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors">{{ __('compare.login_btn') }}</a>
    </div>
    @endauth
</div>
@endsection
