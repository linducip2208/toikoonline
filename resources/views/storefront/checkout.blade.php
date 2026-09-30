<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('checkout.title') }} — {{ config('app.name', 'TokoOnline') }}</title>
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
            <a href="{{ route('cart.index') }}" class="text-sm text-stone-600 hover:text-brand-600 transition">← {{ __('checkout.back_to_cart') }}</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-extrabold text-stone-900 mb-8">{{ __('checkout.title') }}</h1>

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
                <h2 class="text-xl font-bold text-stone-900 mb-6">{{ __('checkout.address_title') }}</h2>

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
                        <span class="font-semibold text-stone-800" x-text="savedAddresses.length ? '{{ __('checkout.use_new_address') }}' : '{{ __('checkout.address_title_plain') }}'"></span>
                    </label>
                </div>

                <div x-show="addressType === 'new'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.recipient') }} *</label>
                            <input type="text" x-model="form.name" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.phone') }} *</label>
                            <input type="tel" x-model="form.phone" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.full_address') }} *</label>
                        <textarea x-model="form.address" rows="3" placeholder="{{ __('checkout.address_ph') }}" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.city') }}</label>
                            <input type="text" x-model="form.city" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.postal') }}</label>
                            <input type="text" x-model="form.postal" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <p x-show="addrError" x-text="addrError" class="text-xs text-red-600"></p>
                </div>
            </div>

            <div x-show="currentStep === 1" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-2">{{ __('checkout.shipping_title') }}</h2>
                <p class="text-sm text-stone-500 mb-6">{{ __('checkout.shipping_live') }} <span x-text="(weight/1000).toFixed(1)"></span> {{ __('checkout.kg_unit') }}).</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('checkout.destination') }} *</label>
                        <input type="text" x-model="destQuery" @input.debounce.500ms="lookupArea()" placeholder="{{ __('checkout.dest_ph') }}" autocomplete="off"
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
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('storefront.courier') }}</label>
                        <select x-model="courier" @change="fetchShipping()" class="w-full px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl bg-white outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                            <option value="">{{ __('storefront.all_couriers') }}</option>
                            <option value="jne">JNE</option>
                            <option value="jnt">J&T</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="anteraja">AnterAja</option>
                            <option value="gosend">GoSend</option>
                        </select>
                    </div>
                </div>

                <div x-show="!shipOptions.length" class="border border-dashed border-stone-300 rounded-xl p-6 text-center text-sm text-stone-500">
                    <span x-show="!loadingShip">{{ __('checkout.ship_hint_a') }} <b>{{ __('checkout.check_shipping_btn') }}</b> {{ __('checkout.ship_hint_b') }}</span>
                    <span x-show="loadingShip">{{ __('storefront.calculating') }}</span>
                </div>
                <button type="button" @click="fetchShipping()" :disabled="loadingShip" class="mb-4 px-6 py-2.5 min-h-[44px] bg-stone-900 text-white text-sm font-semibold rounded-xl disabled:opacity-50">{{ __('checkout.check_shipping_btn') }}</button>
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
                <h2 class="text-xl font-bold text-stone-900 mb-6">{{ __('checkout.payment_title') }}</h2>

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
                                <span class="text-xs text-stone-500" x-text="'{{ __('checkout.fee_label') }} ' + formatRupiah(method.fee)"></span>
                            </template>
                            <template x-if="!(method.fee > 0)">
                                <span class="text-xs text-green-600 font-semibold">{{ __('checkout.free') }}</span>
                            </template>
                        </label>
                    </template>
                </div>

                <div class="border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-semibold text-stone-700 mb-2">{{ __('checkout.have_coupon') }}</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="{{ __('checkout.coupon_ph') }}" class="flex-1 px-4 py-2.5 min-h-[44px] border border-stone-300 rounded-xl uppercase font-mono outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
                        <button type="button" @click="applyCoupon()" :disabled="couponLoading || !couponCode" class="px-5 min-h-[44px] bg-brand-600 text-white text-sm font-bold rounded-xl disabled:opacity-50">{{ __('checkout.apply_coupon') }}</button>
                    </div>
                    <p x-show="couponOk" class="text-xs text-green-700 font-semibold mt-2">{{ __('checkout.coupon') }} <span x-text="couponCode"></span> {{ __('checkout.coupon_active') }} <span x-text="formatRupiah(couponDiscount)"></span></p>
                    <p x-show="couponError" x-text="couponError" class="text-xs text-red-600 mt-2"></p>
                </div>
            </div>

            <div x-show="currentStep === 3" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">{{ __('checkout.confirm_title') }}</h2>

                <div class="border border-stone-200 rounded-xl overflow-hidden mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-stone-50 border-b border-stone-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-stone-600">{{ __('checkout.col_product') }}</th>
                                <th class="text-center px-4 py-3 font-semibold text-stone-600">{{ __('checkout.col_qty') }}</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600">{{ __('checkout.col_price') }}</th>
                                <th class="text-right px-4 py-3 font-semibold text-stone-600">{{ __('checkout.col_subtotal') }}</th>
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
                    <p><span class="text-stone-500">{{ __('checkout.send_to') }}:</span> <b x-text="addrName"></b> (<span x-text="addrPhone"></span>)</p>
                    <p class="text-stone-600" x-text="addrAddress + ', ' + addrCity"></p>
                    <p><span class="text-stone-500">{{ __('checkout.courier_label') }}:</span> <b x-text="shipLabel || '{{ __('checkout.dash') }}'"></b> · <span class="text-stone-500">{{ __('checkout.pay_via') }}:</span> <b x-text="channels[selectedPayment]?.name"></b></p>
                </div>

                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">{{ __('checkout.col_subtotal') }}</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">{{ __('checkout.shipping_cost') }}</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(shippingCost)"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="paymentFee > 0">
                        <span class="text-stone-500">{{ __('checkout.service_fee') }}</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(paymentFee)"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="couponDiscount > 0">
                        <span class="text-stone-500">{{ __('checkout.coupon_discount') }}</span>
                        <span class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(couponDiscount)"></span>
                    </div>
                    <div class="flex justify-between text-base border-t border-stone-200 pt-2 mt-2">
                        <span class="font-bold text-stone-800">{{ __('checkout.grand_total') }}</span>
                        <span class="font-extrabold text-brand-600 text-xl" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <label class="flex items-start gap-3 text-sm text-stone-600 cursor-pointer">
                    <input type="checkbox" x-model="agreedTerms" class="w-4 h-4 mt-0.5 rounded text-brand-600 focus:ring-brand-500">
                    <span>{{ __('checkout.agree_a') }} <a href="/page/syarat-ketentuan" class="text-brand-600 font-semibold hover:underline">{{ __('storefront.terms') }}</a> {{ __('checkout.agree_b') }}</span>
                </label>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-stone-200">
                <button type="button" x-show="currentStep > 0" @click="prevStep()"
                    class="px-6 py-3 min-h-[44px] text-sm font-semibold text-stone-600 bg-stone-100 rounded-xl hover:bg-stone-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ __('checkout.back') }}
                </button>
                <button type="button" x-show="currentStep < 3" @click="nextStep()"
                    class="ml-auto px-8 py-3 min-h-[44px] text-sm font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                    {{ __('checkout.next') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="submit" x-show="currentStep === 3"
                    class="ml-auto px-10 py-4 min-h-[48px] text-base font-extrabold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                    :disabled="!agreedTerms || placing"
                    :class="(!agreedTerms || placing) ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="placing ? '{{ __('checkout.processing') }}' : '{{ __('checkout.place_order') }}'"></span>
                </button>
            </div>
            </div>
        </form>
    </main>

    <script>
        function checkoutPage() {
            return {
                steps: ['{{ __('checkout.step_address') }}', '{{ __('checkout.step_shipping') }}', '{{ __('checkout.step_payment') }}', '{{ __('checkout.step_confirm') }}'],
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
                            this.addrError = '{{ __('checkout.err_address') }}';
                            return;
                        }
                        this.addrError = '';
                    }
                    if (this.currentStep === 1 && !this.shipSel) {
                        this.shipError = this.shipError || '{{ __('checkout.err_ship') }}';
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
                    if (!origin) { this.shipError = '{{ __('checkout.err_origin') }}'; return; }
                    this.loadingShip = true; this.shipError = '';
                    try {
                        const params = new URLSearchParams({
                            origin: origin, destination: this.destArea.id,
                            weight: Math.max(1, this.weight), courier: this.courier,
                            subtotal: this.subtotal,
                        });
                        const r = await fetch('/api/shipping/cost?' + params.toString());
                        const d = await r.json();
                        if (!d.success) throw new Error(d.message || '{{ __('checkout.err_calc') }}');
                        this.shipOptions = (d.data || []).flatMap(g => (g.costs || []).map(c => ({ courier: g.courier, ...c }))).slice(0, 8);
                        this.selectedShip = this.shipOptions.length ? 0 : -1;
                        if (!this.shipOptions.length) this.shipError = '{{ __('checkout.err_no_service') }}';
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
                        if (!d.success) throw new Error(d.message || '{{ __('checkout.err_coupon') }}');
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
