@extends('layouts.storefront')

@section('title', __('pwa.title_tag') . ' — TokoOnline')
@section('meta_description', 'Anda sedang offline. Periksa koneksi internet lalu coba lagi.')

@section('content')
<div class="max-w-xl mx-auto px-4 py-20 text-center">
    <div class="text-7xl mb-6" aria-hidden="true">📡</div>
    <h1 class="text-3xl font-extrabold text-stone-900 mb-3">{{ __('pwa.title') }}</h1>
    <p class="text-stone-500 mb-2">{{ __('pwa.desc') }}</p>
    <p class="text-sm text-stone-400 mb-8">{{ __('pwa.note') }}</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button type="button" onclick="window.location.reload()" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition">
            {{ __('pwa.retry') }}
        </button>
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 text-sm font-bold text-stone-600 bg-white border border-stone-300 rounded-xl hover:bg-stone-50 transition">
            {{ __('pwa.back_home') }}
        </a>
    </div>
</div>
@endsection
