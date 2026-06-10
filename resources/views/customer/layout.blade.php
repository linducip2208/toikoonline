<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Akun Saya') — {{ config('app.name', 'TokoOnline') }}</title>
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
    <style>
        @media (max-width: 1023px) {
            .sidebar-overlay { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; z-index: 50; transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-overlay.open { transform: translateX(0); }
            .sidebar-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 49; display: none; }
            .sidebar-backdrop.open { display: block; }
        }
        @media (min-width: 1024px) {
            .sidebar-overlay { transform: translateX(0) !important; }
        }
        .nav-link { transition: all 0.15s ease; }
        .nav-link:hover { transform: translateX(2px); }
        .nav-link.active { background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #059669; border-left: 3px solid #10b981; font-weight: 700; }
    </style>
</head>
<body class="bg-stone-50 font-sans text-stone-800 antialiased" x-data="customerLayout()">
    <div class="sidebar-backdrop" :class="{ open: sidebarOpen }" @click="sidebarOpen = false"></div>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <aside class="sidebar-overlay bg-white border-r border-stone-200 flex flex-col lg:relative lg:z-auto"
            :class="{ open: sidebarOpen }"
            style="width: 280px; min-height: 100vh; overflow-y: auto;">

            <div class="p-6 border-b border-stone-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-extrabold text-xl flex-shrink-0">
                        {{ strtoupper(substr(auth('customer')->user()->name ?? 'User', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-stone-900 truncate">{{ auth('customer')->user()->name ?? 'Pelanggan' }}</p>
                        <p class="text-xs text-stone-500 truncate">{{ auth('customer')->user()->email ?? 'customer@tokoonline.test' }}</p>
                    </div>
                </div>
                <a href="/customer/profil" class="inline-block mt-3 text-xs text-brand-600 hover:text-brand-700 font-semibold">Edit Profil →</a>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="/customer/dashboard" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.dashboard') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="/customer/pesanan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.orders*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Pesanan Saya
                </a>
                <a href="/customer/wishlist" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.wishlist*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Wishlist
                </a>
                <a href="/customer/ulasan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.reviews*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Ulasan
                </a>
                <a href="/customer/profil" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.profile*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
                <a href="/customer/alamat" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.addresses*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Alamat
                </a>
                <a href="/customer/wallet" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.wallet*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Wallet &amp; Poin
                </a>
                <a href="/customer/tiket" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.tickets*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Tiket Support
                </a>
            </nav>

            <div class="p-4 border-t border-stone-200">
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button class="w-full px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 min-h-screen flex flex-col">
            <header class="bg-white border-b border-stone-200 h-16 flex items-center px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden mr-3 p-2 text-stone-600 hover:bg-stone-100 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex-1 flex items-center justify-between">
                    <span class="font-bold text-stone-900">@yield('page-title', 'Dashboard')</span>
                    <div class="flex items-center gap-4">
                        <a href="/keranjang" class="relative p-2 text-stone-500 hover:text-stone-700 rounded-lg hover:bg-stone-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-brand-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">2</span>
                        </a>
                        <a href="/" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">← Toko</a>
                    </div>
                </div>
            </header>

            <div class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </div>

            <footer class="py-4 text-center text-xs text-stone-400 border-t border-stone-200 bg-white">
                &copy; {{ date('Y') }} {{ config('app.name', 'TokoOnline') }}. Semua hak cipta dilindungi.
            </footer>
        </main>
    </div>

    <script>
        function customerLayout() {
            return { sidebarOpen: false };
        }
    </script>
</body>
</html>
