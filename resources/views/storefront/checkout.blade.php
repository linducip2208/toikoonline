<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — {{ config('app.name', 'TokoOnline') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased" x-data="checkoutPage()">
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="text-2xl font-extrabold text-brand-600 tracking-tight">{{ config('app.name', 'TokoOnline') }}</a>
            <a href="/keranjang" class="text-sm text-stone-600 hover:text-brand-600 transition">← Kembali ke Keranjang</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-extrabold text-stone-900 mb-8">Checkout</h1>

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

        <div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-sm">

            <div x-show="currentStep === 0" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">📍 Alamat Pengiriman</h2>

                <div class="mb-6">
                    <label class="flex items-center gap-3 p-4 border-2 border-brand-200 bg-brand-50/50 rounded-xl cursor-pointer">
                        <input type="radio" name="addressType" value="new" x-model="addressType" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                        <span class="font-semibold text-stone-800">Alamat Baru</span>
                    </label>
                </div>

                <div class="mb-6" x-show="savedAddresses.length > 0">
                    <template x-for="(addr, idx) in savedAddresses" :key="idx">
                        <label class="flex items-start gap-3 p-4 border border-stone-200 rounded-xl mb-3 hover:border-brand-300 transition cursor-pointer"
                            :class="addressType === 'saved' && selectedSavedAddress === idx ? 'border-brand-500 bg-brand-50/30' : ''">
                            <input type="radio" name="addressType" :value="'saved'" x-model="addressType" @change="selectedSavedAddress = idx" class="w-5 h-5 mt-0.5 text-brand-600 focus:ring-brand-500">
                            <div>
                                <p class="font-semibold text-stone-800" x-text="addr.label"></p>
                                <p class="text-sm text-stone-500" x-text="addr.full"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <div x-show="addressType === 'new'" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Penerima</label>
                            <input type="text" x-model="form.name" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nomor Telepon</label>
                            <input type="tel" x-model="form.phone" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Alamat Lengkap</label>
                        <textarea x-model="form.address" rows="3" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kota / Kabupaten</label>
                            <input type="text" x-model="form.city" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kode Pos</label>
                            <input type="text" x-model="form.postal" class="w-full px-4 py-2.5 border border-stone-300 rounded-xl outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 text-sm text-stone-600 mt-2 cursor-pointer">
                        <input type="checkbox" x-model="sameAsBilling" class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500">
                        Alamat tagihan sama dengan alamat pengiriman
                    </label>
                </div>
            </div>

            <div x-show="currentStep === 1" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">🚚 Metode Pengiriman</h2>

                <div class="space-y-4">
                    <template x-for="(method, idx) in shippingMethods" :key="idx">
                        <label class="flex items-center gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md"
                            :class="selectedShipping === idx ? 'border-brand-500 bg-brand-50/30 shadow-sm' : 'border-stone-200'">
                            <input type="radio" name="shipping" :value="idx" x-model="selectedShipping" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-800 text-lg" x-text="method.name"></span>
                                    <span class="font-extrabold text-brand-600" x-text="'Rp ' + formatRupiah(method.cost)"></span>
                                </div>
                                <p class="text-sm text-stone-500 mt-1" x-text="'Estimasi tiba: ' + method.eta"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <div x-show="currentStep === 2" x-cloak>
                <h2 class="text-xl font-bold text-stone-900 mb-6">💳 Metode Pembayaran</h2>

                <div class="space-y-4">
                    <template x-for="(method, idx) in paymentMethods" :key="idx">
                        <label class="flex items-center gap-4 p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md"
                            :class="selectedPayment === idx ? 'border-brand-500 bg-brand-50/30 shadow-sm' : 'border-stone-200'">
                            <input type="radio" name="payment" :value="idx" x-model="selectedPayment" class="w-5 h-5 text-brand-600 focus:ring-brand-500">
                            <div class="w-12 h-8 rounded bg-stone-100 flex items-center justify-center font-bold text-xs text-stone-500 flex-shrink-0" x-text="method.bank"></div>
                            <div class="flex-1">
                                <span class="font-bold text-stone-800" x-text="method.name"></span>
                                <p class="text-xs text-stone-500 mt-0.5" x-text="method.desc"></p>
                            </div>
                            <template x-if="method.fee > 0">
                                <span class="text-xs text-stone-500" x-text="'Biaya: Rp ' + formatRupiah(method.fee)"></span>
                            </template>
                            <template x-if="method.fee === 0">
                                <span class="text-xs text-green-600 font-semibold">Gratis</span>
                            </template>
                        </label>
                    </template>
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

                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Ongkos Kirim</span>
                        <span class="font-semibold" x-text="'Rp ' + formatRupiah(shippingCost)"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="discount > 0">
                        <span class="text-stone-500">Diskon</span>
                        <span class="font-semibold text-green-600" x-text="'-Rp ' + formatRupiah(discount)"></span>
                    </div>
                    <div class="flex justify-between text-base border-t border-stone-200 pt-2 mt-2">
                        <span class="font-bold text-stone-800">Total Pembayaran</span>
                        <span class="font-extrabold text-brand-600 text-xl" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <label class="flex items-start gap-3 text-sm text-stone-600 cursor-pointer">
                    <input type="checkbox" x-model="agreedTerms" class="w-4 h-4 mt-0.5 rounded text-brand-600 focus:ring-brand-500">
                    <span>Saya setuju dengan <a href="/syarat-ketentuan" class="text-brand-600 font-semibold hover:underline">Syarat &amp; Ketentuan</a> yang berlaku</span>
                </label>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-stone-200">
                <button x-show="currentStep > 0" @click="prevStep()"
                    class="px-6 py-3 text-sm font-semibold text-stone-600 bg-stone-100 rounded-xl hover:bg-stone-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button x-show="currentStep < 3" @click="nextStep()"
                    class="ml-auto px-8 py-3 text-sm font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button x-show="currentStep === 3" @click="placeOrder()"
                    class="ml-auto px-10 py-4 text-base font-extrabold text-white bg-gradient-to-r from-brand-600 to-brand-500 rounded-xl hover:from-brand-700 hover:to-brand-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2"
                    :disabled="!agreedTerms"
                    :class="!agreedTerms ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Buat Pesanan
                </button>
            </div>
        </div>
    </main>

    <script>
        function checkoutPage() {
            return {
                steps: ['Alamat', 'Pengiriman', 'Pembayaran', 'Konfirmasi'],
                currentStep: 0,
                addressType: 'new',
                sameAsBilling: true,
                selectedSavedAddress: -1,

                form: { name: '', phone: '', address: '', city: '', postal: '' },
                savedAddresses: [
                    { label: 'Rumah', full: 'Jl. Merdeka No. 123, RT 04/05, Kec. Sukamaju, Jakarta Selatan, 12345' },
                    { label: 'Kantor', full: 'Gedung Graha, Lt. 12, Jl. Sudirman Kav. 21, Jakarta Pusat, 10220' },
                ],

                shippingMethods: [
                    { name: 'Reguler', eta: '3-5 hari kerja', cost: 15000 },
                    { name: 'Express', eta: '1-2 hari kerja', cost: 35000 },
                    { name: 'Instant', eta: '2-4 jam (Jabodetabek)', cost: 65000 },
                ],
                selectedShipping: 0,

                paymentMethods: [
                    { name: 'Transfer Bank BCA', bank: 'BCA', desc: 'Verifikasi otomatis 1-5 menit', fee: 0 },
                    { name: 'Transfer Bank Mandiri', bank: 'MDR', desc: 'Verifikasi otomatis 1-5 menit', fee: 0 },
                    { name: 'COD (Bayar di Tempat)', bank: 'COD', desc: 'Bayar saat barang diterima', fee: 5000 },
                    { name: 'Saldo Wallet', bank: 'WLT', desc: 'Bayar pakai saldo TokoOnline Wallet', fee: 0 },
                ],
                selectedPayment: 0,

                orderItems: [
                    { name: 'Sepatu Running Pro X', variant: 'Hitam, 42', price: 450000, qty: 2 },
                    { name: 'Tas Ransel Urban Explorer', variant: null, price: 275000, qty: 1 },
                    { name: 'Kaos Premium Cotton', variant: 'Navy, L', price: 129000, qty: 3 },
                ],
                discount: 156200,
                agreedTerms: false,

                get subtotal() {
                    return this.orderItems.reduce((s, i) => s + i.price * i.qty, 0);
                },
                get shippingCost() {
                    return this.shippingMethods[this.selectedShipping]?.cost || 0;
                },
                get paymentFee() {
                    return this.paymentMethods[this.selectedPayment]?.fee || 0;
                },
                get grandTotal() {
                    return this.subtotal + this.shippingCost + this.paymentFee - this.discount;
                },

                nextStep() { if (this.currentStep < 3) this.currentStep++; },
                prevStep() { if (this.currentStep > 0) this.currentStep--; },
                placeOrder() {
                    if (!this.agreedTerms) return;
                    window.location.href = '/checkout/sukses?order=ORD-' + Date.now().toString(36).toUpperCase();
                },
                formatRupiah(n) {
                    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            };
        }
    </script>

    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
