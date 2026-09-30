<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('storefront.site_tagline')) &mdash; {{ config('app.name', 'TokoOnline') }}</title>
    <meta name="description" content="@yield('meta_description', __('storefront.meta_description'))">
    <meta property="og:title" content="@yield('title', __('storefront.site_tagline'))">
    <meta property="og:description" content="@yield('meta_description', __('storefront.meta_description_short'))">
    <meta property="og:image" content="@yield('og_image', asset('marketing/og-default.jpg'))">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- CSS lokal (Vite): gantikan cdn.tailwindcss.com — token di tailwind.config.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PWA: installable + offline fallback (checkout selalu network) --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#4f46e5">
    <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
    }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-stone-50 text-stone-900 antialiased lg:pb-0" style="padding-bottom: 3.5rem;">

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

                {{-- Search bar desktop with autocomplete --}}
                <div class="hidden md:flex flex-1 max-w-lg relative" x-data="liveSearch()">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm z-10"></i>
                        <input type="search" x-model="query" @input.debounce.300ms="search" @focus="open = results.length > 0"
                               @keydown.escape="open = false" @keydown.arrow-down.prevent="focusNext" @keydown.arrow-up.prevent="focusPrev"
                               @keydown.enter.prevent="selectFocused"
                               placeholder="{{ __('storefront.search_placeholder') }}"
                               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                        <button x-show="query" @click="query = ''; results = []; open = false" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 z-10">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <div x-show="open && results.length > 0" x-cloak @click.outside="open = false"
                         class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-xl border border-stone-200 overflow-hidden z-50 max-h-96 overflow-y-auto">
                        <template x-for="(product, idx) in results" :key="product.id">
                            <a :href="'/products/' + product.slug"
                               :class="{ 'bg-brand-50': focused === idx }"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50 last:border-0 cursor-pointer">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-50 to-accent-50 flex items-center justify-center shrink-0 overflow-hidden">
                                    <img :src="product.thumbnail_url" :alt="product.name" class="w-full h-full object-cover" x-show="product.thumbnail_url">
                                    <i class="fas fa-box text-brand-300" x-show="!product.thumbnail_url"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-stone-800 truncate" x-text="product.name"></p>
                                    <p class="text-xs text-brand-600 font-semibold" x-text="product.price_formatted"></p>
                                </div>
                                <i class="fas fa-arrow-right text-stone-300 text-xs"></i>
                            </a>
                        </template>
                    </div>
                    <div x-show="open && results.length === 0 && query.length >= 2" x-cloak @click.outside="open = false"
                         class="absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-xl border border-stone-200 overflow-hidden z-50">
                        <div class="px-4 py-6 text-center text-stone-400 text-sm">
                            <i class="fas fa-search text-2xl mb-2 block"></i>
                            {{ __('storefront.search_no_result') }} "<span x-text="query" class="font-semibold text-stone-600"></span>"
                        </div>
                    </div>
                </div>

                {{-- Right icons --}}
                <div class="flex items-center gap-1 sm:gap-3">
                    <button @click="searchOpen = !searchOpen" class="md:hidden text-stone-600 hover:text-brand-600 p-2">
                        <i class="fas fa-search text-lg"></i>
                    </button>

                    <a href="{{ auth()->check() ? route('customer.wishlist') : route('login') }}" class="relative text-stone-600 hover:text-brand-600 p-2 transition-colors">
                        <i class="far fa-heart text-xl"></i>
                        <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                              class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"></span>
                    </a>

                    <a href="{{ auth()->check() ? route('compare.index') : route('login') }}" class="relative text-stone-600 hover:text-brand-600 p-2 transition-colors hidden sm:block">
                        <i class="fas fa-balance-scale text-lg"></i>
                        <span x-show="$store.compare.count > 0" x-text="$store.compare.count"
                              class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"></span>
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
                            @auth
                            <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.dashboard') }}</a>
                            <a href="{{ route('customer.orders') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.my_orders') }}</a>
                            <a href="{{ route('customer.wishlist') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.wishlist') }}</a>
                            <hr class="my-1 border-stone-100">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">{{ __('common.logout') }}</button>
                            </form>
                            @else
                            <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.login') }}</a>
                            <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.register') }}</a>
                            <hr class="my-1 border-stone-100">
                            <a href="{{ route('compare.index') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.compare') }}</a>
                            <a href="{{ route('coupons.index') }}" class="block px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 hover:text-brand-600">{{ __('common.coupons') }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category navbar desktop (CMS: menus lokasi header, fallback kategori) --}}
            <nav class="hidden lg:flex items-center gap-1 pb-2 overflow-x-auto scrollbar-none" aria-label="{{ __('storefront.main_nav') }}">
                @if(isset($headerMenus) && $headerMenus->count())
                    @foreach($headerMenus as $m)
                    <a href="{{ $m->url }}" @if($m->open_new_tab) target="_blank" rel="noopener" @endif class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 min-h-[32px] inline-flex items-center rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">@if($m->icon)<i class="fas fa-{{ $m->icon }} mr-1 text-[10px]"></i>@endif{{ $m->label }}</a>
                    @endforeach
                @else
                    @php
                        $navCategories = \App\Models\Category::where('top', true)->orderBy('name')->get();
                    @endphp
                    @foreach($navCategories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="text-xs font-medium text-stone-600 hover:text-brand-600 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap">{{ $cat->name }}</a>
                    @endforeach
                    <a href="{{ route('categories.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors whitespace-nowrap font-semibold">{{ __('common.view_all') }} <i class="fas fa-chevron-right text-[9px] ml-0.5"></i></a>
                @endif
            </nav>

            {{-- Mobile search --}}
            <div x-show="searchOpen" x-cloak class="md:hidden pb-3">
                <form action="{{ route('search') }}" class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="search" name="q" placeholder="{{ __('storefront.search_mobile_placeholder') }}" autofocus
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
                        <span class="font-display font-bold text-lg">{{ __('common.categories') }}</span>
                        <button @click="mobileMenu = false" class="text-stone-400 hover:text-stone-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4 space-y-1">
                        @if(isset($mobileMenus) && $mobileMenus->count())
                            @foreach($mobileMenus as $m)
                            <a href="{{ $m->url }}" @if($m->open_new_tab) target="_blank" rel="noopener" @endif class="flex items-center gap-3 px-3 py-2.5 min-h-[44px] rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                                <i class="fas fa-{{ $m->icon ?: 'chevron-right' }} w-5 text-center text-brand-400"></i> {{ $m->label }}
                            </a>
                            @endforeach
                            <div class="border-t border-stone-100 my-2"></div>
                        @endif
                        @foreach(\App\Models\Category::where('top', true)->orderBy('name')->get() as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-stone-700 hover:bg-brand-50 hover:text-brand-600">
                            <i class="fas fa-{{ $cat->icon ?: 'folder' }} w-5 text-center text-brand-400"></i> {{ $cat->name }}
                        </a>
                        @endforeach
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
                        <i class="fas fa-shopping-cart text-brand-600 mr-2"></i>{{ __('storefront.cart_title') }}
                    </h3>
                    <button @click="$store.cart.open = false" class="text-stone-400 hover:text-stone-600 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="text-center py-16 text-stone-400">
                        <i class="fas fa-shopping-cart text-5xl mb-4 block"></i>
                        <p class="text-sm">{{ __('storefront.cart_empty') }}</p>
                        <p class="text-xs mt-1">{{ __('storefront.cart_empty_hint') }}</p>
                    </div>
                </div>
                <div class="border-t border-stone-100 p-4">
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-stone-500">{{ __('storefront.cart_total_empty') }}</span>
                        <span class="font-bold text-lg">Rp 0</span>
                    </div>
                    <button disabled class="w-full py-3 bg-stone-300 text-stone-500 rounded-xl text-sm font-semibold cursor-not-allowed">{{ __('storefront.cart_empty_btn') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick View Modal --}}
    <div x-data x-show="$store.quickView.open" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
         x-effect="document.body.style.overflow = $store.quickView.open ? 'hidden' : ''">
        <div class="absolute inset-0 bg-stone-900/60" @click="$store.quickView.open = false; $store.quickView.product = null"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl animate-scale-in overflow-hidden">
                <button @click="$store.quickView.open = false; $store.quickView.product = null"
                        class="absolute top-4 right-4 z-10 w-8 h-8 bg-stone-100 hover:bg-stone-200 rounded-full flex items-center justify-center text-stone-500">
                    <i class="fas fa-times text-sm"></i></button>
                <div x-show="$store.quickView.loading" class="p-12 text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-brand-400"></i>
                </div>
                <template x-if="$store.quickView.product && !$store.quickView.loading">
                <div class="grid md:grid-cols-2 gap-0">
                    <div class="bg-gradient-to-br from-brand-50 to-accent-50 flex items-center justify-center p-8 min-h-[300px]">
                        <img :src="$store.quickView.product.thumbnail" :alt="$store.quickView.product.name"
                             class="max-w-full max-h-64 object-contain" x-show="$store.quickView.product.thumbnail">
                        <i class="fas fa-box text-6xl text-brand-200" x-show="!$store.quickView.product.thumbnail"></i>
                    </div>
                    <div class="p-6 lg:p-8 flex flex-col justify-between">
                        <div>
                            <span class="text-xs text-stone-400" x-text="$store.quickView.product.category"></span>
                            <template x-if="$store.quickView.product.brand"><span class="text-xs text-brand-600 font-medium" x-text="' · '+$store.quickView.product.brand"></span></template>
                            <h3 class="font-bold text-lg text-stone-900 mt-1 mb-2" x-text="$store.quickView.product.name"></h3>
                            <div class="flex items-baseline gap-2 mb-3">
                                <span class="text-2xl font-extrabold text-brand-600" x-text="$store.quickView.formatPrice"></span>
                                <template x-if="$store.quickView.product.has_discount">
                                    <span class="text-sm text-stone-400 line-through" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format($store.quickView.product.price)"></span>
                                </template>
                                <template x-if="$store.quickView.product.has_discount">
                                    <span class="text-xs font-bold text-white bg-red-500 px-2 py-0.5 rounded-md" x-text="'-'+$store.quickView.product.discount_percent+'%'"></span>
                                </template>
                            </div>
                            <div class="flex items-center gap-1 mb-3">
                                <template x-for="i in 5">
                                    <i class="fas fa-star text-xs" :class="i <= Math.round($store.quickView.product.rating) ? 'star-gold' : 'text-stone-300'"></i>
                                </template>
                                <span class="text-xs text-stone-400 ml-1" x-text="'| '+$store.quickView.product.num_of_sale+' terjual'"></span>
                            </div>
                            <template x-if="$store.quickView.product.variant_product && $store.quickView.product.stocks.length">
                                <div class="mb-3">
                                    <p class="text-xs font-semibold text-stone-600 mb-1.5">{{ __('storefront.variant') }}:</p>
                                    <div class="flex gap-1.5 flex-wrap">
                                        <template x-for="stock in $store.quickView.product.stocks" :key="stock.id">
                                            <button @click="$store.quickView.selectedVariant = stock"
                                                    :class="$store.quickView.selectedVariant?.id === stock.id ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-stone-200 text-stone-600'"
                                                    class="px-3 py-1.5 border-2 rounded-lg text-xs font-medium" :disabled="stock.qty <= 0"
                                                    x-text="stock.variant"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 mt-4" x-data="{ qvQty: 1 }">
                            <div class="flex items-center border border-stone-200 rounded-lg">
                                <button @click="qvQty = Math.max(1, qvQty - 1)" class="px-3 py-2 text-stone-500 hover:bg-stone-50">−</button>
                                <input type="number" x-model="qvQty" min="1" class="w-12 text-center text-sm font-semibold border-x border-stone-200 py-2 focus:outline-none">
                                <button @click="qvQty++" class="px-3 py-2 text-stone-500 hover:bg-stone-50">+</button>
                            </div>
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" :value="$store.quickView.product.id">
                                <input type="hidden" name="price" :value="$store.quickView.effectivePrice">
                                <input type="hidden" name="quantity" :value="qvQty">
                                <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">
                                    <i class="fas fa-cart-plus mr-1"></i> {{ __('storefront.add_to_cart_short') }}
                                </button>
                            </form>
                        </div>
                        <a :href="'/products/' + $store.quickView.product.slug" class="block text-center text-xs text-brand-600 hover:underline mt-3">{{ __('storefront.view_full_detail') }}</a>
                    </div>
                </div>
                </template>
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
                        {{ __('storefront.footer_about') }}
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

                {{-- Bantuan (CMS: footerPages dari DB) --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">{{ __('storefront.footer_help') }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('docs') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('storefront.help_center') }}</a></li>
                        @if(isset($footerPages) && $footerPages->count())
                            @foreach($footerPages as $fp)
                            <li><a href="{{ route('page.show', $fp->slug) }}" class="text-stone-400 hover:text-white transition-colors">{{ $fp->title }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('page.show', 'tentang-kami') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('storefront.about_us') }}</a></li>
                            <li><a href="{{ route('page.show', 'syarat-ketentuan') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('storefront.terms') }}</a></li>
                            <li><a href="{{ route('page.show', 'kebijakan-privasi') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('storefront.privacy') }}</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Kategori (CMS: footer_shop atau kategori top) --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">{{ __('common.categories') }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        @if(isset($footerShopMenus) && $footerShopMenus->count())
                            @foreach($footerShopMenus as $m)
                            <li><a href="{{ $m->url }}" @if($m->open_new_tab) target="_blank" rel="noopener" @endif class="text-stone-400 hover:text-white transition-colors">{{ $m->label }}</a></li>
                            @endforeach
                        @else
                            @foreach(\App\Models\Category::where('top', true)->orderBy('name')->take(6)->get() as $cat)
                            <li><a href="{{ route('categories.show', $cat->slug) }}" class="text-stone-400 hover:text-white transition-colors">{{ $cat->name }}</a></li>
                            @endforeach
                            <li><a href="{{ route('categories.index') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('common.view_all') }}</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Halaman (CMS: footer_help + tautan toko) --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 uppercase tracking-wider">{{ __('storefront.footer_pages') }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        @if(isset($footerHelpMenus) && $footerHelpMenus->count())
                            @foreach($footerHelpMenus as $m)
                            <li><a href="{{ $m->url }}" @if($m->open_new_tab) target="_blank" rel="noopener" @endif class="text-stone-400 hover:text-white transition-colors">{{ $m->label }}</a></li>
                            @endforeach
                        @endif
                        <li><a href="{{ route('products.index') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('storefront.all_products') }}</a></li>
                        <li><a href="{{ route('brands.index') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('common.brands') }}</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('common.blog') }}</a></li>
                        <li><a href="{{ route('coupons.index') }}" class="text-stone-400 hover:text-white transition-colors">{{ __('common.coupons') }}</a></li>
                    </ul>
                    <h4 class="text-white font-semibold text-sm mt-6 mb-3 uppercase tracking-wider">{{ __('storefront.payment') }}</h4>
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

            {{-- Policy Icons --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 pt-8 border-t border-stone-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center shrink-0">
                        <i class="fas fa-file-contract text-stone-400 text-sm"></i>
                    </div>
                    <div>
                        <a href="{{ route('page.show', 'syarat-ketentuan') }}" class="text-stone-300 hover:text-white text-sm font-semibold transition-colors">{{ __('storefront.terms') }}</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center shrink-0">
                        <i class="fas fa-undo-alt text-stone-400 text-sm"></i>
                    </div>
                    <div>
                        <span class="text-stone-300 text-sm font-semibold">{{ __('storefront.return_guarantee') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center shrink-0">
                        <i class="fas fa-headset text-stone-400 text-sm"></i>
                    </div>
                    <div>
                        <span class="text-stone-300 text-sm font-semibold">{{ __('storefront.support_247') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-stone-800 flex items-center justify-center shrink-0">
                        <i class="fas fa-shield-alt text-stone-400 text-sm"></i>
                    </div>
                    <div>
                        <a href="{{ route('page.show', 'kebijakan-privasi') }}" class="text-stone-300 hover:text-white text-sm font-semibold transition-colors">{{ __('storefront.privacy') }}</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-800 pt-8 text-center text-xs text-stone-500">
                <p>&copy; {{ date('Y') }} TokoOnline. {{ __('storefront.copyright') }}</p>
                <p class="mt-1">{{ __('storefront.built_with') }} <span class="text-red-400">&hearts;</span> {{ __('storefront.in_indonesia') }} &middot; Powered by Laravel</p>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp CTA (konteks per-produk) --}}
    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo TokoOnline, saya tanya stok ' . (isset($product) ? $product->name . ' ' . url()->current() : 'saya butuh bantuan')) }}"
       target="_blank" rel="noopener"
       aria-label="{{ __('storefront.chat_cs') }}"
       class="fixed bottom-24 lg:bottom-6 right-6 z-40 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full
              shadow-lg hover:shadow-xl flex items-center justify-center text-2xl
              transition-all hover:scale-110 card-lift">
        <i class="fab fa-whatsapp"></i>
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full border-2 border-white"></span>
        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full animate-ping-slow"></span>
    </a>

    {{-- Mobile Bottom Nav --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-stone-200 z-40 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
        <div class="flex items-center justify-around h-14 px-2">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 text-brand-600 min-w-0 px-2">
                <i class="fas fa-home text-lg"></i>
                <span class="text-[10px] font-semibold">{{ __('common.home') }}</span>
            </a>
            <a href="{{ route('categories.index') }}" class="flex flex-col items-center gap-0.5 text-stone-400 hover:text-brand-600 min-w-0 px-2">
                <i class="fas fa-th-large text-lg"></i>
                <span class="text-[10px] font-medium">{{ __('common.categories') }}</span>
            </a>
            <button @click="$store.cart.open = true" class="flex flex-col items-center gap-0.5 text-stone-400 hover:text-brand-600 min-w-0 px-2 relative">
                <i class="fas fa-shopping-cart text-lg"></i>
                <span x-text="$store.cart.count" class="absolute -top-1 right-0 min-w-[18px] h-[18px] bg-brand-600 text-white text-[9px] font-bold rounded-full flex items-center justify-center leading-none px-1" x-show="$store.cart.count > 0">0</span>
                <span class="text-[10px] font-medium">{{ __('common.cart') }}</span>
            </button>
            <a href="{{ auth()->check() ? route('customer.wishlist') : route('login') }}" class="flex flex-col items-center gap-0.5 text-stone-400 hover:text-brand-600 min-w-0 px-2">
                <i class="far fa-heart text-lg"></i>
                <span class="text-[10px] font-medium">{{ __('common.wishlist') }}</span>
            </a>
            <a href="{{ auth()->check() ? route('customer.dashboard') : route('login') }}" class="flex flex-col items-center gap-0.5 text-stone-400 hover:text-brand-600 min-w-0 px-2">
                <i class="far fa-user text-lg"></i>
                <span class="text-[10px] font-medium">{{ __('common.account') }}</span>
            </a>
        </div>
    </nav>

    {{-- Alpine.js Cart Store --}}
    <script>
        function liveSearch() {
            return {
                query: '',
                results: [],
                open: false,
                focused: -1,

                async search() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    try {
                        const res = await fetch(`/api/search/suggest?q=${encodeURIComponent(this.query)}`);
                        this.results = await res.json();
                        this.open = this.results.length >= 0;
                        this.focused = -1;
                    } catch (e) { this.results = []; }
                },

                focusNext() {
                    if (this.focused < this.results.length - 1) this.focused++;
                },

                focusPrev() {
                    if (this.focused > 0) this.focused--;
                },

                selectFocused() {
                    if (this.focused >= 0 && this.results[this.focused]) {
                        window.location = '/products/' + this.results[this.focused].slug;
                    } else if (this.query.length >= 2) {
                        window.location = '/search?q=' + encodeURIComponent(this.query);
                    }
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            // Wishlist & Compare stores
            Alpine.store('wishlist', {
                ids: [],
                count: 0,
                async init() {
                    try { const r = await fetch('/api/wishlist/status'); const d = await r.json(); this.ids = d.ids || []; this.count = d.count || 0; } catch(e) {}
                }
            });

            Alpine.store('compare', {
                ids: [],
                count: 0,
                async init() {
                    try { const r = await fetch('/api/compare/status'); const d = await r.json(); this.ids = d.ids || []; this.count = d.count || 0; } catch(e) {}
                }
            });

            // Quick view modal
            Alpine.store('quickView', {
                open: false,
                product: null,
                loading: false,
                selectedVariant: null,
                qty: 1,
                async load(id) {
                    this.loading = true; this.open = true;
                    try { const r = await fetch(`/api/product/${id}/quick-view`); this.product = await r.json(); } catch(e) { this.product = null; }
                    this.loading = false;
                },
                get effectivePrice() {
                    if (!this.product) return 0;
                    if (this.selectedVariant && this.selectedVariant.price) return this.selectedVariant.price;
                    return this.product.effective_price;
                },
                get formatPrice() {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(this.effectivePrice);
                }
            });

            // Global helpers for product cards
            window.wishlisted = (id) => Alpine.store('wishlist').ids.includes(id);
            window.compared = (id) => Alpine.store('compare').ids.includes(id);

            window.toggleWishlist = async (id) => {
                try {
                    const r = await fetch('/api/wishlist/toggle', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                        body: JSON.stringify({product_id: id})
                    });
                    const d = await r.json();
                    if (d.wishlisted) {
                        Alpine.store('wishlist').ids.push(id);
                    } else {
                        Alpine.store('wishlist').ids = Alpine.store('wishlist').ids.filter(i => i !== id);
                    }
                    Alpine.store('wishlist').count = d.count;
                } catch(e) {}
            };

            window.toggleCompare = async (id) => {
                try {
                    const r = await fetch('/api/compare/toggle', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                        body: JSON.stringify({product_id: id})
                    });
                    const d = await r.json();
                    if (d.compared) {
                        Alpine.store('compare').ids.push(id);
                    } else {
                        Alpine.store('compare').ids = Alpine.store('compare').ids.filter(i => i !== id);
                    }
                    Alpine.store('compare').count = d.count;
                } catch(e) {}
            };

            window.openQuickView = (id) => Alpine.store('quickView').load(id);

            Alpine.store('wishlist').init();
            Alpine.store('compare').init();

            // Cart store
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

    @include('storefront.partials.trust-bar')
    @include('storefront.partials.social-proof')

    {{-- Dynamic Popup (dari DB dynamic_popups, fallback WELCOME20) --}}
    @php
        $cmsPopup = $popup ?? null;
        $popupTitle = $cmsPopup?->title ?? 'Diskon 20%!';
        $popupSummary = $cmsPopup?->summary ?? 'Untuk pembelanjaan pertama Anda';
        $popupBtnText = $cmsPopup?->btn_text ?? 'Salin Kode';
        $popupBtnLink = $cmsPopup?->btn_link ?? '';
        $popupBanner = $cmsPopup?->banner ?? null;
    @endphp
    <div x-data="{ show: false, dismissed: sessionStorage.getItem('popup_dismissed') }"
         x-init="if(!dismissed) setTimeout(() => show = true, 8000)"
         x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-effect="document.body.style.overflow = show ? 'hidden' : ''">
        <div class="absolute inset-0 bg-stone-900/60" @click="show = false"></div>
        <div class="relative bg-white rounded-2xl max-w-sm w-full shadow-2xl animate-scale-in overflow-hidden">
            <button @click="show = false; sessionStorage.setItem('popup_dismissed', '1')" class="absolute top-3 right-3 z-10 w-7 h-7 min-w-[28px] min-h-[28px] bg-stone-100 hover:bg-stone-200 rounded-full flex items-center justify-center text-stone-400 text-xs" aria-label="{{ __('common.close') }}">
                <i class="fas fa-times"></i>
            </button>
            @if($popupBanner)
            <img src="{{ asset('storage/'.$popupBanner) }}" alt="{{ $popupTitle }}" class="w-full h-40 object-cover">
            @else
            <div class="bg-gradient-to-r from-brand-600 to-accent-600 p-6 text-white text-center">
                <i class="fas fa-gift text-4xl mb-3 block animate-float-slow"></i>
                <h3 class="font-display text-2xl font-bold mb-1">{{ $popupTitle }}</h3>
                <p class="text-white/80 text-sm">{{ $popupSummary }}</p>
            </div>
            @endif
            @if($popupBanner)
            <div class="p-5 text-center">
                <h3 class="font-display text-xl font-bold mb-1">{{ $popupTitle }}</h3>
                <p class="text-sm text-stone-500 mb-4">{{ $popupSummary }}</p>
            @else
            <div class="p-5 text-center">
            @endif
                <p class="text-sm text-stone-600 mb-4">{{ __('storefront.popup_use_code') }}</p>
                <div class="bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 mb-4">
                    <span class="font-mono font-bold text-brand-600 text-lg tracking-wider">WELCOME20</span>
                </div>
                @if($popupBtnLink)
                <a href="{{ $popupBtnLink }}" class="block w-full py-2.5 min-h-[44px] mb-2 text-center font-semibold text-sm rounded-xl text-white" style="background:{{ $cmsPopup->btn_background_color ?? '#4f46e5' }};color:{{ $cmsPopup->btn_text_color ?? '#fff' }}">{{ $popupBtnText }}</a>
                @endif
                <button @click="navigator.clipboard.writeText('WELCOME20'); $el.innerHTML='<i class=\'fas fa-check mr-1\'></i>Tersalin!'; setTimeout(() => { show = false; sessionStorage.setItem('popup_dismissed', '1') }, 1000)"
                        class="w-full py-2.5 min-h-[44px] bg-gradient-to-r from-brand-500 to-brand-600 text-white font-semibold text-sm rounded-xl hover:shadow-lg transition-all">
                    <i class="fas fa-copy mr-1"></i> {{ __('common.copy_code') }}
                </button>
                <button @click="show = false; sessionStorage.setItem('popup_dismissed', '1')" class="text-xs text-stone-400 hover:text-stone-600 mt-3 min-h-[44px] px-4">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>

    {{-- Last Viewed Tracking --}}
    <script>
        (function() {
            const pid = {{ isset($product) ? $product->id : 'null' }};
            if (pid) {
                let viewed = JSON.parse(localStorage.getItem('tokoonline_viewed') || '[]');
                viewed = viewed.filter(id => id !== pid);
                viewed.unshift(pid);
                viewed = viewed.slice(0, 10);
                localStorage.setItem('tokoonline_viewed', JSON.stringify(viewed));
            }
        })();
    </script>
</body>
</html>
