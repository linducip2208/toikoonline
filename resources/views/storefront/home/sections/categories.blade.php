{{-- Featured Categories --}}
@if($settings['featured_categories'] === '1' && $featuredCategories->count())
<section class="py-14" id="featured-categories">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">Kategori Populer</h2>
            <a href="{{ route('products.index') }}" class="text-brand-600 text-sm font-semibold hover:underline reveal">Semua Kategori <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($featuredCategories as $cat)
            <a href="{{ route('categories.show', $cat->slug) }}"
               class="bg-white rounded-2xl p-5 border border-stone-100 card-lift reveal group hover:border-brand-200">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-3 group-hover:bg-brand-100 transition-colors">
                    @if($cat->icon)
                    <i class="fas fa-{{ $cat->icon }} text-brand-500 text-lg"></i>
                    @else
                    <i class="fas fa-folder text-brand-500 text-lg"></i>
                    @endif
                </div>
                <h3 class="font-semibold text-sm text-stone-800 mb-1">{{ $cat->name }}</h3>
                <p class="text-[11px] text-stone-400">Lihat semua produk</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
