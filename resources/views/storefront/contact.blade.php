@extends('layouts.storefront')

@section('title', __('contact.title') . ' — TokoOnline')
@section('meta_description', 'Hubungi tim TokoOnline untuk pertanyaan produk, pesanan, atau kerja sama. Kami merespons maksimal 1x24 jam.')

@section('content')
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-stone-800 font-medium" aria-current="page">{{ __('contact.title') }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid lg:grid-cols-2 gap-8">
        <div>
            <h1 class="text-3xl font-extrabold text-stone-900 mb-3">{{ __('contact.title') }}</h1>
            <p class="text-stone-500 mb-8">{{ __('contact.subtitle') }}</p>

            <dl class="space-y-4 text-sm">
                <div class="flex gap-3">
                    <dt class="font-bold text-stone-800 w-24 shrink-0">Email</dt>
                    <dd class="text-stone-600">support@tokoonline.test</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="font-bold text-stone-800 w-24 shrink-0">WhatsApp</dt>
                    <dd class="text-stone-600">+62 812-3456-7890 (Senin–Sabtu, 09.00–17.00 WIB)</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="font-bold text-stone-800 w-24 shrink-0">{{ __('contact.address_label') }}</dt>
                    <dd class="text-stone-600">Jl. Merdeka No. 1, Jakarta Pusat</dd>
                </div>
            </dl>

            <p class="mt-8 text-sm text-stone-500">
                {{ __('contact.quick_hint') }} <a href="{{ route('faq.index') }}" class="text-brand-600 font-semibold hover:text-brand-700">{{ __('contact.faq_link') }}</a>.
            </p>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-6 sm:p-8 shadow-sm">
            @if(session('success'))
                <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-sm font-semibold text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('contact.name') }}</label>
                        <input type="text" id="nama" name="name" value="{{ old('name') }}" required autocomplete="name"
                            class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="subjek" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('contact.subject') }} <span class="font-normal text-stone-400">{{ __('contact.optional') }}</span></label>
                    <input type="text" id="subjek" name="subject" value="{{ old('subject') }}"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('subject') border-red-400 @enderror">
                    @error('subject')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="pesan" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('contact.message') }}</label>
                    <textarea id="pesan" name="message" rows="5" required minlength="10"
                        class="w-full px-4 py-3 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('message') border-red-400 @enderror">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                    {{ __('contact.send') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
