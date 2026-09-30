@extends('layouts.storefront')

@section('title', __('coupons.title') . ' — TokoOnline')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-stone-600 font-medium">{{ __('coupons.title') }}</span>
    </nav>

    <h1 class="font-display text-3xl font-bold text-stone-900 mb-2"><i class="fas fa-ticket-alt text-brand-500 mr-2"></i>{{ __('coupons.title') }}</h1>
    <p class="text-stone-500 mb-8">{{ __('coupons.subtitle') }}</p>

    @if($coupons->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($coupons as $coupon)
        <div class="bg-white rounded-2xl border border-stone-100 card-lift overflow-hidden">
            <div class="bg-gradient-to-r from-brand-600 to-accent-600 p-5 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-4xl font-extrabold font-mono tracking-tight">
                        @if($coupon->discount_type === 'percent')
                        {{ round($coupon->discount) }}%
                        @else
                        Rp{{ number_format($coupon->discount, 0, ',', '.') }}
                        @endif
                    </span>
                    <span class="text-xs font-bold uppercase bg-white/20 px-3 py-1 rounded-full">OFF</span>
                </div>
                <h3 class="font-semibold">{{ __('coupons.code_label') }} <span class="font-mono font-bold bg-white/20 px-3 py-1 rounded text-lg tracking-wider">{{ $coupon->code }}</span></h3>
            </div>
            <div class="p-5">
                @if($coupon->min_buy > 0)
                <p class="text-sm text-stone-600 mb-2"><i class="fas fa-shopping-cart text-brand-400 mr-1"></i>{{ __('coupons.min_spend') }} <strong>Rp{{ number_format($coupon->min_buy, 0, ',', '.') }}</strong></p>
                @endif
                @if($coupon->max_discount > 0)
                <p class="text-sm text-stone-600 mb-2"><i class="fas fa-tag text-brand-400 mr-1"></i>{{ __('coupons.max_discount') }} <strong>Rp{{ number_format($coupon->max_discount, 0, ',', '.') }}</strong></p>
                @endif
                <p class="text-xs text-stone-400 mb-4"><i class="far fa-clock mr-1"></i> {{ __('coupons.valid_until') }} {{ date('d M Y', $coupon->end_date) }}</p>
                <button onclick="navigator.clipboard.writeText('{{ $coupon->code }}');this.innerHTML='<i class=\'fas fa-check mr-1\'></i>Tersalin!';"
                        class="w-full py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-brand-500/25 transition-all">
                    <i class="fas fa-copy mr-1"></i> {{ __('coupons.copy_code') }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($coupons->hasPages())
    <div class="flex items-center justify-center gap-1 mt-8">
        {{ $coupons->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-20 bg-white rounded-2xl border border-stone-100">
        <i class="fas fa-ticket-alt text-6xl text-stone-200 mb-4"></i>
        <h3 class="text-lg font-semibold text-stone-500">{{ __('coupons.empty_title') }}</h3>
        <p class="text-sm text-stone-400 mt-1">{{ __('coupons.empty_hint') }}</p>
    </div>
    @endif
</div>
@endsection
