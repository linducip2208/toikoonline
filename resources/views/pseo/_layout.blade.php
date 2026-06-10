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
        body { font-family: 'Inter', system-ui, sans-serif; }
        .font-display { font-family: 'Playfair Display', Georgia, serif; }
        .card-lift {
            transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s cubic-bezier(.16,1,.3,1);
        }
        .card-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -8px rgba(0,0,0,.12);
        }
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
                    <input type="search" name="q" placeholder="Cari produk..."
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
            <p>&copy; {{ date('Y') }} TokoOnline. Seluruh hak cipta dilindungi.</p>
            <p class="text-stone-600 text-xs mt-1">Powered by Laravel</p>
        </div>
    </footer>

</body>
</html>
