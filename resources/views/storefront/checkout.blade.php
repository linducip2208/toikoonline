<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — {{ config('app.name', 'TokoOnline') }}</title>
    {{-- CSS lokal (Vite). Catatan: brand disatukan ke indigo (dulu emerald khusus halaman ini) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased" x-data="checkoutPage()">
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="text-2xl font-extrabold text-brand-600 tracking-tight">{{ config('app.name', 'TokoOnline') }}</a>
            <a href="{{ route('cart.index') }}" class="text-sm text-stone-600 hover:text-brand-600 transition">← Kembali ke Keranjang</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-extrabold text-stone-900 mb-8">Checkout</h1>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
        @endif

        <div class="flex items-center justify-between mb-10 px-2">
            <template x-for="(step, idx) in steps" :key="idx">
                <div class="flex items-center" :class="idx < steps.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                            :class="currentStep >= idx
                                ? 'bg-brand-600 text-white shadow-lg shadow-brand-200'
                                : 'bg-stone-200 text-stone-400'">
                            <span x-show="currentStep > idx" class="text-lg">✓</span>
                            <span x-show="currentStep <= idx" x-text="idx + 1"></span>
                        </div>
                        <span class="text-xs mt-2 font-medium whitespace-nowrap"
                            :class="currentStep >= idx ? 'text-brand-600' : 'text-stone-400'"
                            x-text="step"></span>
                    </div>
                    <template x-if="idx < steps.length - 1">
                        <div class="flex-1 h-0.5 mx-2 mt-[-1.25rem] transition-colors duration-300"
                            :class="currentStep > idx ? 'bg-brand-500' : 'bg-stone-200'"></div>
                    </template>
                </div>
            </template>
        </div>

        <form x-ref="coForm" action="{{ route('checkout.store') }}" method="POST" @submit.prevent="placeOrder()">
            @csrf
            <input type="hidden" name="shipping_address[name]" :value="addrName">
            <input type="hidden" name="shipping_address[phone]" :value="addrPhone">
            <input type="hidden" name="shipping_address[address]" :value="addrAddress">
            <input type="hidden" name="shipping_address[city]" :value="addrCity">
            <input type="hidden" name="shipping_address[postal]" :value="form.postal">
            <input type="hidden" name="shipping_address[area_id]" :value="destArea?.id || ''">
            <input type="hidden" name="shipping_method" :value="shipLabel">
            <input type="hidden" name="shipping_cost" :value="shippingCost">
            <input type="hidden" name="destination_area_id" :value="destArea?.id || ''">
            <input type="hidden" name="courier" :value="shipCourier">
            <input type="hidden" name="payment_type" :value="channels[selectedPayment]?.code || 'manual'">
            <input type="hidden" name="coupon_code" :value="couponOk ? couponCode : ''">

            <div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-sm">

            <div x-show="currentStep === 0" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">📍 Alamat Pengiriman</h2>

                <div x-show="savedAddresses.length > 0" class="mb-6 space-y-3">
                    <template x-for="(addr, idx) in savedAddresses" :key="addr.id">
                        <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="addressType === 'saved' && selectedSavedAddress === idx ? 'border-brand-500 bg-brand-50/30' : 'border-stone-200 hover:border-brand-300'">
                            <input type="radio" name="addressType" value="saved" x-model="addressType" @change="selectedSavedAddress = idx; syncDestFromAddress()" class="w-5 h-5 mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <p class="font-semibold text-stone-800" x-text="addr.label"></p>
                                <p class="text-sm text-stone-500" x-text="addr.full"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition"
                        :class="addressType === 'new' ? 'border-brand-500 bg-brand-50/30' : 'border-stone-200'">
                        <input type="radio" name="addressType" value="new" x-model="addressType" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                        <span class="font-semibold text-stone-800" x-text="savedAddresses.length ? 'Gunakan Alamat Baru' : 'Alamat Pengiriman'"></span>
                    </label>
                </div>

                <div x-show="addressType === 'new'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Penerima *</label>
                            <input type="text" x-model="form.name" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nomor Telepon *</label>
                            <input type="tel" x-model="form.phone" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Alamat Lengkap *</label>
                        <textarea x-model="form.address" rows="3" placeholder="Jalan, RT/RW, kelurahan, kecamatan" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kota / Kabupaten</label>
                            <input type="text" x-model="form.city" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kode Pos</label>
                            <input type="text" x-model="form.postal" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <p x-show="addrError" x-text="addrError" class="text-xs text-red-600"></p>
                </div>
            </div>

            <div x-show="currentStep === 1" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-2">🚚 Metode Pengiriman</h2>
                <p class="text-sm text-stone-500 mb-6">Ongkir live dari kurir (berat total <span x-text="(weight/1000).toFixed(1)"></span> kg).</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kecamatan / Kota Tujuan *</label>
                        <input type="text" x-model="destQuery" @input.debounce.500ms="lookupArea()" placeholder="Ketik cth: Coblong, Bandung" autocomplete="off"
                            class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <div x-show="areas.length" class="absolute z-10 left-0 right-0 mt-1 bg-white border border-stone-200 rounded-xl shadow-lg overflow-hidden">
                            <template x-for="a in areas.slice(0,6)" :key="a.id">
                                <button type="button" @click="selectArea(a)" class="w-full text-left px-3 py-2.5 min-h-[44px] text-sm hover:bg-brand-50 flex justify-between gap-2">
                                    <span x-text="a.name"></span><span class="text-stone-400 text-xs" x-text="a.postal_code"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kurir</label>
                        <select x-model="courier" @change="fetchShipping()" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl bg-white outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="">Semua kurir</option>
                            <option value="jne">JNE</option>
                            <option value="jnt">J&T</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="anteraja">AnterAja</option>
                            <option value="gosend">GoSend</option>
                        </select>
                    </div>
                </div>

                <div x-show="!shipOptions.length" class="border border-dashed border-stone-300 rounded-xl p-6 text-center text-sm text-stone-500">
                    <span x-show="!loadingShip">Pilih tujuan lalu klik <b>Cek Ongkir</b> — atau otomatis dicek saat alamat tersimpan dipilih.</span>
                    <span x-show="loadingShip">Menghitung ongkir…</span>
                </div>
                <button type="button" @click="fetchShipping()" :disabled="loadingShip" class="mb-4 px-6 py-2.5 min-h-[44px] bg-stone-900 text-white text-sm font-semibold rounded-xl disabled:opacity-50">Cek Ongkir</button>
                <p x-show="shipError" x-text="shipError" class="text-xs text-red-600 mb-3"></p>

                <div class="space-y-4" x-show="shipOptions.length">
                    <template x-for="(opt, idx) in shipOptions" :key="idx">
                        <label class="flex items-center gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md"
                            :class="selectedShip === idx ? 'border-brand-500 bg-brand-50/30 shadow-sm' : 'border-stone-200'">
                            <input type="radio" name="shipping" :value="idx" x-model="selectedShip" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-800" x-text="opt.courier.toUpperCase() + ' — ' + opt.service"></span>
                                    <span class="font-extrabold text-brand-600" x-text="'Rp ' + formatRupiah(opt.cost)"></span>
                                </div>
                                <p class="text-sm text-stone-500 mt-1" x-text="'Estimasi: ' + opt.etd + (opt.free_ongkir_applied ? ' · subsidi gratis ongkir Rp ' + formatRupiah(opt.free_ongkir_applied) : '')"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <div x-show="currentStep === 2" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">💳 Metode Pembayaran</h2>

                <div class="space-y-4 mb-6">
                    <template x-for="(method, idx) in channels" :key="idx">
                        <label class="flex items-center gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md"
                            :class="selectedPayment === idx ? 'border-brand-500 bg-brand-50/30 shadow-sm' : 'border-stone-200'">
                            <input type="radio" name="payment" :value="idx" x-model="selectedPayment" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                            <div class="w-12 h-8 rounded bg-stone-100 flex items-center justify-center font-bold text-xs text-stone-500 flex-shrink-0" x-text="method.bank"></div>
                            <div class="flex-1">
                                <span class="font-bold text-stone-800" x-text="method.name"></span>
                                <p class="text-xs text-stone-500 mt-0.5" x-text="method.desc"></p>
                            </div>
                            <template x-if="(method.fee || 0) > 0">
                                <span class="text-xs text-stone-500" x-text="'Biaya: Rp ' + formatRupiah(method.fee)"></span>
                            </template>
                            <template x-if="!(method.fee > 0)">
                                <span class="text-xs text-green-600 font-semibold">Gratis</span>
                            </template>
                        </label>
                    </template>
                </div>

                <div class="border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Punya kode kupon?</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="cth: WELCOME20" class="flex-1 px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl uppercase font-mono outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <button type="button" @click="applyCoupon()" :disabled="couponLoading || !couponCode" class="px-5 min-h-[44px] bg-brand-600 text-white text-sm font-bold rounded-xl disabled:opacity-50">Pakai</button>
                    </div>
                    <p x-show="couponOk" class="text-xs text-green-700 font-semibold mt-2">Kupon <span x-text="couponCode"></span> aktif — hemat Rp <span x-text="formatRupiah(couponDiscount)"></span></p>
                    <p x-show="couponError" x-text="couponError" class="text-xs text-red-600 mt-2"></p>
                </div>
            </div>

            <div x-show="currentStep === 3" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">✅ Konfirmasi Pesanan</h2>

                <div class="border border-stone-200 rounded-xl overflow-hidden mb-6">
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
                            <template x-for="(item, idx) in orderItems" :key="idx">
                                <tr class="border-b border-stone-100 last:border-b-0">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-stone-800" x-text="item.name"></span>
                                        <span x-show="item.variant" class="text-stone-400 text-xs ml-1" x-text="'(' + item.variant + ')'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center" x-text="item.qty"></td>
                                    <td class="px-4 py-3 text-right text-stone-500" x-text="'Rp ' + formatRupiah(item.price)"></td>
                                    <td class="px-4 py-3 text-right font-semibold" x-text="'Rp ' + formatRupiah(item.price * item.qty)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="bg-stone-50 border border-stone-200 rounded-xl p-4 mb-6 text-sm space-y-1.5">
                    <p><span class="text-stone-500">Kirim ke:</span> <b x-text="addrName"></b> (<span x-text="addrPhone"></span>)</p>
                    <p class="text-stone-600" x-text="addrAddress + ', ' + addrCity"></p>
                    <p><span class="text-stone-500">Kurir:</span> <b x-text="shipLabel || '-'"></b> · <span class="text-stone-500">Bayar via:</span> <b x-text="channels[selectedPayment]?.name"></b></p>
                </div>

                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Ongkos Kirim</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(shippingCost)"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="paymentFee > 0">
                        <span class="text-stone-500">Biaya layanan</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(paymentFee)"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="couponDiscount > 0">
                        <span class="text-stone-500">Diskon kupon</span>
                        <span class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(couponDiscount)"></span>
                    </div>
                    <div class="flex justify-between text-base border-t border-stone-200 pt-2 mt-2">
                        <span class="font-bold text-stone-800">Total Pembayaran</span>
                        <span class="font-extrabold text-brand-600 text-xl" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <label class="flex items-start gap-3 text-sm text-stone-600 cursor-pointer">
                    <input type="checkbox" x-model="agreedTerms" class="w-4 h-4 mt-0.5 rounded text-brand-600 focus:ring-brand-500">
                    <span>Saya setuju dengan <a href="/page/syarat-ketentuan" class="text-brand-600 font-semibold hover:underline">Syarat &amp; Ketentuan</a> yang berlaku</span>
                </label>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-stone-200">
                <button type="button" x-show="currentStep > 0" @click="prevStep()"
                    class="px-6 py-3 min-h-[44px] text-sm font-semibold text-stone-600 bg-stone-100 rounded-xl hover:bg-stone-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" x-show="currentStep < 3" @click="nextStep()"
                    class="ml-auto px-8 py-3 min-h-[44px] text-sm font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="submit" x-show="currentStep === 3"
                    class="ml-auto px-10 py-4 min-h-[48px] text-base font-extrabold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                    :disabled="!agreedTerms || placing"
                    :class="(!agreedTerms || placing) ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="placing ? 'Memproses…' : 'Buat Pesanan'"></span>
                </button>
            </div>
            </div>
        </form>
    </main>

    <script>
        function checkoutPage() {
            return {
                steps: ['Alamat', 'Pengiriman', 'Pembayaran', 'Konfirmasi'],
                currentStep: 0,
                placing: false,
                addressType: 'new',
                selectedSavedAddress: 0,

                savedAddresses: @json($addressList ?? []),
                orderItems: @json($items ?? []),
                channels: @json($paymentChannels ?? []),
                weight: {{ (int) ($totalWeight ?? 1000) }},
                warehouse: @json($warehouse ?? []),
                form: { name: '', phone: '', address: '', city: '', postal: '' },
                addrError: '',

                destQuery: '', areas: [], destArea: null,
                courier: '', shipOptions: [], selectedShip: -1,
                loadingShip: false, shipError: '',
                selectedPayment: 0,
                couponCode: '', couponDiscount: 0, couponError: '', couponOk: false, couponLoading: false,
                agreedTerms: false,

                init() {
                    if (this.savedAddresses.length) {
                        this.addressType = 'saved';
                        const def = this.savedAddresses.findIndex(a => a.set_default);
                        this.selectedSavedAddress = def >= 0 ? def : 0;
                        this.syncDestFromAddress();
                    }
                },
                savedAddr() { return this.savedAddresses[this.selectedSavedAddress] || null; },
                get addrName() { return this.addressType === 'saved' ? (this.savedAddr()?.name || '') : this.form.name; },
                get addrPhone() { return this.addressType === 'saved' ? (this.savedAddr()?.phone || '') : this.form.phone; },
                get addrAddress() { return this.addressType === 'saved' ? (this.savedAddr()?.address || '') : this.form.address; },
                get addrCity() { return this.addressType === 'saved' ? (this.savedAddr()?.city || '') : (this.form.city || this.destQuery); },

                syncDestFromAddress() {
                    const a = this.savedAddr();
                    if (a && a.area_id) {
                        this.destArea = { id: a.area_id, name: a.city || a.label };
                        this.destQuery = a.city || '';
                        this.fetchShipping();
                    }
                },

                get subtotal() { return this.orderItems.reduce((s, i) => s + i.price * i.qty, 0); },
                get shipSel() { return this.shipOptions[this.selectedShip] || null; },
                get shippingCost() { return this.shipSel ? this.shipSel.cost : 0; },
                get shipLabel() { return this.shipSel ? (this.shipSel.courier.toUpperCase() + ' ' + this.shipSel.service) : ''; },
                get shipCourier() { return this.shipSel ? this.shipSel.courier : ''; },
                get paymentFee() { return (this.channels[this.selectedPayment]?.fee) || 0; },
                get grandTotal() { return Math.max(0, this.subtotal + this.shippingCost + this.paymentFee - this.couponDiscount); },

                nextStep() {
                    if (this.currentStep === 0) {
                        if (this.addressType === 'new' && (!this.form.name || !this.form.phone || !this.form.address)) {
                            this.addrError = 'Lengkapi nama, telepon, dan alamat dulu.';
                            return;
                        }
                        this.addrError = '';
                    }
                    if (this.currentStep === 1 && !this.shipSel) {
                        this.shipError = this.shipError || 'Pilih salah satu layanan pengiriman.';
                        return;
                    }
                    if (this.currentStep < 3) this.currentStep++;
                },
                prevStep() { if (this.currentStep > 0) this.currentStep--; },

                async lookupArea() {
                    if (this.destQuery.length < 3) { this.areas = []; return; }
                    try {
                        const r = await fetch(`/api/shipping/areas?q=${encodeURIComponent(this.destQuery)}`);
                        const d = await r.json();
                        this.areas = d.data || [];
                    } catch (e) {}
                },
                selectArea(a) { this.destArea = a; this.destQuery = a.name; this.areas = []; this.fetchShipping(); },

                async fetchShipping() {
                    if (!this.destArea?.id) return;
                    const origin = this.warehouse.area_id || this.warehouse.city_id;
                    if (!origin) { this.shipError = 'Kota asal toko belum diatur admin.'; return; }
                    this.loadingShip = true; this.shipError = '';
                    try {
                        const params = new URLSearchParams({
                            origin: origin, destination: this.destArea.id,
                            weight: Math.max(1, this.weight), courier: this.courier,
                            subtotal: this.subtotal,
                        });
                        const r = await fetch('/api/shipping/cost?' + params.toString());
                        const d = await r.json();
                        if (!d.success) throw new Error(d.message || 'Gagal hitung ongkir');
                        this.shipOptions = (d.data || []).flatMap(g => (g.costs || []).map(c => ({ courier: g.courier, ...c }))).slice(0, 8);
                        this.selectedShip = this.shipOptions.length ? 0 : -1;
                        if (!this.shipOptions.length) this.shipError = 'Tidak ada layanan untuk tujuan ini.';
                    } catch (e) { this.shipError = e.message; }
                    this.loadingShip = false;
                },

                async applyCoupon() {
                    this.couponLoading = true; this.couponError = ''; this.couponOk = false;
                    try {
                        const r = await fetch(`{{ route('api.coupon.validate') }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}' },
                            body: JSON.stringify({ code: this.couponCode }),
                        });
                        const d = await r.json();
                        if (!d.success) throw new Error(d.message || 'Kupon tidak valid');
                        this.couponDiscount = d.discount; this.couponOk = true;
                    } catch (e) { this.couponError = e.message; this.couponDiscount = 0; }
                    this.couponLoading = false;
                },

                placeOrder() {
                    if (!this.agreedTerms || this.placing) return;
                    this.placing = true;
                    this.$refs.coForm.submit();
                },
                formatRupiah(n) {
                    return Number(n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            };
        }
    </script>

    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
