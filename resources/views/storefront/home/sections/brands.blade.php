{{-- Top Brands --}}
@if($settings['top_brands'] === '1' && $brands->count())
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 mb-8 reveal text-center">Brand Terpercaya</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
            @foreach($brands as $brand)
            <a href="{{ route('brands.show', $brand->slug) }}"
               class="bg-stone-50 rounded-2xl p-4 border border-stone-100 reveal card-lift flex flex-col items-center justify-center gap-2 hover:border-brand-200 h-28">
                @if($brand->logo)
                <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="w-10 h-10 object-contain rounded-full">
                @else
                <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center">
                    <i class="fas fa-tag text-brand-500 text-sm"></i>
                </div>
                @endif
                <span class="text-[11px] font-semibold text-stone-700">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
