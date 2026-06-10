<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TokoOnline') &mdash; {{ config('app.name', 'TokoOnline') }}</title>
    <meta name="description" content="@yield('meta_description', 'Belanja mudah, harga terbaik. Temukan ribuan produk berkualitas dengan harga bersaing dan pengiriman cepat ke seluruh Indonesia.')">
    <meta property="og:title" content="@yield('title', 'TokoOnline')">
    <meta property="og:description" content="@yield('meta_description', 'Belanja mudah, harga terbaik. Temukan ribuan produk berkualitas.')">
    <meta property="og:image" content="@yield('og_image', asset('marketing/og-default.jpg'))">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'Georgia', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        accent: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                        },
                        warm: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                        },
                    },
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')

    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes fadeSlideUp {
            0% { transform: translateY(40px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes scaleIn {
            0% { transform: scale(.85); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes slideInRight {
            0% { transform: translateX(100%); }
            100% { transform: translateX(0); }
        }
        @keyframes pingSlow {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.8); opacity: 0; }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .animate-float-slow { animation: floatSlow 5s ease-in-out infinite; }
        .animate-float-slow-delayed { animation: floatSlow 5s ease-in-out 1.5s infinite; }
        .animate-float-slow-delayed-2 { animation: floatSlow 5s ease-in-out 3s infinite; }
        .animate-fade-slide-up { animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) forwards; }
        .animate-scale-in { animation: scaleIn .6s cubic-bezier(.16,1,.3,1) forwards; }
        .animate-slide-in-right { animation: slideInRight .35s cubic-bezier(.16,1,.3,1) forwards; }
        .animate-ping-slow { animation: pingSlow 2s ease-out infinite; }
        .animate-shimmer {
            background: linear-gradient(90deg, transparent 25%, rgba(255,255,255,.15) 50%, transparent 75%);
            background-size: 200% 100%;
            animation: shimmer 1.8s ease-in-out infinite;
        }
        .card-lift {
            transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s cubic-bezier(.16,1,.3,1);
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px -12px rgba(0,0,0,.18);
        }
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .7s ease, transform .7s cubic-bezier(.16,1,.3,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .font-display { font-family: 'Playfair Display', Georgia, serif; }
        .backdrop-blur-nav {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 antialiased">

    {{-- Top Navbar --}}
    <header x-data="{ mobileMenu: false, searchOpen: false }"
            class="sticky top-0 z-50 bg-white/80 backdrop-blur-nav border-b border-stone-200/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Top bar --}}
            <div class="flex items-center justify-between h-16 gap-4">
                {{-- Hamburger mobile --}}
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-stone-700 hover:text-brand-600 p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-brand-500 to-accent-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-store text-white text-sm"></i>
                    </div>
                    <span class="font-display font-bold text-xl text-stone-900 hidden sm:block">TokoOnline</span>
                </a>

                {{-- Search bar desktop --}}
                <form action="{{ route('products.index') }}" class="hidden md:flex flex-1 max-w-lg relative">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="search" name="q" placeholder="Cari produk... (contoh: sepatu, tas, baju)"
                               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                    </div>
                </form>

                {{-- Right icons --}}
                <div class="flex items-center gap-1 sm:gap-3">
                    <button @click="searchOpen = !searchOpen" class="md:hidden text-stone-600 hover:text-brand-600 p-2">
                        <i class="fas fa-search text-lg"></i>
                    </button>

                    <a href="#" class="relative text-stone-600 hover:text-brand-600 p-2 transition-colors">
                        <i class="far fa-heart text-xl"></i>
                        <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold
                                     rounded-full flex items-center justify-center leading-none">0</span>
                    </a>

                    <button @click="$store.cart.open = true" class="relative text-stone-600 hover:text-brand-600 p-2 transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span x-text="$store.cart.count"
                              class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 bg-brand-600 text-white text-[10px] font-bold
                                     rounded-full flex items-center justify-center leading-none px-1"
                              x-show="$store.cart.count > 0">0</span>
                    </button>

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-stone-600 hover:text-brand-600 p-2 transition-colors hidden sm:block">
                            <i class="far fa-user text-xl"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-stone-200 py-2 z-50">
                            <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">Masuk</a>
                            <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">Daftar</a>
                            <hr class="my-1 border-stone-100">
                            <a href="#" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">Pesanan Saya</a>
                            <a href="#" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">Pengaturan</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category navbar desktop --}}
            <nav class="hidden lg:flex items-center gap-1 pb-2 overflow-x-auto scrollbar-none">
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Elektronik</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Fashion Pria</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Fashion Wanita</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Rumah Tangga</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Kesehatan</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Olahraga</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Makanan & Minuman</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Otomotif</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Buku & Alat Tulis</a>
                <a href="#" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">Mainan & Hobi</a>
            </nav>

            {{-- Mobile search --}}
            <div x-show="searchOpen" x-cloak class="md:hidden pb-3">
                <form action="{{ route('products.index') }}" class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="search" name="q" placeholder="Cari produk..." autofocus
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                </form>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileMenu" x-cloak
                 class="lg:hidden fixed inset-0 z-40 bg-stone-900/50"
                 @click="mobileMenu = false">
                <div @click.stop class="absolute left-0 top-0 h-full w-80 bg-white shadow-2xl overflow-y-auto">
                    <div class="flex items-center justify-between p-4 border-b border-stone-100">
                        <span class="font-display font-bold text-lg">Kategori</span>
                        <button @click="mobileMenu = false" class="text-stone-400 hover:text-stone-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4 space-y-1">
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-laptop w-5 text-center text-brand-400"></i> Elektronik
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-tshirt w-5 text-center text-brand-400"></i> Fashion Pria
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-female w-5 text-center text-brand-400"></i> Fashion Wanita
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-home w-5 text-center text-brand-400"></i> Rumah Tangga
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-heartbeat w-5 text-center text-brand-400"></i> Kesehatan
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-running w-5 text-center text-brand-400"></i> Olahraga
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-utensils w-5 text-center text-brand-400"></i> Makanan & Minuman
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-car w-5 text-center text-brand-400"></i> Otomotif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Side Cart Drawer --}}
    <div x-data x-show="$store.cart.open" x-cloak
         class="fixed inset-0 z-50"
         x-effect="document.body.style.overflow = $store.cart.open ? 'hidden' : ''">
        <div class="absolute inset-0 bg-stone-900/50" @click="$store.cart.open = false"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl animate-slide-in-right">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between p-4 border-b border-stone-100">
                    <h3 class="font-semibold text-lg">
                        <i class="fas fa-shopping-cart text-brand-600 mr-2"></i>Keranjang Belanja
                    </h3>
                    <button @click="$store.cart.open = false" class="text-stone-400 hover:text-stone-600 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="text-center py-16 text-stone-400">
                        <i class="fas fa-shopping-cart text-5xl mb-4 block"></i>
                        <p class="text-sm">Keranjang belanja Anda kosong</p>
                        <p class="text-xs mt-1">Yuk, mulai belanja sekarang!</p>
                    </div>
                </div>
                <div class="border-t border-stone-100 p-4">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-stone-500">Total (0 item)</span>
                        <span class="font-bold text-lg">Rp 0</span>
                    </div>
                    <button disabled
                            class="w-full py-3 bg-stone-300 text-stone-500 rounded-xl text-sm font-semibold cursor-not-allowed">
                        Keranjang Kosong
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-stone-900 text-stone-300 pt-16 pb-8 mt-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                {{-- Tentang Kami --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-brand-400 to-accent-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-store text-white text-xs"></i>
                        </div>
                        <span class="font-display font-bold text-lg text-white">TokoOnline</span>
                    </div>
                    <p class="text-sm leading-relaxed text-stone-400 mb-4">
                        Platform belanja online terpercaya di Indonesia. Menyediakan jutaan produk berkualitas dari ribuan penjual terbaik dengan pengiriman cepat ke seluruh Indonesia.
                    </p>
                    <div class="flex gap-3">
                        <a href="#" class="w-9 h-9 rounded-lg bg-stone-800 hover:bg-brand-600 flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-stone-800 hover:bg-brand-600 flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-stone-800 hover:bg-brand-600 flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                            <i class="fab fa-tiktok text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-stone-800 hover:bg-brand-600 flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>

                {{-- Bantuan --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">Bantuan</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Cara Berbelanja</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Cara Pembayaran</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Pengiriman</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Pengembalian & Refund</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                {{-- Kategori --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">Kategori</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Elektronik</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Fashion Pria</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Fashion Wanita</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Rumah Tangga</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Kesehatan & Kecantikan</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Olahraga & Outdoor</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Lihat Semua</a></li>
                    </ul>
                </div>

                {{-- Kebijakan --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">Kebijakan</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Kebijakan Cookie</a></li>
                        <li><a href="#" class="text-stone-400 hover:text-white transition-colors">Kebijakan Pengembalian</a></li>
                    </ul>
                    <h4 class="text-white font-semibold text-sm mt-6 mb-3 uppercase tracking-wider">Pembayaran</h4>
                    <div class="flex gap-1.5 flex-wrap">
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">BCA</span>
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">Mandiri</span>
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">BNI</span>
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">GoPay</span>
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">OVO</span>
                        <span class="px-2 py-1 bg-stone-800 rounded text-[10px] text-stone-400">DANA</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-800 pt-8 text-center text-xs text-stone-500">
                <p>&copy; {{ date('Y') }} TokoOnline. Seluruh hak cipta dilindungi.</p>
                <p class="mt-1">Dibangun dengan <span class="text-red-400">&hearts;</span> di Indonesia &middot; Powered by Laravel</p>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp CTA --}}
    <a href="https://wa.me/6281234567890?text=Halo%20TokoOnline%2C%20saya%20butuh%20bantuan"
       target="_blank" rel="noopener"
       class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full
              shadow-lg hover:shadow-xl flex items-center justify-center text-2xl
              transition-all hover:scale-110 card-lift">
        <i class="fab fa-whatsapp"></i>
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full border-2 border-white"></span>
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full animate-ping-slow"></span>
    </a>

    {{-- Alpine.js Cart Store --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: JSON.parse(localStorage.getItem('tokoonline_cart') || '[]'),
                open: false,

                get count() {
                    return this.items.reduce((sum, item) => sum + item.qty, 0);
                },

                get total() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                add(product) {
                    const existing = this.items.find(i => i.id === product.id);
                    if (existing) {
                        existing.qty += product.qty || 1;
                    } else {
                        this.items.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            image: product.image,
                            qty: product.qty || 1
                        });
                    }
                    this.save();
                    this.open = true;
                },

                remove(id) {
                    this.items = this.items.filter(i => i.id !== id);
                    this.save();
                },

                updateQty(id, qty) {
                    const item = this.items.find(i => i.id === id);
                    if (item) {
                        item.qty = Math.max(1, qty);
                        this.save();
                    }
                },

                save() {
                    localStorage.setItem('tokoonline_cart', JSON.stringify(this.items));
                },

                clear() {
                    this.items = [];
                    this.save();
                }
            });
        });
    </script>

    {{-- Scroll reveal --}}
    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>

    @stack('scripts')
</body>
</html>
