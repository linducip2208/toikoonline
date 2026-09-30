{{-- Renderer blok Page Builder CMS: hero, html, banner_grid, product_grid + testimonial, faq, countdown, newsletter, contact_form, gallery, video, pricing --}}
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
        @elseif(($block['type'] ?? '') === 'testimonial')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(($block['data']['items'] ?? []) as $t)
                    <figure class="rounded-2xl border border-stone-200 p-4 bg-white">
                        <div class="text-amber-500 text-sm mb-1">{{ str_repeat('★', max(1, min(5, (int)($t['rating'] ?? 5)))) }}</div>
                        <blockquote class="text-sm text-stone-700 mb-2">"{{ $t['quote'] ?? '' }}"</blockquote>
                        <figcaption class="text-xs font-bold text-stone-500">{{ $t['name'] ?? '' }}</figcaption>
                    </figure>
                    @endforeach
                </div>
            </section>
        @elseif(($block['type'] ?? '') === 'faq')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="divide-y divide-stone-200 rounded-2xl border border-stone-200 bg-white">
                    @foreach(($block['data']['items'] ?? []) as $i => $f)
                    <details class="p-4" @if($i===0) open @endif>
                        <summary class="cursor-pointer font-semibold text-sm min-h-[44px]">{{ $f['question'] ?? '' }}</summary>
                        <p class="text-sm text-stone-600 mt-2">{{ $f['answer'] ?? '' }}</p>
                    </details>
                    @endforeach
                </div>
            </section>
        @elseif(($block['type'] ?? '') === 'countdown')
            <section class="mb-6 rounded-2xl bg-stone-900 text-white p-6 text-center" data-countdown="{{ $block['data']['ends_at'] ?? '' }}">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-2">{{ $block['data']['title'] }}</h3>@endif
                <div class="text-2xl font-mono font-bold countdown-timer">--:--:--</div>
                @if(!empty($block['data']['cta_url']))<a href="{{ $block['data']['cta_url'] }}" class="inline-flex mt-3 px-5 py-2.5 min-h-[44px] bg-white text-stone-900 text-sm font-bold rounded-xl">{{ __('cms.countdown') }}</a>@endif
            </section>
        @elseif(($block['type'] ?? '') === 'newsletter')
            <section class="mb-6 rounded-2xl border border-stone-200 p-6 bg-white text-center">
                <h3 class="font-bold text-lg mb-1">{{ $block['data']['title'] ?? __('cms.newsletter') }}</h3>
                @if(!empty($block['data']['subtitle']))<p class="text-sm text-stone-500 mb-4">{{ $block['data']['subtitle'] }}</p>@endif
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
                    @csrf
                    <input type="email" name="email" required placeholder="email@example.com" class="flex-1 rounded-xl border border-stone-300 px-4 py-2.5 min-h-[44px] text-sm">
                    <button class="px-5 py-2.5 min-h-[44px] bg-brand-600 text-white text-sm font-bold rounded-xl">{{ __('cms.newsletter') }}</button>
                </form>
            </section>
        @elseif(($block['type'] ?? '') === 'contact_form')
            <section class="mb-6 rounded-2xl border border-stone-200 p-6 bg-white">
                <h3 class="font-bold text-lg mb-1">{{ $block['data']['title'] ?? __('cms.contact_form') }}</h3>
                @if(!empty($block['data']['description']))<p class="text-sm text-stone-500 mb-4">{{ $block['data']['description'] }}</p>@endif
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="grid gap-2 max-w-lg">
                    @csrf
                    <input type="email" name="email" required placeholder="email@example.com" class="rounded-xl border border-stone-300 px-4 py-2.5 min-h-[44px] text-sm">
                    <button class="px-5 py-2.5 min-h-[44px] bg-brand-600 text-white text-sm font-bold rounded-xl w-fit">{{ __('cms.contact_form') }}</button>
                </form>
                <p class="text-xs text-stone-400 mt-2">Contact submissions are stored as newsletter subscribers (existing subscribers table).</p>
            </section>
        @elseif(($block['type'] ?? '') === 'gallery')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(($block['data']['images'] ?? []) as $g)
                    <figure class="rounded-2xl overflow-hidden border border-stone-200">
                        @if(!empty($g['image']))<img src="{{ asset('storage/'.$g['image']) }}" alt="{{ $g['caption'] ?? '' }}" class="w-full h-40 object-cover" loading="lazy">@endif
                        @if(!empty($g['caption']))<figcaption class="text-xs p-2">{{ $g['caption'] }}</figcaption>@endif
                    </figure>
                    @endforeach
                </div>
            </section>
        @elseif(($block['type'] ?? '') === 'video')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="aspect-video rounded-2xl overflow-hidden bg-black">
                    <iframe src="{{ $block['data']['url'] ?? '' }}" class="w-full h-full min-h-[280px]" frameborder="0" allowfullscreen loading="lazy"></iframe>
                </div>
                @if(!empty($block['data']['caption']))<p class="text-sm text-stone-500 mt-2">{{ $block['data']['caption'] }}</p>@endif
            </section>
        @elseif(($block['type'] ?? '') === 'pricing')
            <section class="mb-6">
                @if(!empty($block['data']['title']))<h3 class="font-bold text-lg mb-3">{{ $block['data']['title'] }}</h3>@endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(($block['data']['plans'] ?? []) as $plan)
                    <div class="rounded-2xl border border-stone-200 p-5 bg-white">
                        <h4 class="font-bold mb-1">{{ $plan['name'] ?? '' }}</h4>
                        <p class="text-xl font-extrabold mb-3">Rp{{ number_format((int)($plan['price'] ?? 0), 0, ',', '.') }}</p>
                        <ul class="text-sm text-stone-600 mb-4 list-disc pl-5">
                            @foreach(preg_split('/\r?\n/', $plan['features'] ?? '') as $feat)
                                @if(trim($feat) !== '')<li>{{ trim($feat) }}</li>@endif
                            @endforeach
                        </ul>
                        @if(!empty($plan['cta_text']))<a href="{{ $plan['cta_url'] ?? '#' }}" class="inline-flex px-5 py-2.5 min-h-[44px] bg-brand-600 text-white text-sm font-bold rounded-xl">{{ $plan['cta_text'] }}</a>@endif
                    </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endif
<script>
document.querySelectorAll('[data-countdown]').forEach(function (el) {
    var end = new Date(el.getAttribute('data-countdown')).getTime();
    var out = el.querySelector('.countdown-timer');
    if (!end || !out) return;
    function tick() {
        var d = end - Date.now();
        if (d <= 0) { out.textContent = '00:00:00'; return; }
        var h = Math.floor(d / 3600000), m = Math.floor(d % 3600000 / 60000), s = Math.floor(d % 60000 / 1000);
        out.textContent = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }
    tick(); setInterval(tick, 1000);
});
</script>
