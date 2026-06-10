@extends('customer.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="customerDashboard()">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-stone-900" x-text="'Halo, ' + userName + '! 👋'"></h1>
        <p class="text-stone-500 mt-1">Selamat datang kembali. Ada yang bisa kami bantu hari ini?</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Total Pesanan</span>
            </div>
            <span class="text-3xl font-extrabold text-stone-900" x-text="stats.totalOrders"></span>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Pesanan Aktif</span>
            </div>
            <span class="text-3xl font-extrabold text-stone-900" x-text="stats.activeOrders"></span>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Wallet Balance</span>
            </div>
            <span class="text-3xl font-extrabold text-stone-900" x-text="'Rp ' + formatRupiah(stats.walletBalance)"></span>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wide">Poin Loyalty</span>
            </div>
            <span class="text-3xl font-extrabold text-stone-900" x-text="stats.loyaltyPoints"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <a href="/" class="flex items-center gap-4 p-5 bg-white border border-stone-200 rounded-2xl hover:border-brand-300 hover:shadow-md transition-all duration-200 group">
            <div class="w-12 h-12 rounded-xl bg-brand-100 flex items-center justify-center group-hover:bg-brand-200 transition">
                <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <p class="font-bold text-stone-800">Belanja Sekarang</p>
                <p class="text-sm text-stone-500">Lihat produk terbaru</p>
            </div>
        </a>

        <a href="/customer/pesanan" class="flex items-center gap-4 p-5 bg-white border border-stone-200 rounded-2xl hover:border-brand-300 hover:shadow-md transition-all duration-200 group">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="font-bold text-stone-800">Lacak Pesanan</p>
                <p class="text-sm text-stone-500">Pantau status pesanan</p>
            </div>
        </a>

        <a href="/customer/ulasan" class="flex items-center gap-4 p-5 bg-white border border-stone-200 rounded-2xl hover:border-brand-300 hover:shadow-md transition-all duration-200 group">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <div>
                <p class="font-bold text-stone-800">Tulis Ulasan</p>
                <p class="text-sm text-stone-500">Bagikan pengalamanmu</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-stone-200 flex items-center justify-between">
                    <h2 class="font-bold text-stone-900">Pesanan Terbaru</h2>
                    <a href="/customer/pesanan" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">Lihat Semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">Kode</th>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">Tanggal</th>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">Status</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600">Total</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="order in recentOrders" :key="order.code">
                                <tr class="border-b border-stone-100 hover:bg-stone-50/50 transition">
                                    <td class="px-4 py-3 font-semibold text-brand-600" x-text="'#' + order.code"></td>
                                    <td class="px-4 py-3 text-stone-500" x-text="order.date"></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full" :class="statusBadge(order.status)" x-text="order.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold" x-text="'Rp ' + formatRupiah(order.total)"></td>
                                    <td class="px-4 py-3 text-right">
                                        <a :href="'/customer/pesanan/' + order.code" class="text-brand-600 hover:text-brand-700 text-xs font-bold">Detail</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div x-show="recentOrders.length === 0" class="p-8 text-center text-stone-400">
                    Belum ada pesanan. <a href="/" class="text-brand-600 font-semibold">Mulai belanja →</a>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-stone-200 flex items-center justify-between">
                    <h2 class="font-bold text-stone-900">Wishlist</h2>
                    <a href="/customer/wishlist" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">Lihat Semua →</a>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="item in wishlistItems" :key="item.name">
                        <a :href="'/produk/' + item.slug" class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 transition group">
                            <img :src="item.image" :alt="item.name" class="w-14 h-14 rounded-xl object-cover bg-stone-100 flex-shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-stone-800 truncate group-hover:text-brand-600 transition" x-text="item.name"></p>
                                <p class="text-xs font-bold text-brand-600" x-text="'Rp ' + formatRupiah(item.price)"></p>
                            </div>
                        </a>
                    </template>
                </div>
                <div x-show="wishlistItems.length === 0" class="p-8 text-center text-stone-400">
                    <p class="text-3xl mb-2">🤍</p>
                    <p>Wishlist kosong</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function customerDashboard() {
        return {
            userName: 'Budi Santoso',
            stats: {
                totalOrders: 24,
                activeOrders: 3,
                walletBalance: 850000,
                loyaltyPoints: 1250
            },
            recentOrders: [
                { code: 'ORD-MZ9XK4L2', date: '10 Jun 2026', status: 'Menunggu Pembayaran', total: 1410800 },
                { code: 'ORD-NY7WK3J1', date: '08 Jun 2026', status: 'Dikirim', total: 550000 },
                { code: 'ORD-OX6VJ2H0', date: '05 Jun 2026', status: 'Selesai', total: 387000 },
                { code: 'ORD-PW5UI1G9', date: '01 Jun 2026', status: 'Selesai', total: 1299000 },
                { code: 'ORD-QV4TH0F8', date: '28 Mei 2026', status: 'Dibatalkan', total: 235000 },
            ],
            wishlistItems: [
                { name: 'Sepatu Running Pro X', price: 450000, slug: 'sepatu-running-pro-x', image: 'https://placehold.co/112x112/e2e8f0/64748b?text=S' },
                { name: 'Tas Ransel Urban', price: 275000, slug: 'tas-ransel-urban', image: 'https://placehold.co/112x112/e2e8f0/64748b?text=T' },
                { name: 'Kaos Premium Cotton', price: 129000, slug: 'kaos-premium-cotton', image: 'https://placehold.co/112x112/e2e8f0/64748b?text=K' },
                { name: 'Headphone Wireless', price: 890000, slug: 'headphone-wireless', image: 'https://placehold.co/112x112/e2e8f0/64748b?text=H' },
            ],

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
@endsection
