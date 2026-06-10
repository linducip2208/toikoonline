@extends('pseo._layout')

@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'Source Code Aplikasi Toko Online',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web',
    'description' => 'Source code aplikasi toko online / e-commerce siap pakai. Laravel + MySQL. Whitelabel ready.',
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR', 'availability' => 'https://schema.org/InStock'],
];
@endphp

@section('content')
<div class="bg-gradient-to-br from-brand-900 via-brand-800 to-accent-900 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">Source Code</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            Beli Aplikasi Toko Online
        </h1>
        <p class="text-brand-200 text-xl mt-4 max-w-3xl mx-auto leading-relaxed">
            Source code aplikasi toko online / e-commerce siap pakai. Whitelabel — 100% punya kode, bisa ganti nama, logo, warna sesuka hati.
        </p>
    </div>
</div>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="font-display text-3xl font-bold text-stone-900 mb-4">Kenapa Beli Source Code Kami?</h2>
            <p class="text-stone-600 leading-relaxed mb-6">
                Punya toko online sendiri tanpa ribet coding dari nol. Source code kami sudah dipakai oleh puluhan bisnis di Indonesia —
                dari fashion, elektronik, makanan, hingga otomotif. Dapatkan semua fitur lengkap di bawah ini:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-code text-brand-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Laravel + MySQL</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Framework modern, performa kencang, mudah maintenance.</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-paint-brush text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Whitelabel Ready</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Ganti nama, logo, warna, domain sesuka hati. 100% brand sendiri.</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-warm-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-mobile-alt text-warm-500"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Responsive 100%</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Tampilan mobile & desktop perfect. Custom theme premium.</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shield-alt text-red-500"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Full Source Code</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Bukan SaaS bulanan. Punya 100% kode. Modifikasi bebas.</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plug text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">Payment Gateway</h4>
                        <p class="text-xs text-stone-500 mt-0.5">Midtrans, Xendit, COD, transfer bank, e-wallet.</p>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-search text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-stone-800 text-sm">SEO Built-in</h4>
                        <p class="text-xs text-stone-500 mt-0.5">PSEO 1M+ halaman, sitemap auto, JSON-LD schema.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-8 shadow-lg">
            <h3 class="font-display text-2xl font-bold text-stone-900 mb-6 text-center">Fitur Lengkap</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Multi-vendor marketplace</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Manajemen produk + stok + varian</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Keranjang belanja real-time</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Checkout + payment gateway</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Order tracking + notifikasi</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Admin panel Filament premium</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Customer portal / akun</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Wishlist + review + rating</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Blog + PSEO + sitemap</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Laporan penjualan + revenue chart</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Kupon diskon + flash sale</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Ongkir otomatis (RajaOngkir)</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Multi-bahasa + multi-currency</span></div>
                <div class="flex items-center gap-3"><i class="fas fa-check-circle text-green-500"></i><span class="text-sm text-stone-700">Notifikasi WhatsApp + email</span></div>
            </div>
        </div>
    </div>
</section>

<section class="bg-stone-100 py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="font-display text-3xl font-bold text-stone-900 text-center mb-2">Siapa yang Cocok Pakai?</h2>
        <p class="text-stone-500 text-center mb-10">Source code ini cocok untuk berbagai skenario bisnis</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-4">
                    <i class="fas fa-store text-brand-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">Pemilik Bisnis</h3>
                <p class="text-sm text-stone-500 leading-relaxed">Punya toko fisik dan ingin go online? Source code ini jadi fondasi toko online Anda dalam hitungan hari.</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                    <i class="fas fa-laptop-code text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">Developer / Agency</h3>
                <p class="text-sm text-stone-500 leading-relaxed">Jual ke klien dengan brand sendiri. Whitelabel = markup harga sesuka hati. Tidak perlu develop dari nol.</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-6 card-lift">
                <div class="w-12 h-12 rounded-xl bg-warm-50 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-warm-500 text-xl"></i>
                </div>
                <h3 class="font-semibold text-lg text-stone-800 mb-2">Startup / Founder</h3>
                <p class="text-sm text-stone-500 leading-relaxed">MVP siap launch dalam 1 minggu. Validasi ide marketplace tanpa burn cash development mahal.</p>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="bg-gradient-to-br from-brand-700 to-accent-700 text-white rounded-2xl p-10 text-center shadow-xl">
        <p class="text-brand-200 text-sm font-semibold tracking-wider uppercase mb-2">Dapatkan Sekarang</p>
        <h2 class="font-display text-3xl font-extrabold mb-3">Siap Punya Toko Online Sendiri?</h2>
        <p class="text-brand-100 text-lg mb-6 max-w-2xl mx-auto leading-relaxed">
            Hubungi kami via WhatsApp untuk info harga, demo, dan dokumentasi lengkap.
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20source%20code%20aplikasi%20TokoOnline%20%F0%9F%9B%92"
               target="_blank" rel="noopener"
               class="px-8 py-3.5 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 hover:shadow-xl transition-all hover:-translate-y-0.5 text-lg">
                <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp Sekarang
            </a>
        </div>
        <p class="text-brand-200 text-xs mt-5">Respon cepat &lt; 1 jam di jam kerja</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 pb-12">
    <h2 class="font-display text-2xl font-bold text-stone-900 mb-6">Pertanyaan Umum (FAQ)</h2>
    <div x-data="{ openIndex: null }" class="space-y-3">
        @php $faqs = [
            ['Apakah ini termasuk source code lengkap?', 'Ya, Anda mendapatkan 100% source code. Bukan SaaS, bukan sewa bulanan. Punya kode sepenuhnya — modifikasi, jual ulang, sesuka hati.'],
            ['Bisa ganti nama & logo?', 'Tentu. Ini whitelabel — Anda bisa ganti nama aplikasi, logo, warna tema, domain, dan semua tampilan sesuai brand sendiri.'],
            ['Apa saja yang termasuk?', 'Source code lengkap + database migration + dokumentasi setup + admin panel + storefront + customer portal + laporan. Semua fitur yang ada di demo.'],
            ['Payment gateway apa yang support?', 'Midtrans, Xendit, transfer bank manual, COD. Bisa ditambah payment gateway lain karena Anda punya source code penuh.'],
            ['Berapa lama setup sampai live?', 'Dengan bantuan dokumentasi, rata-rata 1-3 hari untuk instalasi & konfigurasi. Kami bantu via WhatsApp jika ada kendala.'],
            ['Ada biaya tambahan?', 'Tidak. One-time purchase. Tidak ada biaya lisensi per bulan, tidak ada hidden cost, tidak ada royalti.'],
        ] @endphp
        @foreach($faqs as $i => $faq)
        <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
            <button @click="openIndex = openIndex === {{ $i }} ? null : {{ $i }}"
                    class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-stone-50 transition-colors">
                <span class="font-semibold text-stone-800">{{ $faq[0] }}</span>
                <i class="fas text-stone-400 transition-transform duration-200"
                   :class="openIndex === {{ $i }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="openIndex === {{ $i }}" x-cloak class="px-6 pb-4 text-stone-600 text-sm leading-relaxed">
                {{ $faq[1] }}
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection
