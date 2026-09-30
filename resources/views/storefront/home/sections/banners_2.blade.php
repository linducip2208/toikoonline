{{-- Banner Section 2 --}}
@if($settings['home_banner2'] === '1' && $banners2->count())
<section class="py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 reveal">
            @foreach($banners2 as $banner)
            <a href="{{ $banner->link ?: '#' }}"
               class="relative rounded-2xl overflow-hidden h-48 group card-lift">
                @if($banner->photo)
                <img src="{{ asset($banner->photo) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                <div class="w-full h-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                    <span class="text-white font-display text-2xl font-bold">{{ $banner->title }}</span>
                </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-stone-900/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <h3 class="text-white font-display text-xl font-bold">{{ $banner->title }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
