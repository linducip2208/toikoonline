{{-- Best Sellers --}}
@if($settings['best_selling'] === '1' && $bestSellers->count())
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">{{ __('storefront.best_sellers') }}</h2>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-brand-600 text-sm font-semibold hover:underline reveal">{{ __('common.view_all') }} <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($bestSellers as $product)
            @include('storefront.partials.product-card', ['product' => $product, 'showBestBadge' => true])
            @endforeach
        </div>
    </div>
</section>
@endif
