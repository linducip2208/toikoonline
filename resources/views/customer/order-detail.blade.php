@extends('customer.layout')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div x-data="orderDetailPage()">
    <div class="mb-6">
        <a href="/customer/pesanan" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-brand-600 transition mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Pesanan
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-stone-900" x-text="'Pesanan #' + order.code"></h1>
                <p class="text-sm text-stone-500 mt-1" x-text="'Dibuat pada ' + order.date"></p>
            </div>
            <span class="px-4 py-2 text-sm font-bold rounded-full self-start" :class="statusBadge(order.status)" x-text="order.status"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-stone-900 mb-6">Status Pesanan</h2>
                <div class="relative">
                    <template x-for="(step, idx) in timeline" :key="idx">
                        <div class="flex gap-4 pb-8 last:pb-0">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all"
                                    :class="idx <= currentTimelineIdx ? 'bg-brand-600 text-white' : 'bg-stone-200 text-stone-400'">
                                    <span x-show="idx < currentTimelineIdx" class="text-lg">✓</span>
                                    <span x-html="idx <= currentTimelineIdx ? step.icon : step.icon"></span>
                                </div>
                                <div x-show="idx < timeline.length - 1" class="w-0.5 flex-1 mt-2 transition-colors"
                                    :class="idx < currentTimelineIdx ? 'bg-brand-500' : 'bg-stone-200'"></div>
                            </div>
                            <div class="flex-1" :class="idx > currentTimelineIdx ? 'opacity-50' : ''">
                                <p class="font-bold text-stone-800" x-text="step.label"></p>
                                <p class="text-sm text-stone-500" x-text="step.date || 'Menunggu'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-stone-200">
                    <h2 class="font-bold text-stone-900">Detail Produk</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">Produk</th>
                                <th class="text-center px-4 py-3 font-semibold text-stone-600">Qty</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600">Harga</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in order.items" :key="idx">
                                <tr class="border-b border-stone-100 last:border-b-0">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img :src="item.image" class="w-12 h-12 rounded-lg object-cover bg-stone-100 flex-shrink-0">
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
                <h3 class="font-bold text-stone-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(order.subtotal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Ongkos Kirim</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(order.shippingCost)"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.discount > 0">
                        <span class="text-stone-500">Diskon</span>
                        <span class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(order.discount)"></span>
                    </div>
                    <div class="flex justify-between border-t border-stone-200 pt-2.5 mt-2.5 text-base">
                        <span class="font-bold text-stone-800">Total</span>
                        <span class="font-extrabold text-brand-600" x-text="'Rp ' + formatRupiah(order.total)"></span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm" x-show="order.shippingAddress">
                <h3 class="font-bold text-stone-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Alamat Pengiriman
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
                    Info Pembayaran
                </h3>
                <div class="text-sm space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Metode</span>
                        <span class="font-semibold text-stone-800" x-text="order.paymentInfo.method"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.paymentInfo.bankName">
                        <span class="text-stone-500">Bank</span>
                        <span class="font-semibold text-stone-800" x-text="order.paymentInfo.bankName"></span>
                    </div>
                    <div class="flex justify-between" x-show="order.paymentInfo.accountNumber">
                        <span class="text-stone-500">Nomor Rekening</span>
                        <span class="font-semibold font-mono text-stone-800" x-text="order.paymentInfo.accountNumber"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <template x-if="order.status === 'Menunggu Pembayaran'">
            <a :href="'/customer/pesanan/bayar?order=' + order.code"
                class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Bukti Bayar
            </a>
        </template>

        <template x-if="order.status === 'Dikirim'">
            <button @click="confirmReceived()"
                class="px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Konfirmasi Terima
            </button>
        </template>

        <template x-if="order.status === 'Selesai'">
            <a href="" @click.prevent="requestRefund()"
                class="px-6 py-3 text-sm font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 hover:border-amber-300 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Ajukan Refund
            </a>
        </template>

        <a :href="'/customer/pesanan/' + order.code + '/invoice'"
            class="px-6 py-3 text-sm font-bold text-stone-600 bg-stone-100 border border-stone-200 rounded-xl hover:bg-stone-200 hover:border-stone-300 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Invoice
        </a>
    </div>
</div>

<script>
    function orderDetailPage() {
        return {
            order: {
                code: 'ORD-MZ9XK4L2',
                date: '10 Juni 2026, 14:35 WIB',
                status: 'Dikirim',
                subtotal: 1562000,
                shippingCost: 35000,
                discount: 156200,
                total: 1410800,
                items: [
                    { name: 'Sepatu Running Pro X', variant: 'Warna: Hitam, Ukuran: 42', price: 450000, qty: 2, image: 'https://placehold.co/96x96/e2e8f0/64748b?text=S' },
                    { name: 'Tas Ransel Urban Explorer', variant: null, price: 275000, qty: 1, image: 'https://placehold.co/96x96/e2e8f0/64748b?text=T' },
                    { name: 'Kaos Premium Cotton', variant: 'Warna: Navy, Ukuran: L', price: 129000, qty: 3, image: 'https://placehold.co/96x96/e2e8f0/64748b?text=K' },
                ],
                shippingAddress: {
                    name: 'Budi Santoso',
                    phone: '0812-3456-7890',
                    full: 'Jl. Merdeka No. 123, RT 04/05, Kec. Sukamaju, Jakarta Selatan, 12345'
                },
                paymentInfo: {
                    method: 'Transfer Bank BCA',
                    bankName: 'Bank BCA',
                    accountNumber: '8721 1234 5678 9012'
                },
            },
            timeline: [
                { label: 'Pesanan Dibuat', date: '10 Jun 2026, 14:35', icon: '🛒' },
                { label: 'Pembayaran Dikonfirmasi', date: '10 Jun 2026, 15:02', icon: '💰' },
                { label: 'Diproses', date: '10 Jun 2026, 16:20', icon: '📦' },
                { label: 'Dikirim', date: '11 Jun 2026, 08:15', icon: '🚚' },
                { label: 'Selesai', date: null, icon: '✅' },
            ],

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
                if (confirm('Konfirmasi bahwa pesanan sudah diterima?')) {
                    alert('Pesanan dikonfirmasi! Terima kasih sudah berbelanja.');
                    this.order.status = 'Selesai';
                }
            },

            requestRefund() {
                if (confirm('Ajukan pengembalian dana untuk pesanan ini?')) {
                    alert('Refund berhasil diajukan. Tim kami akan menghubungi kamu.');
                }
            },

            formatRupiah(n) {
                return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        };
    }
</script>
@endsection
