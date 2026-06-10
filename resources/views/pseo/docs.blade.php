@extends('pseo._layout')

@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Dokumentasi Lengkap TokoOnline — Aplikasi Toko Online',
    'description' => 'Panduan lengkap penggunaan aplikasi TokoOnline. Demo accounts, struktur menu, tutorial langkah demi langkah, dan fitur lengkap.',
];

$demoAccounts = [
    ['role' => 'Super Admin', 'email' => 'admin@tokoonline.test', 'password' => 'password', 'scope' => 'Semua fitur — produk, pesanan, laporan, pengaturan sistem'],
    ['role' => 'Manager', 'email' => 'manager@tokoonline.test', 'password' => 'password', 'scope' => 'Kelola produk, pesanan, kategori. Tanpa akses sistem'],
    ['role' => 'Vendor', 'email' => 'vendor@tokoonline.test', 'password' => 'password', 'scope' => 'Tambah produk, lihat pesanan masuk, tarik komisi'],
    ['role' => 'Customer', 'email' => 'customer@tokoonline.test', 'password' => 'password', 'scope' => 'Belanja, lacak pesanan, wishlist, review'],
];

$menuStructure = [
    ['group' => 'Katalog', 'icon' => 'fa-box', 'items' => [
        ['Produk', 'fa-box-open', 'Tambah, edit, hapus produk. Atur harga, stok, varian, gambar.'],
        ['Kategori', 'fa-tags', 'Kategori produk. Hierarki parent-child, icon, banner.'],
        ['Brand', 'fa-copyright', 'Brand/manufaktur produk. Logo dan meta SEO.'],
        ['Atribut', 'fa-list-check', 'Atribut produk: warna, ukuran, material, dll.'],
    ]],
    ['group' => 'Penjualan', 'icon' => 'fa-cart-shopping', 'items' => [
        ['Pesanan', 'fa-clipboard-list', 'Semua pesanan. Filter status, tanggal, vendor. Update status.'],
        ['Pembayaran', 'fa-credit-card', 'Riwayat pembayaran. Konfirmasi manual, refund.'],
        ['Invoice', 'fa-file-invoice', 'Generate & download invoice PDF per pesanan.'],
    ]],
    ['group' => 'Marketing', 'icon' => 'fa-bullhorn', 'items' => [
        ['Flash Deal', 'fa-bolt', 'Promo flash sale dengan timer countdown.'],
        ['Kupon', 'fa-ticket', 'Kupon diskon: persentase, nominal, free shipping.'],
        ['Blog', 'fa-newspaper', 'Artikel blog untuk SEO. Kategori, publish/draft.'],
        ['Banner', 'fa-image', 'Banner homepage, popup promosi.'],
        ['Newsletter', 'fa-envelope', 'Subscriber list + kirim email blast.'],
    ]],
    ['group' => 'Customer', 'icon' => 'fa-users', 'items' => [
        ['Customer', 'fa-user', 'Data customer, riwayat belanja, status akun.'],
        ['Support Ticket', 'fa-headset', 'Tiket bantuan customer. Reply, status, prioritas.'],
        ['Review Produk', 'fa-star', 'Moderasi review & rating produk.'],
    ]],
    ['group' => 'Keuangan', 'icon' => 'fa-coins', 'items' => [
        ['Transaksi', 'fa-exchange-alt', 'Riwayat transaksi keuangan.'],
        ['Komisi Vendor', 'fa-percent', 'Perhitungan & pencairan komisi vendor.'],
        ['Laporan', 'fa-chart-bar', 'Laporan penjualan, revenue, produk terlaris.'],
    ]],
    ['group' => 'Pengaturan', 'icon' => 'fa-cog', 'items' => [
        ['Pengaturan Bisnis', 'fa-building', 'Nama toko, logo, alamat, kontak, pajak.'],
        ['Pengiriman', 'fa-truck', 'Zona pengiriman, kurir, ongkir.'],
        ['Metode Pembayaran', 'fa-wallet', 'Bank, e-wallet, COD. Manual + gateway.'],
        ['Notifikasi', 'fa-bell', 'Template email, SMS, WhatsApp.'],
        ['Bahasa', 'fa-language', 'Multi-bahasa. Translate string.'],
        ['User & Role', 'fa-user-shield', 'Manajemen user admin/vendor. Role & permission.'],
    ]],
];

$tutorial = [
    ['phase' => 'Fase 1: Setup Awal', 'steps' => [
        ['Login ke Admin Panel', 'Buka [URL]/admin dan login dengan akun Super Admin.'],
        ['Atur Informasi Toko', 'Masuk ke Pengaturan > Pengaturan Bisnis. Isi nama toko, alamat, nomor telepon, email. Upload logo.'],
        ['Atur Pajak & Mata Uang', 'Di Pengaturan Bisnis, atur pajak default (PPN 11%), mata uang (IDR), dan format angka.'],
        ['Atur Zona Pengiriman', 'Di Pengaturan > Pengiriman, tambahkan zona: Dalam Kota, Luar Kota, Luar Pulau. Atur ongkir per zona.'],
        ['Atur Kurir', 'Tambahkan kurir: JNE, J&T, SiCepat, AnterAja. Input tarif per zona. Atau integrasi RajaOngkir.'],
        ['Atur Metode Pembayaran', 'Di Pengaturan > Metode Pembayaran, tambah: BCA, Mandiri, BNI, GoPay, OVO, DANA, COD.'],
    ]],
    ['phase' => 'Fase 2: Input Data Master', 'steps' => [
        ['Tambah Kategori', 'Katalog > Kategori. Tambahkan kategori utama: Elektronik, Fashion Pria, Fashion Wanita, dll. Atur parent-child.'],
        ['Tambah Brand', 'Katalog > Brand. Daftarkan brand produk Anda. Upload logo brand.'],
        ['Tambah Atribut', 'Katalog > Atribut. Buat atribut: Warna (Merah, Biru, Hitam), Ukuran (S, M, L, XL), dll.'],
        ['Tambah Produk', 'Katalog > Produk. Tambah produk baru: nama, kategori, brand, harga, stok, deskripsi, foto.'],
        ['Atur Varian', 'Jika produk punya varian (warna/ukuran), aktifkan "Produk Varian" dan input kombinasi + harga + stok per varian.'],
        ['Upload Gambar Produk', 'Upload multiple gambar per produk. Atur thumbnail utama. Optimasi ukuran gambar untuk loading cepat.'],
    ]],
    ['phase' => 'Fase 3: Marketing & Promo', 'steps' => [
        ['Buat Banner Homepage', 'Marketing > Banner. Upload banner promosi ukuran 1200x400px. Atur link tujuan.'],
        ['Buat Flash Deal', 'Marketing > Flash Deal. Buat promo flash sale: pilih produk, atur harga diskon, tentukan waktu mulai & selesai.'],
        ['Buat Kupon', 'Marketing > Kupon. Buat kupon diskon: persentase atau nominal. Atur minimum belanja, tanggal berlaku, maksimum pemakaian.'],
        ['Tulis Artikel Blog', 'Marketing > Blog. Tulis artikel terkait produk, tips belanja, atau review. Optimasi meta title & description untuk SEO.'],
    ]],
    ['phase' => 'Fase 4: Transaksi Harian', 'steps' => [
        ['Customer Belanja', 'Customer browse produk di storefront, tambah ke keranjang, checkout, pilih alamat & kurir, lakukan pembayaran.'],
        ['Konfirmasi Pembayaran', 'Admin cek Pesanan > Pesanan. Lihat pesanan dengan status "Pending". Cek bukti pembayaran, klik "Konfirmasi".'],
        ['Update Status Pesanan', 'Setelah pembayaran terkonfirmasi, update status: Processing → Shipped → Delivered.'],
        ['Input Resi Pengiriman', 'Saat status "Shipped", input nomor resi dan kurir. Customer bisa track di portal mereka.'],
    ]],
    ['phase' => 'Fase 5: Keuangan', 'steps' => [
        ['Lihat Laporan Penjualan', 'Keuangan > Laporan. Filter per tanggal. Lihat total order, revenue, produk terlaris.'],
        ['Ekspor Laporan', 'Download laporan dalam format CSV/Excel untuk analisis lebih lanjut.'],
        ['Hitung Komisi Vendor', 'Keuangan > Komisi Vendor. Sistem otomatis hitung komisi per vendor berdasarkan persentase yang diatur.'],
    ]],
    ['phase' => 'Fase 6: Komunikasi', 'steps' => [
        ['Atur Notifikasi Otomatis', 'Pengaturan > Notifikasi. Atur template email untuk: order confirmed, shipped, delivered.'],
        ['Balas Support Ticket', 'Customer > Support Ticket. Lihat tiket masuk dari customer, balas, update status.'],
        ['Moderasi Review', 'Customer > Review Produk. Approve/reject review. Balas review customer.'],
    ]],
];

$features = [
    ['group' => 'Katalog & Produk', 'screenshot' => 'screens/products-list.png', 'title' => 'Manajemen Produk Lengkap', 'desc' => 'Tambah, edit, dan kelola ribuan produk dengan mudah. Dukungan penuh untuk produk varian (warna, ukuran), multi-gambar, dan optimasi SEO.', 'bullets' => [
        'Produk varian dengan kombinasi atribut',
        'Multi-gambar per produk + thumbnail',
        'SEO meta title, description, keywords',
        'Harga diskon + tanggal berlaku',
        'Stok tracking & low stock alert',
        'Import/export produk via CSV',
    ]],
    ['group' => 'Penjualan & Pesanan', 'screenshot' => 'screens/orders-list.png', 'title' => 'Kelola Pesanan Mudah', 'desc' => 'Dashboard pesanan lengkap dengan filter status, tanggal, dan vendor. Update status dengan satu klik dan kirim notifikasi ke customer.', 'bullets' => [
        'Filter pesanan: All, Pending, Processing, Shipped',
        'Detail pesanan: item, alamat, ongkir',
        'Input resi + kurir untuk tracking',
        'Invoice PDF auto-generate',
        'Refund & return management',
        'Notifikasi status otomatis',
    ]],
    ['group' => 'Dashboard & Laporan', 'screenshot' => 'screens/reports.png', 'title' => 'Laporan Bisnis Real-Time', 'desc' => 'Pantau performa toko Anda dengan dashboard interaktif. Revenue chart, produk terlaris, dan statistik customer.', 'bullets' => [
        'Revenue chart harian/bulanan',
        'Top 10 produk terlaris',
        'Statistik customer baru',
        'Export laporan Excel/PDF',
    ]],
];

$whatsapp = '6281234567890';
@endphp

@section('content')
<div class="bg-gradient-to-br from-brand-900 via-brand-800 to-accent-900 text-white py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <span class="inline-block text-brand-200 bg-white/10 backdrop-blur rounded-full px-4 py-1.5 text-xs font-semibold tracking-wider uppercase mb-4">Dokumentasi</span>
        <h1 class="font-display text-4xl lg:text-5xl font-extrabold leading-tight">
            Dokumentasi Lengkap TokoOnline
        </h1>
        <p class="text-brand-200 text-lg mt-4 max-w-2xl mx-auto leading-relaxed">
            Panduan lengkap penggunaan aplikasi toko online — dari setup awal hingga operasional harian.
        </p>
    </div>
</div>

<div class="sticky top-16 z-40 bg-white/90 backdrop-blur-lg border-b border-stone-200 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-2 overflow-x-auto flex gap-2 text-xs font-semibold whitespace-nowrap">
        @foreach(['Akun Demo', 'Struktur Menu', 'Tutorial'] as $i => $anchor)
        @php
        $slugs = ['akun-demo', 'struktur-menu', 'tutorial-langkah-demi-langkah'];
        @endphp
        <a href="#{{ $slugs[$i] }}" class="px-4 py-2 rounded-lg text-stone-600 hover:text-brand-600 hover:bg-brand-50 transition-colors">{{ $anchor }}</a>
        @endforeach
        @foreach($features as $f)
        <a href="#fitur-{{ Str::slug($f['title']) }}" class="px-4 py-2 rounded-lg text-stone-600 hover:text-brand-600 hover:bg-brand-50 transition-colors">{{ $f['title'] }}</a>
        @endforeach
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-12 space-y-16">

    <section id="akun-demo">
        <h2 class="font-display text-3xl font-bold text-stone-900 mb-6">Akun Demo</h2>
        <p class="text-stone-500 mb-6">Gunakan akun berikut untuk mencoba semua fitur TokoOnline:</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-stone-200 rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-stone-100">
                        <th class="text-left px-5 py-3 font-semibold text-stone-700">Role</th>
                        <th class="text-left px-5 py-3 font-semibold text-stone-700">Email</th>
                        <th class="text-left px-5 py-3 font-semibold text-stone-700">Password</th>
                        <th class="text-left px-5 py-3 font-semibold text-stone-700">Cakupan Akses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($demoAccounts as $acc)
                    <tr class="hover:bg-stone-50/50">
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-stone-800">{{ $acc['role'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs text-brand-600">{{ $acc['email'] }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs text-stone-500">{{ $acc['password'] }}</td>
                        <td class="px-5 py-3.5 text-xs text-stone-500">{{ $acc['scope'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-center">
            <a href="/admin/login" class="inline-block px-6 py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-brand-500/25 transition-all hover:-translate-y-0.5">
                <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Admin Panel
            </a>
        </div>
    </section>

    <section id="struktur-menu">
        <h2 class="font-display text-3xl font-bold text-stone-900 mb-6">Struktur Menu Admin</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($menuStructure as $group)
            <div class="bg-white rounded-2xl border border-stone-200 p-5 card-lift">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center">
                        <i class="fas {{ $group['icon'] }} text-brand-600"></i>
                    </div>
                    <h3 class="font-semibold text-stone-800">{{ $group['group'] }}</h3>
                </div>
                <ul class="space-y-2.5">
                    @foreach($group['items'] as $item)
                    <li class="flex items-start gap-2.5">
                        <i class="fas {{ $item[1] }} text-brand-400 text-xs mt-0.5 w-4 text-center"></i>
                        <div>
                            <span class="text-sm font-medium text-stone-700">{{ $item[0] }}</span>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $item[2] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </section>

    <section id="tutorial-langkah-demi-langkah">
        <h2 class="font-display text-3xl font-bold text-stone-900 mb-2">Tutorial Langkah Demi Langkah</h2>
        <p class="text-stone-500 mb-8">Ikuti panduan lengkap dari setup awal hingga operasional harian.</p>
        <div class="space-y-10">
            @foreach($tutorial as $phase)
            <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-50 to-accent-50 px-6 py-4 border-b border-stone-100">
                    <h3 class="font-display text-xl font-bold text-stone-900">{{ $phase['phase'] }}</h3>
                </div>
                <div class="divide-y divide-stone-100">
                    @foreach($phase['steps'] as $i => $step)
                    <div class="flex gap-4 px-6 py-4 hover:bg-stone-50/50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-stone-800">{{ $step[0] }}</h4>
                            <p class="text-sm text-stone-500 mt-1">{{ $step[1] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>

    @foreach($features as $i => $feature)
    <section id="fitur-{{ Str::slug($feature['title']) }}">
        <h2 class="font-display text-3xl font-bold text-stone-900 mb-6">{{ $feature['title'] }}</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center {{ $i % 2 === 0 ? '' : 'lg:flex-row-reverse' }}">
            <div class="{{ $i % 2 === 1 ? 'lg:order-2' : '' }}">
                <div class="bg-stone-200 rounded-2xl overflow-hidden border border-stone-300 browser-mock">
                    <div class="bg-stone-300 px-4 py-2 flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        <span class="ml-3 text-[10px] text-stone-500 bg-white/60 px-3 py-0.5 rounded-full flex-1">TokoOnline Admin</span>
                    </div>
                    <div class="p-4 flex items-center justify-center min-h-[280px] text-stone-400">
                        <div class="text-center">
                            <i class="fas fa-desktop text-5xl mb-3 block"></i>
                            <p class="text-sm font-mono">{{ $feature['screenshot'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="{{ $i % 2 === 1 ? 'lg:order-1' : '' }}">
                <span class="inline-block text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1 rounded-full mb-3">
                    {{ $feature['group'] }}
                </span>
                <h3 class="font-display text-2xl font-bold text-stone-900 mb-3">{{ $feature['title'] }}</h3>
                <p class="text-stone-600 leading-relaxed mb-5">{{ $feature['desc'] }}</p>
                <ul class="space-y-2.5">
                    @foreach($feature['bullets'] as $bullet)
                    <li class="flex items-start gap-3 text-sm text-stone-600">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                        {{ $bullet }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    @endforeach

</div>

<section class="bg-gradient-to-r from-brand-700 to-accent-700 text-white py-12 mt-12">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p class="text-brand-200 text-sm font-semibold tracking-wider uppercase mb-2">Mulai Sekarang</p>
        <h2 class="font-display text-3xl font-extrabold mb-3">Siap Jualan Online?</h2>
        <p class="text-brand-100 text-lg mb-6 max-w-2xl mx-auto">
            Akses admin panel sekarang dan mulai kelola toko online Anda dalam hitungan menit.
        </p>
        <a href="/admin/login" class="inline-block px-8 py-3.5 bg-white text-brand-700 font-bold rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5">
            <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Admin Panel
        </a>
    </div>
</section>
@endsection
