@extends('pseo._layout')

@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'Source Code Aplikasi Toko Online',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web',
    'description' => 'Source code aplikasi toko online / e-commerce siap pakai. Laravel + MySQL. Whitelabel ready.',
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR', 'availability' => 'https://schema.org/InStock'],
];
@endphp

@section('content')
<div class="bg-gradient-to-br from-brand-900 via-brand-800 to-accent-900 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">Source Code</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            {{ __('pseo.buy_title') }}
        </h1>
        <p class="text-brand-200 text-xl mt-4 max-w-3xl mx-auto leading-relaxed">
            {{ __('pseo.buy_sub') }}
        </p>
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="font-display text-3xl font-bold text-stone-900 mb-4">{{ __('pseo.buy_why') }}</h2>
            <p class="text-stone-600 leading-relaxed mb-6">
                {{ __('pseo.buy_why_p') }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-code text-brand-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Laravel + MySQL</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f1_d') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-paint-brush text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Whitelabel Ready</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f2_d') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-warm-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-mobile-alt text-warm-500"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Responsive 100%</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f3_d') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-alt text-red-500"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Full Source Code</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f4_d') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plug text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Payment Gateway</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f5_d') }}</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-search text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">SEO Built-in</h4>
                        <p class="text-xs text-stone-500 mt-0.5">{{ __('pseo.buy_f6_d') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-8 shadow-lg">
            <h3 class="font-display text-2xl font-bold text-stone-900 mb-6 text-center">{{ __('pseo.buy_features') }}</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf1') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf2') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf3') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf4') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf5') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf6') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf7') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf8') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf9') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf10') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf11') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf12') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf13') }}</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">{{ __('pseo.bf14') }}</span></div>
            </div>
        </div>
    </div>
</section>

<section class="bg-stone-100 py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="font-display text-3xl font-bold text-stone-900 text-center mb-2">{{ __('pseo.buy_who') }}</h2>
        <p class="text-stone-500 text-center mb-10">{{ __('pseo.buy_who_sub') }}</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-4">
                    <i class="fas fa-store text-brand-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">{{ __('pseo.buy_who1_t') }}</h3>
                <p class="text-sm text-stone-500 leading-relaxed">{{ __('pseo.buy_who1_d') }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                    <i class="fas fa-laptop-code text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">{{ __('pseo.buy_who2_t') }}</h3>
                <p class="text-sm text-stone-500 leading-relaxed">{{ __('pseo.buy_who2_d') }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-warm-50 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-warm-500 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">{{ __('pseo.buy_who3_t') }}</h3>
                <p class="text-sm text-stone-500 leading-relaxed">{{ __('pseo.buy_who3_d') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="bg-gradient-to-br from-brand-700 to-accent-700 text-white rounded-2xl p-10 text-center shadow-xl">
        <p class="text-brand-200 text-sm font-semibold tracking-wider uppercase mb-2">{{ __('pseo.buy_cta_badge') }}</p>
        <h2 class="font-display text-3xl font-extrabold mb-3">{{ __('pseo.buy_cta_title') }}</h2>
        <p class="text-brand-100 text-lg mb-6 max-w-2xl mx-auto leading-relaxed">
            {{ __('pseo.buy_cta_sub') }}
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20source%20code%20aplikasi%20TokoOnline%20%F0%9F%9B%92"
               target="_blank" rel="noopener"
               class="px-8 py-3.5 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 hover:shadow-xl transition-all hover:-translate-y-0.5 text-lg">
                <i class="fab fa-whatsapp mr-2"></i>{{ __('pseo.chat_wa_now') }}
            </a>
        </div>
        <p class="text-brand-200 text-xs mt-5">{{ __('pseo.fast_response') }}</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 pb-12">
    <h2 class="font-display text-2xl font-bold text-stone-900 mb-6">{{ __('pseo.faq_title') }}</h2>
    <div x-data="{ openIndex: null }" class="space-y-3">
        @php $faqs = [
            [__('pseo.faq1_q'), __('pseo.faq1_a')],
            [__('pseo.faq2_q'), __('pseo.faq2_a')],
            [__('pseo.faq3_q'), __('pseo.faq3_a')],
            [__('pseo.faq4_q'), __('pseo.faq4_a')],
            [__('pseo.faq5_q'), __('pseo.faq5_a')],
            [__('pseo.faq6_q'), __('pseo.faq6_a')],
        ] @endphp
        @foreach($faqs as $i => $faq)
        <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
            <button @click="openIndex = openIndex === {{ $i }} ? null : {{ $i }}"
                    class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-stone-50 transition-colors">
                <span class="font-semibold text-stone-800">{{ $faq[0] }}</span>
                <i class="fas text-stone-400 transition-transform duration-200"
                   :class="openIndex === {{ $i }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openIndex === {{ $i }}" x-cloak class="px-6 pb-4 text-stone-600 text-sm leading-relaxed">
                {{ $faq[1] }}
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection
