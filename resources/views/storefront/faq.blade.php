@extends('layouts.storefront')

@section('title', __('faq.meta_title') . ' — TokoOnline')
@section('meta_description', 'Jawaban atas pertanyaan umum seputar pemesanan, pembayaran, pengiriman, dan pengembalian barang di TokoOnline.')

@section('content')
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-stone-800 font-medium" aria-current="page">FAQ</span>
        </nav>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 py-10" x-data="{ open: null }">
    <h1 class="text-3xl font-extrabold text-stone-900 mb-3">{{ __('faq.title') }}</h1>
    <p class="text-stone-500 mb-8">{{ __('faq.subtitle') }}</p>

    @if($faqPages->count() > 0)
        @foreach($faqPages as $i => $page)
            <div class="bg-white border border-stone-200 rounded-2xl mb-4 overflow-hidden">
                <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}" :aria-expanded="(open === {{ $i }}).toString()" aria-controls="faq-panel-{{ $i }}"
                    class="w-full flex items-center justify-between gap-4 px-5 py-4 min-h-[44px] text-left font-bold text-stone-900 hover:bg-stone-50 transition">
                    {{ $page->title }}
                    <svg class="w-5 h-5 shrink-0 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="faq-panel-{{ $i }}" x-show="open === {{ $i }}" x-cloak class="px-5 pb-5 text-sm text-stone-600 leading-relaxed">
                    {!! $page->content !!}
                </div>
            </div>
        @endforeach
    @endif

    @if($blockFaqs->count() > 0)
        @foreach($blockFaqs as $j => $item)
            @php $k = 1000 + $j; @endphp
            <div class="bg-white border border-stone-200 rounded-2xl mb-4 overflow-hidden">
                <button type="button" @click="open === {{ $k }} ? open = null : open = {{ $k }}" :aria-expanded="(open === {{ $k }}).toString()" aria-controls="faq-panel-{{ $k }}"
                    class="w-full flex items-center justify-between gap-4 px-5 py-4 min-h-[44px] text-left font-bold text-stone-900 hover:bg-stone-50 transition">
                    {{ $item['question'] }}
                    <svg class="w-5 h-5 shrink-0 transition-transform" :class="open === {{ $k }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="faq-panel-{{ $k }}" x-show="open === {{ $k }}" x-cloak class="px-5 pb-5 text-sm text-stone-600 leading-relaxed">
                    {!! $item['answer'] !!}
                    <p class="mt-2 text-xs text-stone-400">{{ __('faq.source') }}: {{ $item['source'] }}</p>
                </div>
            </div>
        @endforeach
    @endif

    @if($faqPages->count() === 0 && $blockFaqs->count() === 0)
        <div class="text-center py-16 bg-white border border-stone-200 rounded-2xl" role="status">
            <div class="text-6xl mb-4" aria-hidden="true">❓</div>
            <h2 class="text-xl font-bold text-stone-800 mb-2">{{ __('faq.empty_title') }}</h2>
            <p class="text-stone-500 mb-6">{{ __('faq.empty_hint') }}</p>
            <a href="{{ route('contact.show') }}" class="inline-flex items-center justify-center min-h-[44px] px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                {{ __('faq.contact_us') }}
            </a>
        </div>
    @endif
</div>
@endsection
