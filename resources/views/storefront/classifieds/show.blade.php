@extends('layouts.storefront')

@section('title', $classified->name . ' - Iklan Baris')

@section('content')
<div class="bg-gradient-to-br from-brand-50 to-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-stone-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('classifieds.index') }}" class="hover:text-brand-600">Iklan Baris</a>
            <span class="mx-2">/</span>
            <span class="text-stone-800">{{ $classified->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Images --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                    @if($classified->thumbnail_img)
                        <img src="{{ asset($classified->thumbnail_img) }}" alt="{{ $classified->name }}"
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-stone-100 flex items-center justify-center text-stone-300">
                            <i class="fas fa-image text-7xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
                    <h2 class="font-semibold text-lg text-stone-900 mb-3">Deskripsi</h2>
                    <div class="text-stone-600 text-sm leading-relaxed prose prose-sm max-w-none">
                        {!! $classified->description ?? '<p class="text-stone-400 italic">Tidak ada deskripsi.</p>' !!}
                    </div>
                </div>

                {{-- Photos Gallery --}}
                @if($classified->photos)
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
                        <h2 class="font-semibold text-lg text-stone-900 mb-4">Galeri Foto</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach(json_decode($classified->photos) as $photo)
                                <img src="{{ asset($photo) }}" alt="{{ $classified->name }}"
                                     class="w-full h-40 object-cover rounded-xl border border-stone-100">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Seller Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 sticky top-24">
                    <h1 class="font-semibold text-xl text-stone-900 mb-2">{{ $classified->name }}</h1>

                    <div class="flex items-center gap-2 text-sm text-brand-600 mb-4">
                        <span class="px-2.5 py-1 bg-brand-50 rounded-full font-medium">
                            {{ $classified->category->name }}
                        </span>
                        @if($classified->condition)
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full font-medium">
                                {{ $classified->condition }}
                            </span>
                        @endif
                    </div>

                    <div class="text-3xl font-bold text-brand-600 mb-6">
                        Rp {{ number_format($classified->unit_price, 0, ',', '.') }}
                    </div>

                    {{-- Seller Info --}}
                    <div class="border-t border-stone-100 pt-4 space-y-2 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center">
                                <i class="fas fa-user text-brand-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-stone-800 text-sm">{{ $classified->user->name }}</p>
                                <p class="text-xs text-stone-500">Penjual</p>
                            </div>
                        </div>
                        @if($classified->location)
                            <div class="flex items-center gap-2 text-sm text-stone-500">
                                <i class="fas fa-map-marker-alt text-brand-400 w-4"></i>
                                <span>{{ $classified->location }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Contact Button --}}
                    <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20iklan%20{{ urlencode($classified->name) }}"
                       target="_blank" rel="noopener"
                       class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 mb-3">
                        <i class="fab fa-whatsapp text-lg"></i>Hubungi Penjual
                    </a>

                    <button class="w-full py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-flag"></i>Laporkan Iklan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
