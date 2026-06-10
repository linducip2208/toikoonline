@extends('layouts.storefront')

@section('title', $auction->product->name . ' - Lelang')

@section('content')
<div class="bg-gradient-to-br from-brand-50 to-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-stone-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('auctions.index') }}" class="hover:text-brand-600">Lelang</a>
            <span class="mx-2">/</span>
            <span class="text-stone-800">{{ $auction->product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Left: Product Info & Image --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Product Image --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                    @if($auction->product->thumbnail_img)
                        <img src="{{ asset($auction->product->thumbnail_img) }}" alt="{{ $auction->product->name }}"
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-stone-100 flex items-center justify-center text-stone-300">
                            <i class="fas fa-image text-7xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Product Description --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6">
                    <h2 class="font-semibold text-lg text-stone-900 mb-3">Deskripsi Produk</h2>
                    <div class="text-stone-600 text-sm leading-relaxed prose prose-sm max-w-none">
                        {!! $auction->product->description ?? '<p class="text-stone-400 italic">Tidak ada deskripsi.</p>' !!}
                    </div>
                </div>
            </div>

            {{-- Right: Bid Panel --}}
            <div class="space-y-6">
                {{-- Auction Info Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-stone-200 p-6 sticky top-24">
                    <h1 class="font-semibold text-xl text-stone-900 mb-4">{{ $auction->product->name }}</h1>

                    {{-- Price Info --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-stone-500 text-sm">Harga Awal</span>
                            <span class="font-semibold text-stone-700">Rp {{ number_format($auction->starting_bid, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-stone-500 text-sm">Tawaran Saat Ini</span>
                            <span class="font-bold text-brand-600 text-xl">
                                Rp {{ number_format($auction->current_bid ?? $auction->starting_bid, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-stone-500 text-sm">Kelipatan Tawar</span>
                            <span class="font-medium text-stone-700">Rp {{ number_format($auction->bid_increment, 0, ',', '.') }}</span>
                        </div>
                        @if($auction->buy_now_price)
                            <div class="flex justify-between items-center">
                                <span class="text-stone-500 text-sm">Beli Langsung</span>
                                <span class="font-semibold text-green-600">Rp {{ number_format($auction->buy_now_price, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Countdown --}}
                    <div class="bg-brand-50 rounded-xl p-4 text-center mb-6" x-data="countdown('{{ $auction->end_date->toIso8601String() }}')">
                        <p class="text-xs text-brand-600 font-medium mb-1">
                            @if($auction->status === 'active')
                                Lelang Berakhir Dalam
                            @elseif($auction->status === 'ended')
                                Lelang Telah Berakhir
                            @else
                                Status: {{ $auction->status }}
                            @endif
                        </p>
                        <div class="flex items-center justify-center gap-2 text-brand-700 font-bold text-2xl">
                            <span x-text="days">00</span><span>:</span>
                            <span x-text="hours">00</span><span>:</span>
                            <span x-text="minutes">00</span><span>:</span>
                            <span x-text="seconds">00</span>
                        </div>
                    </div>

                    {{-- Bid Form --}}
                    @if($auction->status === 'active')
                        @auth
                            <form action="{{ route('auctions.bid', $auction->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-stone-700 mb-1">Jumlah Tawaran (Rp)</label>
                                    <input type="number" name="amount"
                                           min="{{ ($auction->current_bid ?? $auction->starting_bid) + $auction->bid_increment }}"
                                           step="1000"
                                           class="w-full rounded-xl border-stone-300 text-lg py-3 px-4 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400"
                                           placeholder="Masukkan jumlah tawaran..."
                                           required>
                                </div>
                                <button type="submit"
                                        class="w-full py-3 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                                    <i class="fas fa-gavel mr-2"></i>Ajukan Tawaran
                                </button>
                            </form>
                        @else
                            <div class="text-center p-4 bg-stone-50 rounded-xl">
                                <p class="text-sm text-stone-500 mb-3">Silakan masuk untuk mengajukan tawaran.</p>
                                <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition-colors">
                                    Masuk
                                </a>
                            </div>
                        @endauth
                    @else
                        <div class="text-center p-4 bg-stone-100 rounded-xl">
                            <p class="text-sm text-stone-500">
                                @if($auction->status === 'ended')
                                    Lelang telah berakhir.
                                    @if($auction->winner_id)
                                        <br>Pemenang: <span class="font-semibold text-brand-600">{{ $auction->winner?->name }}</span>
                                    @endif
                                @else
                                    Lelang saat ini tidak menerima tawaran.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bid History --}}
        <div class="mt-10">
            <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                <div class="p-5 border-b border-stone-200">
                    <h2 class="font-semibold text-lg text-stone-900">
                        <i class="fas fa-history text-brand-600 mr-2"></i>Riwayat Tawaran
                        <span class="text-stone-400 text-sm font-normal ml-2">({{ $bids->count() }} tawaran)</span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-stone-700">#</th>
                                <th class="px-5 py-3 text-left font-semibold text-stone-700">Penawar</th>
                                <th class="px-5 py-3 text-left font-semibold text-stone-700">Jumlah Tawaran</th>
                                <th class="px-5 py-3 text-left font-semibold text-stone-700">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bids as $bid)
                                <tr class="border-b border-stone-100 hover:bg-stone-50/50 transition-colors">
                                    <td class="px-5 py-3 text-stone-500">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3 font-medium text-stone-800">{{ $bid->user->name }}</td>
                                    <td class="px-5 py-3 font-bold text-brand-600">Rp {{ number_format($bid->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-stone-500">{{ $bid->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-stone-400">
                                        Belum ada tawaran. Jadilah yang pertama!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
