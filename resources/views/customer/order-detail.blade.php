@extends('customer.layout')
@section('title', __('customer.order_detail'))
@section('page-title', __('customer.order_detail'))

@section('content')
<div x-data="orderDetailPage()">
    <div class="mb-6">
        <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-brand-600 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('customer.back_to_orders') }}
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-stone-900" x-text="'{{ __('customer.orders.number_sign') }}' + order.code"></h1>
                <p class="text-sm text-stone-500 mt-1" x-text="'{{ __('customer.orders.placed_on') }} ' + order.date"></p>
            </div>
            <span class="px-4 py-2 text-sm font-bold rounded-full self-start" :class="statusBadge(order.status)" x-text="order.status"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-6">{{ __('customer.order_status') }}</h2>
                <div class="relative">
                    <template x-for="(step, idx) in timeline" :key="idx">
                        <div class="flex gap-4 pb-8 last:pb-0">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all text-sm font-bold"
                                    :class="step.done ? 'bg-brand-600 text-white' : 'bg-stone-200 text-stone-400'">
                                    <span x-show="step.done">✓</span>
                                    <span x-show="!step.done" x-text="idx + 1"></span>
                                </div>
                                <div x-show="idx < timeline.length - 1" class="w-0.5 flex-1 mt-2 transition-colors"
                                    :class="step.done ? 'bg-brand-500' : 'bg-stone-200'"></div>
                            </div>
                            <div class="flex-1" :class="!step.done && idx > currentTimelineIdx ? 'opacity-50' : ''">
                                <p class="font-bold text-stone-800" x-text="step.label"></p>
                                <p class="text-sm text-stone-500" x-text="step.date || '{{ __('customer.orders.waiting') }}'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-stone-200">
                    <h2 class="font-bold text-stone-900">{{ __('customer.product_detail') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <caption class="sr-only">Rincian produk dalam pesanan</caption>
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th scope="col" class="text-left px-4 py-3 font-semibold text-stone-600">{{ __('customer.col_product') }}</th>
                                <th scope="col" class="text-center px-4 py-3 font-semibold text-stone-600">Qty</th>
                                <th scope="col" class="text-right px-4 py-3 font-semibold text-stone-600">{{ __('customer.col_price') }}</th>
                                <th scope="col" class="text-right px-4 py-3 font-semibold text-stone-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in order.items" :key="idx">
                                <tr class="border-b border-stone-100 last:border-b-0">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-lg object-cover bg-stone-100 flex-shrink-0">
                                            <div>
                                                <p class="font-semibold text-stone-800" x-text="item.name"></p>
                                                <p x-show="item.variant" class="text-xs text-stone-400 mt-0.5" x-text="item.variant"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center" x-text="item.qty"></td>
                                    <td class="px-4 py-4 text-right text-stone-500" x-text="'Rp ' + formatRupiah(item.price)"></td>
                                    <td class="px-4 py-4 text-right font-semibold" x-text="'Rp ' + formatRupiah(item.price * item.qty)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 mb-4">{{ __('customer.payment_summary') }}</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(order.subtotal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">{{ __('customer.shipping_cost') }}</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(order.shippingCost)"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.discount > 0">
                        <span class="text-stone-500">{{ __('customer.discount') }}</span>
                        <span class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(order.discount)"></span>
                    </div>
                    <div class="flex justify-between border-t border-stone-200 pt-2.5 mt-2.5 text-base">
                        <span class="font-bold text-stone-800">{{ __('customer.col_total') }}</span>
                        <span class="font-extrabold text-brand-600" x-text="'Rp ' + formatRupiah(order.total)"></span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm" x-show="order.shippingAddress">
                <h3 class="font-bold text-stone-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('customer.shipping_address') }}
                </h3>
                <div class="text-sm">
                    <p class="font-semibold text-stone-800" x-text="order.shippingAddress.name"></p>
                    <p class="text-stone-500" x-text="order.shippingAddress.phone"></p>
                    <p class="text-stone-500 mt-1" x-text="order.shippingAddress.full"></p>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm" x-show="order.paymentInfo">
                <h3 class="font-bold text-stone-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    {{ __('customer.payment_info') }}
                </h3>
                <div class="text-sm space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-stone-500">{{ __('customer.method') }}</span>
                        <span class="font-semibold text-stone-800" x-text="order.paymentInfo.method"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.paymentInfo.bankName">
                        <span class="text-stone-500">Bank</span>
                        <span class="font-semibold text-stone-800" x-text="order.paymentInfo.bankName"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.paymentInfo.accountNumber">
                        <span class="text-stone-500">{{ __('customer.account_no') }}</span>
                        <span class="font-semibold font-mono text-stone-800" x-text="order.paymentInfo.accountNumber"></span>
                    </div>
                </div>
            </div>

            {{-- Lacak pengiriman live (delivery_histories DB + API Biteship/RajaOngkir) --}}
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 mb-1 flex items-center gap-2">
                    <i class="fas fa-truck text-brand-500"></i>{{ __('customer.track_shipping') }}
                </h3>
                <p class="text-xs text-stone-500 mb-4">{{ __('customer.track_history_note') }}</p>
                @if(isset($trackingTimeline) && $trackingTimeline->count())
                <ol class="relative border-l border-stone-200 ml-2 space-y-4" aria-label="Riwayat pengiriman">
                    @foreach($trackingTimeline as $t)
                    <li class="ml-4">
                        <span class="absolute -left-[7px] mt-1 w-3 h-3 rounded-full bg-brand-500 ring-4 ring-brand-100"></span>
                        <p class="text-sm font-semibold text-stone-800">{{ $t['label'] }}</p>
                        <p class="text-[11px] text-stone-400">{{ $t['date'] }}</p>
                        @if($t['note'])<p class="text-xs text-stone-500 mt-0.5">{{ $t['note'] }}</p>@endif
                    </li>
                    @endforeach
                </ol>
                @else
                <div x-data="{ waybill: '', courier: 'jne', result: null, loading: false, err: '' }" class="text-sm">
                    <div class="flex gap-2">
                        <input x-model="waybill" placeholder="{{ __('customer.waybill_ph') }}" class="flex-1 px-3 py-2.5 min-h-[44px] border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400" aria-label="Nomor resi">
                        <button @click="loading=true; err=''; fetch(`/api/shipping/track/${waybill}?courier=${courier}`).then(r=>r.json()).then(d=>{result=d.data; if(!d.success) err=d.message||'Tidak ditemukan'}).catch(e=>err=e.message).finally(()=>loading=false)" class="px-4 min-h-[44px] bg-stone-900 text-white text-sm font-semibold rounded-xl" :disabled="!waybill">{{ __('customer.track_btn') }}</button>
                    </div>
                    <p x-show="err" x-text="err" class="text-xs text-red-600 mt-2"></p>
                    <pre x-show="result" x-text="JSON.stringify(result,null,2)" class="text-[11px] bg-stone-50 border rounded-xl p-3 mt-2 overflow-auto max-h-48"></pre>
                    <p class="text-[11px] text-stone-400 mt-2">{{ __('customer.no_history_hint') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        @if(session('success'))
        <div class="w-full bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="w-full bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3" role="alert">{{ session('error') }}</div>
        @endif

        <template x-if="order.status === 'Menunggu Pembayaran'">
            <div class="flex flex-wrap gap-3">
                @if(!empty($snapToken))
                <button @click="payNow()" class="px-6 py-3 min-h-[44px] text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fas fa-qrcode"></i>{{ __('customer.pay_now') }}
                </button>
                @elseif($order->manual_payment)
                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo, konfirmasi pembayaran manual order '.$order->code) }}" target="_blank" rel="noopener"
                    class="px-6 py-3 min-h-[44px] text-sm font-bold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i>{{ __('customer.confirm_wa') }}
                </a>
                @endif
            </div>
        </template>

        <template x-if="order.status === 'Dikirim'">
            <form action="{{ route('customer.orders.receive', $order) }}" method="POST">
                @csrf
                <button type="submit" aria-label="Konfirmasi pesanan sudah diterima"
                    class="min-h-[44px] px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('customer.confirm_received') }}
                </button>
            </form>
        </template>

        <div x-data="{ refundOpen: false }" x-show="order.status === 'Selesai'" class="flex flex-col gap-2">
            <button @click="refundOpen = !refundOpen"
                class="px-6 py-3 min-h-[44px] text-sm font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                {{ __('customer.request_refund') }}
            </button>
            <form x-show="refundOpen" x-cloak action="{{ route('customer.orders.refund', $order) }}" method="POST" class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3 max-w-md">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-amber-800 mb-1" for="refund_amount">Nominal (maks Rp {{ number_format($order->grand_total, 0, ',', '.') }})</label>
                    <input id="refund_amount" type="number" name="refund_amount" min="1000" max="{{ (int) $order->grand_total }}" required class="w-full px-3 py-2.5 min-h-[44px] border border-amber-300 rounded-xl text-sm bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-amber-800 mb-1" for="refund_reason">{{ __('customer.refund_reason') }}</label>
                    <textarea id="refund_reason" name="refund_reason" rows="3" required placeholder="{{ __('customer.refund_reason_ph') }}" class="w-full px-3 py-2.5 border border-amber-300 rounded-xl text-sm bg-white"></textarea>
                </div>
                <button class="px-5 py-2.5 min-h-[44px] bg-amber-600 text-white text-sm font-bold rounded-xl">{{ __('customer.refund_submit') }}</button>
            </form>
        </div>

        <a href="{{ route('customer.orders.invoice', $order) }}"
            class="px-6 py-3 min-h-[44px] text-sm font-bold text-stone-600 bg-stone-100 border border-stone-200 rounded-xl hover:bg-stone-200 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ __('customer.download_invoice') }}
        </a>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    function orderDetailPage() {
        const snapToken = @json($snapToken ?? null);
        function payNow() { if (snapToken && window.snap) window.snap.pay(snapToken); }
        return {
            payNow,
            order: @json($orderPayload ?? []),
            timeline: @json($timelineSteps ?? []),

            get currentTimelineIdx() {
                const statusMap = {
                    'Menunggu Pembayaran': 0,
                    'Diproses': 2,
                    'Dikirim': 3,
                    'Selesai': 4,
                    'Dibatalkan': -1,
                };
                return statusMap[this.order.status] ?? 0;
            },

            statusBadge(status) {
                const map = {
                    'Menunggu Pembayaran': 'bg-yellow-100 text-yellow-800',
                    'Diproses': 'bg-blue-100 text-blue-800',
                    'Dikirim': 'bg-purple-100 text-purple-800',
                    'Selesai': 'bg-green-100 text-green-800',
                    'Dibatalkan': 'bg-red-100 text-red-800',
                };
                return map[status] || 'bg-stone-100 text-stone-600';
            },

            confirmReceived() {
                // Digantikan form POST ke orders.receive (server-side, tercatat di timeline).
            },

            requestRefund() {
                // Digantikan form refund di bawah (POST ke orders.refund).
            },

            formatRupiah(n) {
                return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        };
    }
</script>
@endsection
