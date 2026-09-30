@if($settings['coupon_system'] === '1' && $coupons->count())
<section id="coupon-section" class="py-14 bg-gradient-to-r from-brand-600 via-brand-700 to-accent-700 relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4">
        <div class="text-center mb-10 reveal">
            <h2 class="font-display text-3xl lg:text-4xl font-bold text-white mb-3">
                <i class="fas fa-ticket-alt mr-2"></i>Kupon Diskon
            </h2>
            <p class="text-brand-100/80 text-lg max-w-xl mx-auto">Dapatkan potongan harga spesial dengan kupon eksklusif kami.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($coupons as $coupon)
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 text-white reveal card-lift">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-3xl font-extrabold font-mono tracking-tight">
                        @if($coupon->discount_type === 'percent')
                        {{ round($coupon->discount) }}%
                        @else
                        Rp{{ number_format($coupon->discount, 0, ',', '.') }}
                        @endif
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider bg-white/20 px-2.5 py-1 rounded-full">OFF</span>
                </div>
                <div class="mb-3">
                    <h4 class="font-semibold text-sm mb-1">Kode: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded text-xs">{{ $coupon->code }}</span></h4>
                    @if($coupon->min_buy > 0)
                    <p class="text-[11px] text-brand-100/70">Min. belanja Rp{{ number_format($coupon->min_buy, 0, ',', '.') }}</p>
                    @endif
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $coupon->code }}');this.innerHTML='<i class=\'fas fa-check mr-1\'></i>Tersalin!'"
                        class="w-full py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-xl transition-colors border border-white/30">
                    <i class="fas fa-copy mr-1"></i> Salin Kode
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
