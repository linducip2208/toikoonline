{{-- Category Sidebar Overlay (Desktop) --}}
@if($allCategories->count())
<div x-data="{ catSidebar: false }" class="hidden lg:block">
    <div class="fixed left-0 top-16 bottom-0 z-40 w-[270px] bg-white border-r border-stone-200 overflow-y-auto cat-sidebar shadow-sm"
         x-show="catSidebar" x-cloak x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        <div class="p-4">
            <div class="flex items-center justify-between mb-5">
                <span class="font-display font-bold text-lg text-stone-800">Kategori</span>
                <button @click="catSidebar = false" class="text-stone-400 hover:text-stone-600 p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="space-y-0.5">
                @foreach($allCategories as $cat)
                <a href="{{ route('categories.show', $cat->slug) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-stone-700 hover:bg-brand-50 hover:text-brand-600 transition-colors group">
                    @if($cat->icon)
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center shrink-0 text-brand-500 group-hover:bg-brand-100 transition-colors">
                        <i class="fas fa-{{ $cat->icon }} text-sm"></i>
                    </span>
                    @else
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center shrink-0 text-brand-500 group-hover:bg-brand-100 transition-colors">
                        <i class="fas fa-folder text-sm"></i>
                    </span>
                    @endif
                    <span class="flex-1">{{ $cat->name }}</span>
                    <i class="fas fa-chevron-right text-[9px] text-stone-300 group-hover:text-brand-400 transition-colors"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    <div x-show="catSidebar" x-cloak class="fixed inset-0 bg-stone-900/30 z-30" @click="catSidebar = false" x-transition.opacity></div>
</div>
@endif

{{-- Top Banner Promo Bar --}}
@if($coupons->count())
<div x-data="{ showTopBanner: true }" x-show="showTopBanner" class="bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 text-white text-xs">
    <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none">
            <i class="fas fa-ticket-alt text-brand-300 animate-ping-slow"></i>
            <span class="font-semibold">Kupon Tersedia:</span>
            @foreach($coupons->take(3) as $coupon)
            <span class="bg-white/15 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold">{{ $coupon->code }}</span>
            @endforeach
            <span class="text-brand-200 cursor-pointer hover:underline whitespace-nowrap" onclick="document.getElementById('coupon-section')?.scrollIntoView({behavior:'smooth'})">Klaim Sekarang &raquo;</span>
        </div>
        <button @click="showTopBanner = false" class="text-white/60 hover:text-white ml-2 shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

{{-- Hero Section with Slider --}}
@if($sliders->count())
<section class="relative overflow-hidden">
    <div x-data="{ activeSlide: 0, slides: {{ $sliders->count() }}, autoplay: null }" x-init="autoplay = setInterval(() => activeSlide = (activeSlide + 1) % slides, 5000)">
        @foreach($sliders as $i => $slider)
        <div class="relative h-[420px] lg:h-[500px] hero-gradient flex items-center" x-show="activeSlide === {{ $i }}" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="absolute inset-0">
                @if($slider->photo)
                <img src="{{ asset($slider->photo) }}" alt="{{ $slider->title }}" class="w-full h-full object-cover opacity-40">
                @endif
                <div class="absolute top-20 left-10 text-6xl animate-float-slow opacity-20">ðŸ›ï¸</div>
                <div class="absolute top-40 right-16 text-5xl animate-float-slow-delayed opacity-15">ðŸ“¦</div>
            </div>
            <div class="relative max-w-7xl mx-auto px-4 w-full">
                <div class="max-w-xl reveal">
                    <h1 class="font-display text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-4 hero-headline">{{ $slider->title }}</h1>
                    @if($slider->subtitle)
                    <p class="text-lg text-brand-100/90 mb-8">{{ $slider->subtitle }}</p>
                    @endif
                    <a href="{{ $slider->link ?: route('products.index') }}"
                       class="btn-gradient inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-white font-semibold text-sm shadow-lg shadow-brand-600/30">
                        <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                    </a>
                </div>
            </div>
        </div>
        @endforeach
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            @foreach($sliders as $i => $slider)
            <button @click="activeSlide = {{ $i }}" :class="activeSlide === {{ $i }} ? 'slider-dot active' : 'slider-dot'"></button>
            @endforeach
        </div>
    </div>
</section>
@else
{{-- Static Hero --}}
<section class="hero-gradient relative overflow-hidden pt-16 pb-24 lg:pt-24 lg:pb-32">
    <div class="absolute inset-0">
        <div class="absolute top-20 left-10 text-6xl animate-float-slow opacity-30">ðŸ›ï¸</div>
        <div class="absolute top-40 right-16 text-5xl animate-float-slow-delayed opacity-25">ðŸ“¦</div>
        <div class="absolute bottom-20 left-1/3 text-4xl animate-float-slow-delayed-2 opacity-25">ðŸ·ï¸</div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4">
        <div class="max-w-2xl reveal">
            <h1 class="font-display text-4xl lg:text-6xl font-extrabold text-white leading-tight mb-6 hero-headline">
                Belanja Mudah,<br>Harga Terbaik
            </h1>
            <p class="text-lg text-brand-100/90 leading-relaxed mb-9">
                Temukan ribuan produk berkualitas dengan harga bersaing dan pengiriman cepat ke seluruh Indonesia.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('products.index') }}" class="btn-gradient inline-flex items-center gap-2 px-7 py-3.5 rounded-xl text-white font-semibold text-sm shadow-lg shadow-brand-600/30">
                    <i class="fas fa-shopping-bag"></i> Belanja Sekarang
                </a>
                <a href="#featured-categories"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl border-2 border-white/30 text-white font-semibold text-sm hover:bg-white/10 transition-colors backdrop-blur-sm">
                    <i class="fas fa-th-large"></i> Lihat Kategori
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Category Browse Button (Desktop only - triggers sidebar) --}}
@if($allCategories->count())
<div class="bg-white border-b border-stone-100 hidden lg:block">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <button @click="catSidebar = true" class="inline-flex items-center gap-2 text-sm font-medium text-stone-700 hover:text-brand-600 transition-colors px-4 py-2 rounded-lg hover:bg-brand-50">
            <i class="fas fa-bars"></i>
            <span>Semua Kategori</span>
            <i class="fas fa-chevron-down text-[10px]"></i>
        </button>
    </div>
</div>
@endif

{{-- Trust Strip --}}
<section class="py-6 bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="flex items-center gap-2.5 p-2 reveal"><div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0"><i class="fas fa-shipping-fast text-green-600"></i></div><div><p class="text-xs font-semibold text-stone-800">Pengiriman Cepat</p><p class="text-[10px] text-stone-500">JNE Â· J&T Â· SiCepat Â· GoSend</p></div></div>
            <div class="flex items-center gap-2.5 p-2 reveal"><div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0"><i class="fas fa-qrcode text-blue-600"></i></div><div><p class="text-xs font-semibold text-stone-800">QRIS Â· VA Â· E-wallet</p><p class="text-[10px] text-stone-500">Midtrans Â· Xendit Â· Tripay</p></div></div>
            <div class="flex items-center gap-2.5 p-2 reveal"><div class="w-10 h-10 rounded-xl bg-warm-100 flex items-center justify-center shrink-0"><i class="fas fa-medal text-warm-600"></i></div><div><p class="text-xs font-semibold text-stone-800">Garansi Produk</p><p class="text-[10px] text-stone-500">Retur 7 hari Â· 100% Ori</p></div></div>
            <div class="flex items-center gap-2.5 p-2 reveal"><div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center shrink-0"><i class="fas fa-headset text-purple-600"></i></div><div><p class="text-xs font-semibold text-stone-800">Support 24/7 + COD</p><p class="text-[10px] text-stone-500">WA CS siap membantu</p></div></div>
        </div>
    </div>
</section>
