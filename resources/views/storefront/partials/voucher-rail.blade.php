@if(isset($coupons) && $coupons->count())
<section id="coupon-section" class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display text-xl lg:text-2xl font-bold text-stone-900">
                <i class="fas fa-ticket-alt text-brand-500 mr-2"></i>Voucher Untukmu
            </h2>
            <a href="{{ route('coupons.index') }}" class="text-brand-600 text-sm font-semibold hover:underline">Lihat Semua →</a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory scrollbar-none" style="scrollbar-width:none">
            @foreach($coupons->take(8) as $coupon)
            <div class="snap-start shrink-0 w-72 bg-gradient-to-r from-brand-600 to-violet-600 rounded-2xl p-[1px]">
                <div class="bg-white rounded-2xl p-4 flex items-center gap-3 h-full">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-percent text-brand-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-mono font-bold text-brand-700 tracking-wider truncate">{{ $coupon->code }}</p>
                        <p class="text-[11px] text-stone-500 truncate">{{ \Illuminate\Support\Str::limit($coupon->name ?? 'Diskon spesial', 32) }}</p>
                    </div>
                    <form action="{{ route('coupons.claim') }}" method="POST" class="shrink-0">
                        @csrf
                        <input type="hidden" name="code" value="{{ $coupon->code }}">
                        <button type="submit" class="px-4 py-2 min-h-[44px] bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition">Klaim</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
