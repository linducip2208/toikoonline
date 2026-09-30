{{-- Banner Section 1 --}}
@if($settings['home_banner1'] === '1' && $banners1->count())
<section class="py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 reveal">
            @foreach($banners1 as $banner)
            <a href="{{ $banner->link ?: '#' }}"
               class="relative rounded-2xl overflow-hidden h-56 group card-lift">
                @if($banner->photo)
                <img src="{{ asset($banner->photo) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                <div class="w-full h-full bg-gradient-to-br from-brand-500 to-accent-600 flex items-center justify-center">
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
