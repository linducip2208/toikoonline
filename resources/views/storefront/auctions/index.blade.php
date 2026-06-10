@extends('layouts.storefront')

@section('title', 'Lelang')

@section('content')
<div class="bg-gradient-to-br from-brand-50 to-white">
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="font-display text-4xl font-bold text-stone-900 mb-3">
                <i class="fas fa-gavel text-brand-600 mr-2"></i>Lelang Produk
            </h1>
            <p class="text-stone-500 text-lg max-w-2xl mx-auto">
                Ikuti lelang produk-produk menarik dengan harga mulai dari yang terjangkau. Jangan lewatkan kesempatan menang!
            </p>
        </div>

        {{-- Active Auctions Grid --}}
        @if($auctions->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($auctions as $auction)
                    <a href="{{ route('auctions.show', $auction->id) }}" class="block bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden card-lift group">
                        {{-- Product Image --}}
                        <div class="relative h-52 bg-stone-100 overflow-hidden">
                            @if($auction->product->thumbnail_img)
                                <img src="{{ asset($auction->product->thumbnail_img) }}" alt="{{ $auction->product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <i class="fas fa-image text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                <i class="fas fa-gavel mr-1"></i>Lelang
                            </div>
                            @if($auction->buy_now_price)
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur text-stone-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                                    Beli Langsung: Rp {{ number_format($auction->buy_now_price, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-5">
                            <h3 class="font-semibold text-stone-900 mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
                                {{ $auction->product->name }}
                            </h3>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-stone-500">Tawaran Saat Ini</span>
                                    <span class="font-bold text-brand-600 text-lg">
                                        Rp {{ number_format($auction->current_bid ?? $auction->starting_bid, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-stone-500">Harga Awal</span>
                                    <span class="text-stone-600">Rp {{ number_format($auction->starting_bid, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-stone-500">Total Tawaran</span>
                                    <span class="text-stone-600 font-medium">{{ $auction->bids_count }}</span>
                                </div>
                            </div>

                            {{-- Countdown --}}
                            <div class="bg-brand-50 rounded-xl p-3 text-center" x-data="countdown('{{ $auction->end_date->toIso8601String() }}')">
                                <p class="text-xs text-brand-600 font-medium mb-1">Berakhir dalam</p>
                                <div class="flex items-center justify-center gap-2 text-brand-700 font-bold text-lg">
                                    <span x-text="days">00</span><span>:</span>
                                    <span x-text="hours">00</span><span>:</span>
                                    <span x-text="minutes">00</span><span>:</span>
                                    <span x-text="seconds">00</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $auctions->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-gavel text-6xl text-stone-300 mb-4 block"></i>
                <p class="text-stone-500 text-lg">Belum ada lelang yang aktif saat ini.</p>
                <p class="text-stone-400 text-sm mt-1">Silakan kembali lagi nanti untuk melihat lelang terbaru.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('countdown', (endDate) => ({
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            interval: null,

            init() {
                this.update();
                this.interval = setInterval(() => this.update(), 1000);
            },

            update() {
                const now = new Date().getTime();
                const end = new Date(endDate).getTime();
                const diff = end - now;

                if (diff <= 0) {
                    this.days = '00';
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    clearInterval(this.interval);
                    return;
                }

                this.days = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
            }
        }));
    });
</script>
@endpush
@endsection
