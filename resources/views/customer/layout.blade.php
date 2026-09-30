<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Akun Saya') — {{ config('app.name', 'TokoOnline') }}</title>
    {{-- CSS lokal (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <style>
        @media (max-width: 1023px) {
            .sidebar-overlay { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; z-index: 50; transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-overlay.open { transform: translateX(0); }
            .sidebar-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 49; display: none; }
            .sidebar-backdrop.open { display: block; }
        }
        @media (min-width: 1024px) { .sidebar-overlay { transform: translateX(0) !important; } }
        .nav-link { transition: all 0.15s ease; }
        .nav-link:hover { transform: translateX(2px); }
        .nav-link.active { background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: #4f46e5; border-left: 3px solid #6366f1; font-weight: 700; }
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
                        {{ strtoupper(substr(auth()->user()->name ?? 'User', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-stone-900 truncate">{{ auth()->user()->name ?? 'Pelanggan' }}</p>
                        <p class="text-xs text-stone-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('customer.profile') }}" class="inline-block mt-3 text-xs text-brand-600 hover:text-brand-700 font-semibold">Edit Profil →</a>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('customer.dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.dashboard') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('customer.orders') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.orders*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Pesanan Saya
                </a>
                <a href="{{ route('customer.wishlist') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.wishlist*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Wishlist
                </a>
                <a href="{{ route('customer.profile') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm {{ request()->routeIs('customer.profile*') ? 'active' : 'text-stone-600 hover:bg-stone-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
            </nav>

            <div class="p-4 border-t border-stone-200">
                <form method="POST" action="{{ route('logout') }}">
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
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-stone-500 hover:text-stone-700 rounded-lg hover:bg-stone-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        </a>
                        <a href="{{ route('home') }}" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">← Toko</a>
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
