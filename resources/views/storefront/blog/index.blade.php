@extends('layouts.storefront')

@section('title', 'Blog — TokoOnline')
@section('meta_description', 'Tips belanja, review produk, panduan, dan berita terbaru seputar belanja online. Baca artikel menarik di Blog TokoOnline.')

@push('styles')
<style>
    .blog-card:hover .blog-card-img img {
        transform: scale(1.06);
    }
    .blog-card-img img {
        transition: transform .5s cubic-bezier(.16,1,.3,1);
    }
    .sidebar-sticky {
        position: sticky;
        top: 6rem;
    }
    @media (max-width: 1023px) {
        .sidebar-sticky {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-stone-800 font-medium">Blog</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-10">
        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            <h1 class="font-display text-3xl font-bold text-stone-900 mb-2">
                <i class="fas fa-newspaper text-brand-600 mr-2"></i>Blog TokoOnline
            </h1>
            <p class="text-stone-500 mb-8">Tips belanja, review produk, dan berita terbaru seputar belanja online.</p>

            @if(isset($posts) && $posts->count() > 0)
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach($posts as $post)
                        <article class="blog-card bg-white border border-stone-200 rounded-2xl overflow-hidden card-lift reveal">
                            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-img block aspect-[16/9] overflow-hidden bg-stone-100">
                                @if($post->featured_image)
                                    <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-stone-300">
                                        <i class="fas fa-newspaper text-5xl"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="p-5">
                                @if($post->category)
                                    <a href="{{ route('blog.category', $post->category->slug) }}" class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-brand-50 text-brand-700 mb-3 hover:bg-brand-100 transition-colors">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                                <h2 class="font-semibold text-stone-900 mb-2 leading-snug line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-600 transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <p class="text-sm text-stone-500 leading-relaxed mb-3 line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->short_description ?: $post->content), 100) }}
                                </p>
                                <div class="flex items-center gap-3 text-xs text-stone-400">
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}
                                    </span>
                                    @if($post->user)
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-user"></i>
                                        {{ $post->user->name }}
                                    </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-clock"></i>
                                        {{ \Illuminate\Support\Str::readingMinutes(strip_tags($post->content)) }} mnt baca
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-stone-100 flex items-center justify-center">
                        <i class="fas fa-newspaper text-3xl text-stone-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-stone-700 mb-2">Belum ada artikel</h3>
                    <p class="text-stone-400 text-sm">Kami sedang menyiapkan konten menarik untuk Anda. Kunjungi lagi nanti!</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-80 shrink-0">
            <div class="sidebar-sticky space-y-6">

                {{-- Search --}}
                <div class="bg-white border border-stone-200 rounded-2xl p-5">
                    <h4 class="font-semibold text-stone-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-search text-brand-500"></i>Cari Artikel
                    </h4>
                    <form action="{{ route('blog.index') }}" class="relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                    </form>
                </div>

                {{-- Categories --}}
                @if(isset($categories) && $categories->count() > 0)
                <div class="bg-white border border-stone-200 rounded-2xl p-5">
                    <h4 class="font-semibold text-stone-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-folder text-brand-500"></i>Kategori
                    </h4>
                    <ul class="space-y-1">
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm text-stone-600 hover:bg-brand-50 hover:text-brand-600 transition-colors">
                                    {{ $cat->name }}
                                    <span class="text-xs text-stone-400">({{ $cat->blogs_count ?? 0 }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Recent posts --}}
                @if(isset($recentPosts) && $recentPosts->count() > 0)
                <div class="bg-white border border-stone-200 rounded-2xl p-5">
                    <h4 class="font-semibold text-stone-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-clock text-brand-500"></i>Artikel Terbaru
                    </h4>
                    <div class="space-y-3">
                        @foreach($recentPosts as $rp)
                            <a href="{{ route('blog.show', $rp->slug) }}" class="flex gap-3 group">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-stone-100 shrink-0">
                                    @if($rp->featured_image)
                                        <img src="{{ asset($rp->featured_image) }}" alt="{{ $rp->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-stone-300">
                                            <i class="fas fa-newspaper text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-stone-700 group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $rp->title }}
                                    </p>
                                    <span class="text-xs text-stone-400 mt-1 block">
                                        {{ $rp->published_at?->format('d M Y') ?? $rp->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Newsletter --}}
                <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-5 text-white">
                    <h4 class="font-semibold mb-2 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>Newsletter
                    </h4>
                    <p class="text-sm text-brand-100 mb-4">Dapatkan tips belanja & promo terbaru langsung ke email Anda.</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="Email Anda"
                               class="w-full px-4 py-2.5 rounded-xl border border-brand-500 bg-brand-700/50 text-white text-sm
                                      placeholder:text-brand-300 focus:outline-none focus:ring-2 focus:ring-white/20">
                        <button type="submit"
                                class="w-full py-2.5 bg-white text-brand-700 rounded-xl text-sm font-semibold hover:bg-brand-50 transition-colors">
                            Berlangganan
                        </button>
                    </form>
                </div>

                {{-- CTA Source Code --}}
                <div class="bg-stone-900 rounded-2xl p-5 text-white">
                    <div class="text-3xl mb-3">🛒</div>
                    <h4 class="font-semibold mb-2">Beli Source Code</h4>
                    <p class="text-sm text-stone-400 mb-4">Dapatkan source code lengkap TokoOnline untuk bisnis Anda.</p>
                    <a href="https://wa.me/6281234567890?text=Halo%2C%20saya%20tertarik%20dengan%20source%20code%20TokoOnline"
                       target="_blank" rel="noopener"
                       class="block text-center py-2.5 bg-green-600 hover:bg-green-500 rounded-xl text-sm font-semibold transition-colors">
                        <i class="fab fa-whatsapp mr-1.5"></i>Chat via WhatsApp
                    </a>
                </div>

            </div>
        </aside>
    </div>
</div>
@endsection
