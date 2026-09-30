{{-- Renderer blok Page Builder CMS versi kita: hero, html, banner_grid, product_grid --}}
@if(!empty($blocks))
    @foreach($blocks as $block)
        @if(($block['type'] ?? '') === 'hero')
            <section class="bg-gradient-to-r from-brand-700 to-violet-700 rounded-2xl p-8 mb-6 text-white overflow-hidden">
                <h2 class="font-display text-2xl font-bold mb-2">{{ $block['data']['heading'] ?? '' }}</h2>
                @if(!empty($block['data']['subheading']))<p class="text-white/80 text-sm mb-4">{{ $block['data']['subheading'] }}</p>@endif
                <div class="flex flex-wrap gap-3 items-center">
                    @if(!empty($block['data']['cta_text']))
                    <a href="{{ $block['data']['cta_url'] ?? '#' }}" class="inline-flex items-center px-5 py-2.5 min-h-[44px] bg-white text-brand-700 text-sm font-bold rounded-xl">{{ $block['data']['cta_text'] }}</a>
                    @endif
                    @if(!empty($block['data']['image']))<img src="{{ asset('storage/'.$block['data']['image']) }}" alt="" class="h-24 rounded-xl object-cover">@endif
                </div>
            </section>
        @elseif(($block['type'] ?? '') === 'html')
            <div class="page-block-html mb-6">{!! $block['data']['html'] ?? '' !!}</div>
        @elseif(($block['type'] ?? '') === 'banner_grid')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach(($block['data']['items'] ?? []) as $item)
                    <a href="{{ $item['link'] ?? '#' }}" class="block rounded-2xl overflow-hidden border border-stone-200">
                        @if(!empty($item['image']))<img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['caption'] ?? '' }}" class="w-full h-44 object-cover" loading="lazy">@endif
                        @if(!empty($item['caption']))<p class="text-sm p-3">{{ $item['caption'] }}</p>@endif
                    </a>
                    @endforeach
                </div>
            </section>
        @elseif(($block['type'] ?? '') === 'product_grid')
            <section class="mb-6">
                <h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] ?? 'Produk Pilihan' }}</h3>
                @php
                    $src = $block['data']['source'] ?? 'featured';
                    $lim = (int)($block['data']['limit'] ?? 8);
                    $q = \App\Models\Product::published()->approved();
                    if ($src === 'best_seller') $q->orderBy('num_of_sale','desc');
                    elseif ($src === 'latest') $q->latest();
                    else $q->where('featured', true)->latest();
                    $gridProducts = $q->take($lim)->get();
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($gridProducts as $product)
                        @include('storefront.partials.product-card')
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endif
