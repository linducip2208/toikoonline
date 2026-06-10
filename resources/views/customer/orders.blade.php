@extends('customer.layout')
@section('title', 'Pesanan Saya')
@section('page-title', 'Pesanan Saya')

@section('content')
<div x-data="ordersPage()">
    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-hide">
        <template x-for="tab in tabs" :key="tab.key">
            <button @click="activeTab = tab.key"
                class="px-4 py-2.5 text-sm font-semibold rounded-xl whitespace-nowrap transition-all duration-200"
                :class="activeTab === tab.key
                    ? 'bg-brand-600 text-white shadow-md'
                    : 'bg-white border border-stone-200 text-stone-600 hover:bg-stone-50'">
                <span x-text="tab.label"></span>
                <span class="ml-1.5 text-xs opacity-70" x-text="'(' + tab.count + ')'"></span>
            </button>
        </template>
    </div>

    <div class="space-y-4">
        <template x-for="order in filteredOrders" :key="order.code">
            <div class="bg-white border border-stone-200 rounded-2xl p-5 sm:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="font-extrabold text-brand-600" x-text="'# ' + order.code"></span>
                        <span class="text-stone-300">|</span>
                        <span class="text-sm text-stone-500" x-text="order.date"></span>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full self-start" :class="statusBadge(order.status)" x-text="order.status"></span>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex items-center gap-3 flex-1">
                        <template x-for="(img, idx) in order.images" :key="idx">
                            <img :src="img" class="w-14 h-14 rounded-xl object-cover bg-stone-100 border border-stone-200" :alt="'Produk ' + (idx + 1)">
                        </template>
                        <div x-show="order.totalItems > 3" class="w-14 h-14 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-sm font-bold text-stone-500" x-text="'+' + (order.totalItems - 3)"></div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-6 sm:gap-8">
                        <div class="text-right">
                            <p class="text-xs text-stone-500 mb-0.5">Total</p>
                            <p class="text-lg font-extrabold text-stone-900" x-text="'Rp ' + formatRupiah(order.total)"></p>
                        </div>
                        <a :href="'/customer/pesanan/' + order.code"
                            class="px-5 py-2.5 text-sm font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-xl hover:bg-brand-100 hover:border-brand-300 transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="filteredOrders.length === 0" class="text-center py-16">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-stone-800 mb-2">Tidak ada pesanan</h3>
            <p class="text-stone-500 mb-6" x-text="activeTab === 'all' ? 'Kamu belum memiliki pesanan.' : 'Tidak ada pesanan dengan status ini.'"></p>
            <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Mulai Belanja
            </a>
        </div>
    </div>

    <div class="flex items-center justify-center gap-2 mt-8" x-show="totalPages > 1">
        <button @click="currentPage--" :disabled="currentPage === 1"
            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-stone-200 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-stone-100 transition"
            :class="currentPage === 1 ? 'text-stone-300' : 'text-stone-600'">← Sebelumnya</button>

        <template x-for="page in visiblePages" :key="page">
            <button @click="currentPage = page"
                class="w-10 h-10 text-sm font-bold rounded-xl transition"
                :class="currentPage === page ? 'bg-brand-600 text-white' : 'text-stone-600 hover:bg-stone-100'"
                x-text="page"></button>
        </template>

        <button @click="currentPage++" :disabled="currentPage === totalPages"
            class="px-4 py-2.5 text-sm font-semibold rounded-xl border border-stone-200 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-stone-100 transition"
            :class="currentPage === totalPages ? 'text-stone-300' : 'text-stone-600'">Selanjutnya →</button>
    </div>
</div>

<script>
    function ordersPage() {
        const allOrders = [
            { code: 'ORD-MZ9XK4L2', date: '10 Jun 2026', status: 'Menunggu Pembayaran', total: 1410800, totalItems: 4, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1','https://placehold.co/112x112/e2e8f0/64748b?text=P2','https://placehold.co/112x112/e2e8f0/64748b?text=P3'] },
            { code: 'ORD-NY7WK3J1', date: '08 Jun 2026', status: 'Dikirim', total: 550000, totalItems: 2, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1','https://placehold.co/112x112/e2e8f0/64748b?text=P2'] },
            { code: 'ORD-OX6VJ2H0', date: '05 Jun 2026', status: 'Selesai', total: 387000, totalItems: 1, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1'] },
            { code: 'ORD-PW5UI1G9', date: '01 Jun 2026', status: 'Selesai', total: 1299000, totalItems: 5, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1','https://placehold.co/112x112/e2e8f0/64748b?text=P2','https://placehold.co/112x112/e2e8f0/64748b?text=P3'] },
            { code: 'ORD-QV4TH0F8', date: '28 Mei 2026', status: 'Dibatalkan', total: 235000, totalItems: 1, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1'] },
            { code: 'ORD-RU3SG9E7', date: '25 Mei 2026', status: 'Diproses', total: 765000, totalItems: 3, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1','https://placehold.co/112x112/e2e8f0/64748b?text=P2','https://placehold.co/112x112/e2e8f0/64748b?text=P3'] },
            { code: 'ORD-ST2RF8D6', date: '20 Mei 2026', status: 'Selesai', total: 412000, totalItems: 2, images: ['https://placehold.co/112x112/e2e8f0/64748b?text=P1','https://placehold.co/112x112/e2e8f0/64748b?text=P2'] },
        ];

        return {
            activeTab: 'all',
            currentPage: 1,
            perPage: 5,
            tabs: [
                { key: 'all', label: 'Semua', count: 7 },
                { key: 'Menunggu Pembayaran', label: 'Belum Bayar', count: 1 },
                { key: 'Diproses', label: 'Diproses', count: 1 },
                { key: 'Dikirim', label: 'Dikirim', count: 1 },
                { key: 'Selesai', label: 'Selesai', count: 3 },
                { key: 'Dibatalkan', label: 'Dibatalkan', count: 1 },
            ],

            get filteredOrders() {
                let orders = this.activeTab === 'all' ? allOrders : allOrders.filter(o => o.status === this.activeTab);
                const start = (this.currentPage - 1) * this.perPage;
                return orders.slice(start, start + this.perPage);
            },
            get totalPages() {
                const orders = this.activeTab === 'all' ? allOrders : allOrders.filter(o => o.status === this.activeTab);
                return Math.max(1, Math.ceil(orders.length / this.perPage));
            },
            get visiblePages() {
                const pages = [];
                for (let i = 1; i <= this.totalPages; i++) pages.push(i);
                return pages;
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
            formatRupiah(n) {
                return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        };
    }
</script>

<style>
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
@endsection
