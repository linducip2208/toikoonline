<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] ?? 'TokoOnline' }} — TokoOnline</title>
    <meta name="description" content="{{ $meta['description'] ?? 'Belanja mudah, harga terbaik. Toko online terpercaya di Indonesia.' }}">
    <meta property="og:title" content="{{ $meta['title'] ?? 'TokoOnline' }}">
    <meta property="og:description" content="{{ $meta['description'] ?? '' }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? asset('marketing/og-default.jpg') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'TokoOnline' }}">
    <meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    @if(isset($jsonld))
    <script type="application/ld+json">{!! json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- CSS lokal (Vite) — token sama persis dengan config inline sebelumnya --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        @keyframes fadeSlideUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        .animate-fade-slide-up { animation: fadeSlideUp .6s cubic-bezier(.16,1,.3,1) forwards; }
    </style>
</head>
<body class="bg-stone-50 text-stone-900 antialiased">

    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-stone-200/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0">
                <div class="w-9 h-9 bg-gradient-to-br from-brand-500 to-accent-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-white text-sm"></i>
                </div>
                <span class="font-display font-bold text-xl text-stone-900">TokoOnline</span>
            </a>
            <form action="{{ url('/products') }}" class="flex-1 max-w-md mx-4 hidden sm:block">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="search" name="q" placeholder="{{ __('storefront.search_mobile_placeholder') }}"
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400
                                  transition-all placeholder:text-stone-400">
                </div>
            </form>
            <div></div>
        </div>
    </header>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-stone-900 text-stone-400 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm">
            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="w-6 h-6 bg-gradient-to-br from-brand-400 to-accent-400 rounded flex items-center justify-center">
                    <i class="fas fa-store text-white text-[10px]"></i>
                </div>
                <span class="font-display font-bold text-white">TokoOnline</span>
            </div>
            <p>&copy; {{ date('Y') }} TokoOnline. {{ __('storefront.copyright') }}</p>
            <p class="text-stone-600 text-xs mt-1">{{ __('pseo.powered_by') }}</p>
        </div>
    </footer>

</body>
</html>
