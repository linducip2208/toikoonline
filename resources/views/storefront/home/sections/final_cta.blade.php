{{-- Final CTA --}}
<section class="py-20 bg-gradient-to-br from-stone-900 via-stone-900 to-brand-950 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-10 -left-20 w-80 h-80 bg-brand-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 right-10 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-display text-4xl lg:text-5xl font-bold text-white mb-4">Siap Mulai Belanja?</h2>
        <p class="text-stone-400 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
            Jelajahi jutaan produk dari brand terpercaya. Pengiriman cepat, pembayaran aman, dan garansi produk.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('products.index') }}" class="btn-gradient px-8 py-4 rounded-xl text-white font-semibold text-sm shadow-xl shadow-brand-600/30 inline-flex items-center gap-2">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja Sekarang
            </a>
            <a href="{{ route('docs') }}" class="px-8 py-4 rounded-xl border-2 border-white/25 text-white font-semibold text-sm hover:bg-white/10 transition-colors backdrop-blur-sm inline-flex items-center gap-2">
                <i class="fas fa-play-circle"></i> Cara Kerja
            </a>
        </div>
    </div>
</section>
