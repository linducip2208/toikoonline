{{-- Category-wise Products --}}
@if($settings['category_products'] === '1' && $categoryProducts->count())
@foreach($categoryProducts as $cat)
@if($cat->products->count())
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-2xl p-6 lg:p-8 border border-stone-100 reveal">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-xl lg:text-2xl font-bold text-stone-900">{{ $cat->name }}</h2>
                <a href="{{ route('categories.show', $cat->slug) }}" class="text-brand-600 text-sm font-semibold hover:underline">Lihat Semua <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($cat->products as $product)
                <div class="bg-stone-50 rounded-xl overflow-hidden group cursor-pointer card-lift">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="h-32 flex items-center justify-center bg-gradient-to-br from-brand-50 to-accent-50">
                            @if($product->thumbnail_img)
                            <img src="{{ asset($product->thumbnail_img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                            <i class="fas fa-box text-3xl text-brand-200"></i>
                            @endif
                        </div>
                    </a>
                    <div class="p-2.5">
                        <a href="{{ route('products.show', $product->slug) }}" class="text-[11px] font-medium text-stone-700 line-clamp-2 block hover:text-brand-600">{{ $product->name }}</a>
                        <p class="font-bold text-brand-600 text-xs mt-1">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endforeach
@endif
