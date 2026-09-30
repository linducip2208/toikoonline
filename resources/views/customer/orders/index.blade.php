@extends('customer.layout')
@section('title', 'Pesanan Saya')
@section('page-title', 'Pesanan Saya')

@section('content')
<div>
    <h1 class="text-2xl font-extrabold text-stone-900 mb-6">Pesanan Saya</h1>

    @if($orders->count() > 0)
        <div class="space-y-4" role="list" aria-label="Daftar pesanan">
            @foreach($orders as $order)
                @php
                    $deliveryLabels = [
                        'pending' => 'Menunggu Diproses', 'confirmed' => 'Dikonfirmasi',
                        'picked_up' => 'Diproses', 'on_delivery' => 'Dikirim',
                        'delivered' => 'Selesai', 'cancelled' => 'Dibatalkan',
                    ];
                    $deliveryColors = [
                        'pending' => 'bg-stone-100 text-stone-600', 'confirmed' => 'bg-blue-100 text-blue-800',
                        'picked_up' => 'bg-purple-100 text-purple-800', 'on_delivery' => 'bg-purple-100 text-purple-800',
                        'delivered' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $paymentLabels = ['paid' => 'Dibayar', 'unpaid' => 'Belum Bayar', 'refunded' => 'Refund'];
                @endphp
                <div role="listitem" class="bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="font-extrabold text-brand-600"># {{ $order->code }}</span>
                            <span class="text-stone-300" aria-hidden="true">|</span>
                            <span class="text-sm text-stone-500">{{ $order->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full {{ $deliveryColors[$order->delivery_status] ?? 'bg-stone-100 text-stone-600' }}">{{ $deliveryLabels[$order->delivery_status] ?? $order->delivery_status }}</span>
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status === 'refunded' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex items-center gap-3 flex-1">
                            @foreach($order->orderDetails->take(3) as $detail)
                                @if($detail->product?->thumbnail_img)
                                    <img src="{{ asset($detail->product->thumbnail_img) }}" alt="{{ $detail->product->name }}" class="w-14 h-14 rounded-xl object-cover bg-stone-100 border border-stone-200" loading="lazy">
                                @else
                                    <span class="w-14 h-14 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-xs font-bold text-stone-400" aria-hidden="true">{{ strtoupper(substr($detail->product->name ?? '?', 0, 1)) }}</span>
                                @endif
                            @endforeach
                            @if($order->orderDetails->count() > 3)
                                <span class="w-14 h-14 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-sm font-bold text-stone-500">+{{ $order->orderDetails->count() - 3 }}</span>
                            @endif
                            <span class="text-sm text-stone-500">{{ $order->orderDetails->sum('quantity') }} item</span>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-6 sm:gap-8">
                            <div class="text-right">
                                <p class="text-xs text-stone-500 mb-0.5">Total</p>
                                <p class="text-lg font-extrabold text-stone-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('customer.orders.show', $order->id) }}" aria-label="Lihat detail pesanan {{ $order->code }}"
                                class="inline-flex items-center justify-center min-h-[44px] px-5 text-sm font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-xl hover:bg-brand-100 hover:border-brand-300 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-16" role="status">
            <div class="text-6xl mb-4" aria-hidden="true">📦</div>
            <h2 class="text-xl font-bold text-stone-800 mb-2">Tidak ada pesanan</h2>
            <p class="text-stone-500 mb-6">Kamu belum memiliki pesanan.</p>
            <a href="/" class="inline-flex items-center justify-center min-h-[44px] gap-2 px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection
