<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('checkout.success_title') }} — {{ config('app.name', 'TokoOnline') }}</title>
    {{-- CSS lokal (Vite). Catatan: brand disatukan ke indigo (dulu emerald khusus halaman ini) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <style>
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeSlideUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        .animate-scale-in { animation: scaleIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-fade-up { animation: fadeSlideUp 0.6s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
    </style>
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased min-h-screen flex flex-col" x-data="successPage()">
    <header class="bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
            <a href="/" class="text-2xl font-extrabold text-brand-600 tracking-tight">{{ config('app.name', 'TokoOnline') }}</a>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">
            <div class="animate-scale-in mb-4">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold text-stone-900 mb-2 animate-fade-up delay-100">{{ __('checkout.success_title') }}!</h1>
            <p class="text-stone-500 mb-8 animate-fade-up delay-200">{{ __('checkout.thank_you', ['app' => config('app.name', 'TokoOnline')]) }}</p>

            <div class="bg-white border border-stone-200 rounded-2xl p-6 mb-6 text-left space-y-4 animate-fade-up delay-300 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-stone-500">{{ __('checkout.order_code') }}</span>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-brand-600 text-lg" x-text="'#' + orderCode"></span>
                        <button @click="copyOrderCode()" class="p-1.5 text-stone-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="{{ __('checkout.copy_code') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-stone-500">{{ __('checkout.grand_total') }}</span>
                    <span class="font-extrabold text-stone-800 text-lg" x-text="'Rp ' + formatRupiah(totalPaid)"></span>
                </div>
                <div x-show="copied" x-transition class="text-xs text-green-600 font-medium text-right" x-cloak>{{ __('checkout.code_copied') }}</div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 text-left animate-fade-up delay-400">
                <h3 class="font-bold text-amber-800 flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('checkout.payment_instruction') }}
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-amber-700">{{ __('checkout.bank') }}</span>
                        <span class="font-semibold text-amber-900" x-text="bankName"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-700">{{ __('checkout.account_no') }}</span>
                        <span class="font-semibold font-mono text-amber-900" x-text="bankAccount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-700">{{ __('checkout.account_name') }}</span>
                        <span class="font-semibold text-amber-900" x-text="bankHolder"></span>
                    </div>
                    <div class="flex justify-between border-t border-amber-200 pt-2 mt-2">
                        <span class="font-bold text-amber-800">{{ __('checkout.transfer_amount') }}</span>
                        <span class="font-extrabold text-amber-900" x-text="'Rp ' + formatRupiah(totalPaid)"></span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center animate-fade-up delay-500">
                @if(!empty($snapToken))
                <button @click="payNow()" class="px-6 py-3.5 bg-gradient-to-r from-brand-600 to-violet-600 text-white font-bold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-qrcode mr-1"></i> {{ __('checkout.pay_now') }}
                </button>
                @endif
                <a href="{{ route('customer.orders') }}" class="px-6 py-3.5 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    {{ __('common.my_orders') }}
                </a>
                <a href="/customer/pesanan/bayar?order=" x-bind:href="'/customer/pesanan/bayar?order=' + orderCode" class="px-6 py-3.5 bg-white border-2 border-brand-200 text-brand-700 font-bold rounded-xl hover:bg-brand-50 hover:border-brand-400 transition-all duration-200">
                    {{ __('checkout.upload_proof') }}
                </a>
                <a href="/" class="px-6 py-3.5 bg-stone-100 text-stone-700 font-semibold rounded-xl hover:bg-stone-200 transition-all duration-200">
                    {{ __('checkout.continue_shopping') }}
                </a>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-stone-400 border-t border-stone-200 bg-white">
        &copy; {{ date('Y') }} {{ config('app.name', 'TokoOnline') }}. {{ __('storefront.copyright') }}
    </footer>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}"></script>
    <script>
        function successPage() {
            return {
                orderCode: @json($order->code),
                totalPaid: {{ (int) $order->grand_total }},
                bankName: '{{ __('checkout.bank_default') }}',
                bankAccount: @json($order->code),
                bankHolder: '{{ __('checkout.bank_holder') }}',
                copied: false,
                snapToken: @json($snapToken ?? null),

                init() {
                    // Auto-buka Snap jika ada token (pembayaran online)
                    if (this.snapToken && window.snap) {
                        setTimeout(() => window.snap.pay(this.snapToken), 800);
                    }
                },
                payNow() {
                    if (this.snapToken && window.snap) window.snap.pay(this.snapToken);
                },

                copyOrderCode() {
                    navigator.clipboard.writeText(this.orderCode).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }).catch(() => {});
                },
                formatRupiah(n) {
                    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            };
        }
    </script>
</body>
</html>
