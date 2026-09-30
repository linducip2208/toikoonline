@if($settings['todays_deal'] === '1' && $todaysDealProducts->count())
<section class="py-14 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-display text-2xl lg:text-3xl font-bold text-stone-900 reveal">
                <i class="fas fa-calendar-check text-blue-500 mr-2"></i>{{ __('storefront.todays_deal') }}
            </h2>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-brand-600 text-sm font-semibold hover:underline reveal">{{ __('common.view_all') }} <i class="fas fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($todaysDealProducts as $product)
            @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
