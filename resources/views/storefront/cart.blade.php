<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('cart.title') }} — {{ config('app.name', 'TokoOnline') }}</title>
    {{-- CSS lokal (Vite). Catatan: brand disatukan ke indigo (dulu emerald khusus halaman ini) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased" x-data="cartPage()">
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="text-2xl font-extrabold text-brand-600 tracking-tight">{{ config('app.name', 'TokoOnline') }}</a>
            <div class="flex items-center gap-6">
                <a href="/" class="text-sm text-stone-600 hover:text-brand-600 transition">{{ __('cart.continue_shopping') }}</a>
                <a href="/cari" aria-label="Cari produk" class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] text-stone-500 hover:text-stone-700 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="flex items-center gap-2 text-sm text-stone-500 mb-6" aria-label="Breadcrumb">
            <a href="/" class="hover:text-brand-600 transition">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-stone-800 font-semibold" aria-current="page">{{ __('cart.title') }}</span>
        </nav>

        <h1 class="text-3xl font-extrabold text-stone-900 mb-8">{{ __('cart.heading') }} <span class="text-stone-400 text-lg font-medium" x-text="'(' + items.length + ' {{ __('cart.item_suffix') }})'"></span></h1>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3" role="alert">{{ session('error') }}</div>
        @endif

        <div x-show="items.length === 0" class="text-center py-20" x-cloak>
            <div class="text-8xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-stone-800 mb-2">{{ __('cart.empty_title') }}</h2>
            <p class="text-stone-500 mb-6">{{ __('cart.empty_hint') }}</p>
            <a href="/" class="inline-flex items-center gap-2 px-8 py-3.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                {{ __('cart.start_shopping') }}
            </a>
        </div>

        <div x-show="items.length > 0" class="flex flex-col lg:flex-row gap-8" x-cloak>
            <div class="flex-1 space-y-4">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-5 flex flex-col sm:flex-row gap-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex-shrink-0">
                            <img :src="item.image || '{{ asset('marketing/products/placeholder.jpg') }}'" :alt="item.name" loading="lazy" class="w-full sm:w-28 h-28 object-cover rounded-xl bg-stone-100">
                        </div>
                        <div class="flex-1 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-stone-900" x-text="item.name"></h3>
                                <template x-if="item.variant">
                                    <p class="text-sm text-stone-500 mt-0.5" x-text="'{{ __('cart.variant_prefix') }}' + item.variant"></p>
                                </template>
                                <p class="text-brand-600 font-bold mt-2" x-text="'Rp ' + formatRupiah(item.price)"></p>
                            </div>
                            <div class="flex items-center justify-between sm:justify-end gap-6">
                                <div class="flex items-center gap-0 border border-stone-300 rounded-xl overflow-hidden" role="group" :aria-label="'Jumlah ' + item.name">
                                    <button @click="decreaseQty(idx)" aria-label="Kurangi jumlah" class="min-w-[44px] min-h-[44px] flex items-center justify-center text-stone-600 hover:bg-stone-100 transition" :disabled="item.qty <= 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input type="number" x-model.number="item.qty" @input="recalculate()" min="1" :aria-label="'Jumlah ' + item.name" class="w-12 h-11 text-center text-sm font-semibold border-x border-stone-300 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button @click="increaseQty(idx)" aria-label="Tambah jumlah" class="min-w-[44px] min-h-[44px] flex items-center justify-center text-stone-600 hover:bg-stone-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                <p class="text-sm font-bold text-stone-800 w-28 text-right" x-text="'Rp ' + formatRupiah(item.price * item.qty)"></p>
                                <button @click="removeItem(idx)" :aria-label="'Hapus ' + item.name + ' dari keranjang'" class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] p-2 text-stone-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex justify-between items-center pt-2">
                    <a href="/" class="inline-flex items-center gap-1.5 text-sm text-brand-600 hover:text-brand-700 font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        {{ __('cart.continue_shopping') }}
                    </a>
                    <a href="/cari" class="text-sm text-stone-500 hover:text-stone-700">{{ __('cart.search_other') }}</a>
                </div>
            </div>

            <div class="w-full lg:w-96 flex-shrink-0">
                <div class="bg-white rounded-2xl border border-stone-200 p-6 sticky top-24 space-y-5 shadow-sm">
                    <h3 class="font-bold text-lg text-stone-900">{{ __('cart.summary') }}</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Subtotal (<span x-text="items.length"></span> item)</span>
                            <span class="font-semibold text-stone-800" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-stone-500">{{ __('cart.coupon') }} <span class="text-xs text-brand-600" x-show="discount > 0" x-text="'{{ __('cart.applied') }}'" x-cloak></span></span>
                            <span x-show="discount > 0" class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(discount)" x-cloak></span>
                            <span x-show="discount === 0" class="text-stone-400 text-xs">{{ __('cart.no_coupon') }}</span>
                        </div>

                        <div class="flex gap-2">
                            <label for="kupon" class="sr-only">{{ __('cart.coupon_code_label') }}</label>
                            <input id="kupon" type="text" x-model="couponCode" @keyup.enter="applyCoupon()" placeholder="{{ __('cart.coupon_ph') }}" class="flex-1 px-3 py-2 min-h-[44px] text-sm border border-stone-300 rounded-lg outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <button @click="applyCoupon()" class="px-4 py-2 min-h-[44px] text-sm font-semibold text-brand-700 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition">{{ __('cart.apply') }}</button>
                        </div>
                        <p x-show="couponMsg" x-text="couponMsg" :class="couponMsgType === 'error' ? 'text-red-500' : 'text-green-600'" class="text-xs" role="status" x-cloak></p>

                        <div class="border-t border-stone-200 pt-3 flex justify-between text-base">
                            <span class="font-semibold text-stone-800">{{ __('cart.total') }}</span>
                            <span class="font-extrabold text-brand-600 text-lg" x-text="'Rp ' + formatRupiah(total)"></span>
                        </div>
                    </div>

                    <a href="/checkout" class="block w-full py-3.5 min-h-[44px] bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        {{ __('cart.checkout_btn') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        function cartPage() {
            return {
                items: @json($cartPayload ?? []),
                couponCode: '',
                discount: 0,
                couponMsg: '',
                couponMsgType: '',

                get subtotal() {
                    return this.items.reduce((sum, i) => sum + i.price * i.qty, 0);
                },
                get total() {
                    return Math.max(0, this.subtotal - this.discount);
                },

                postAction(url, fields) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name=csrf-token]').content;
                    form.appendChild(csrf);
                    for (const [k, v] of Object.entries(fields)) {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = k;
                        input.value = v ?? '';
                        form.appendChild(input);
                    }
                    document.body.appendChild(form);
                    form.submit();
                },
                increaseQty(idx) {
                    const it = this.items[idx];
                    this.postAction(@json(route('cart.update')), { product_id: it.product_id, variation: it.variation, quantity: it.qty + 1 });
                },
                decreaseQty(idx) {
                    const it = this.items[idx];
                    if (it.qty > 1) {
                        this.postAction(@json(route('cart.update')), { product_id: it.product_id, variation: it.variation, quantity: it.qty - 1 });
                    }
                },
                removeItem(idx) {
                    const it = this.items[idx];
                    this.postAction(@json(route('cart.remove')), { product_id: it.product_id, variation: it.variation });
                },
                async applyCoupon() {
                    if (!this.couponCode) return;
                    this.couponMsg = ''; this.couponMsgType = '';
                    try {
                        const r = await fetch(@json(route('api.coupon.validate')), {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({ code: this.couponCode }),
                        });
                        if (r.status === 401) throw new Error('Masuk dulu untuk memakai kupon — atau isi kodenya saat checkout.');
                        const d = await r.json();
                        if (!d.success) throw new Error(d.message || 'Kode kupon tidak valid');
                        this.discount = d.discount;
                        this.couponMsg = 'Kupon ' + d.code + ' terbaca — diskon Rp ' + this.formatRupiah(d.discount) + ' akan diterapkan saat checkout.';
                        this.couponMsgType = 'success';
                    } catch (e) {
                        this.discount = 0;
                        this.couponMsg = e.message;
                        this.couponMsgType = 'error';
                    }
                },
                formatRupiah(n) {
                    return Number(n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            };
        }
    </script>
</body>
</html>
