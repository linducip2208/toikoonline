@extends('layouts.storefront')

@section('title', ($post->meta_title ?: $post->title) . ' — Blog TokoOnline')
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->short_description ?: $post->content), 160))
@if($post->meta_image)
    @section('og_image', asset($post->meta_image))
@elseif($post->featured_image)
    @section('og_image', asset($post->featured_image))
@endif

@push('styles')
<style>
    .article-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: #1c1917;
    }
    .article-content h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: #292524;
    }
    .article-content p {
        margin-bottom: 1rem;
        line-height: 1.8;
        color: #44403c;
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
        color: #44403c;
    }
    .article-content li {
        margin-bottom: 0.4rem;
        line-height: 1.7;
    }
    .article-content img {
        border-radius: 12px;
        margin: 1.5rem 0;
        max-width: 100%;
        height: auto;
    }
    .article-content blockquote {
        border-left: 4px solid #6366f1;
        padding: 1rem 1.25rem;
        margin: 1.5rem 0;
        background: #eef2ff;
        border-radius: 0 12px 12px 0;
        color: #4338ca;
        font-style: italic;
    }
    .article-content a {
        color: #4f46e5;
        text-decoration: underline;
    }
    .article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    .article-content th, .article-content td {
        border: 1px solid #e7e5e4;
        padding: 0.75rem;
        text-align: left;
    }
    .article-content th {
        background: #fafaf9;
        font-weight: 600;
    }
    .share-btn {
        transition: transform .2s, background .2s;
    }
    .share-btn:hover {
        transform: translateY(-2px);
    }
    .sidebar-sticky {
        position: sticky;
        top: 6rem;
    }
    @media (max-width: 1023px) {
        .sidebar-sticky { position: static; }
    }
</style>
@endpush

@section('content')
{{-- Breadcrumb --}}
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-600 transition-colors">Blog</a>
            @if($post->category)
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-brand-600 transition-colors">{{ $post->category->name }}</a>
            @endif
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-stone-800 font-medium truncate max-w-[200px]">{{ $post->title }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-10">
        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            <article>
                {{-- Header --}}
                <header class="mb-6">
                    @if($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="inline-block px-3 py-1 rounded-lg text-xs font-semibold bg-brand-50 text-brand-700 mb-3 hover:bg-brand-100 transition-colors">
                            {{ $post->category->name }}
                        </a>
                    @endif
                    <h1 class="font-display text-3xl sm:text-4xl font-bold text-stone-900 leading-tight mb-4">
                        {{ $post->title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-stone-500">
                        @if($post->user)
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-user text-brand-500"></i>
                            {{ $post->user->name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-brand-500"></i>
                            {{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-clock text-brand-500"></i>
                            {{ \Illuminate\Support\Str::readingMinutes(strip_tags($post->content)) }} mnt baca
                        </span>
                        @if($post->views)
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-eye text-brand-500"></i>
                            {{ number_format($post->views, 0, ',', '.') }} dilihat
                        </span>
                        @endif
                    </div>
                </header>

                {{-- Featured Image --}}
                @if($post->featured_image)
                    <div class="rounded-2xl overflow-hidden mb-8 aspect-[2/1] bg-stone-100">
                        <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                {{-- Content --}}
                <div class="article-content text-stone-700 leading-relaxed text-[15px]">
                    {!! $post->content !!}
                </div>

                {{-- Share buttons --}}
                <div class="mt-10 pt-6 border-t border-stone-200">
                    <p class="text-sm font-semibold text-stone-800 mb-3">Bagikan artikel ini:</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                           target="_blank" rel="noopener"
                           class="share-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#1877f2] text-white text-sm font-medium hover:bg-[#166fe5]">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                           target="_blank" rel="noopener"
                           class="share-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-black text-white text-sm font-medium hover:bg-stone-800">
                            <i class="fab fa-x-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . url()->current()) }}"
                           target="_blank" rel="noopener"
                           class="share-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#25d366] text-white text-sm font-medium hover:bg-[#22c55e]">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <button onclick="copyLink()"
                                class="share-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-stone-100 text-stone-700 text-sm font-medium hover:bg-stone-200">
                            <i class="fas fa-link"></i> <span id="copyText">Salin Link</span>
                        </button>
                    </div>
                </div>

                {{-- Author bio --}}
                @if($post->user)
                <div class="mt-8 p-6 bg-stone-50 border border-stone-200 rounded-2xl flex gap-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-brand-400 to-accent-400 flex items-center justify-center text-white font-bold text-lg shrink-0">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">{{ $post->user->name }}</p>
                        <p class="text-sm text-stone-500 mt-0.5">Penulis di Blog TokoOnline. Menyajikan tips dan informasi seputar belanja online.</p>
                    </div>
                </div>
                @endif
            </article>

            {{-- Related posts --}}
            @if(isset($relatedPosts) && $relatedPosts->count() > 0)
            <div class="mt-12">
                <h3 class="font-display text-2xl font-bold text-stone-900 mb-6">
                    <i class="fas fa-link text-brand-500 mr-2"></i>Artikel Terkait
                </h3>
                <div class="grid sm:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $rp)
                        <article class="bg-white border border-stone-200 rounded-2xl overflow-hidden card-lift reveal">
                            <a href="{{ route('blog.show', $rp->slug) }}" class="block aspect-[16/9] overflow-hidden bg-stone-100">
                                @if($rp->featured_image)
                                    <img src="{{ asset($rp->featured_image) }}" alt="{{ $rp->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-stone-300">
                                        <i class="fas fa-newspaper text-3xl"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="p-4">
                                <h4 class="font-semibold text-stone-900 mb-1.5 leading-snug">
                                    <a href="{{ route('blog.show', $rp->slug) }}" class="hover:text-brand-600 transition-colors line-clamp-2">
                                        {{ $rp->title }}
                                    </a>
                                </h4>
                                <span class="text-xs text-stone-400">
                                    {{ $rp->published_at?->format('d M Y') ?? $rp->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
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
                        <input type="search" name="q" placeholder="Cari artikel..."
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

{{-- JSON-LD Article Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ \Illuminate\Support\Str::limit(strip_tags($post->short_description ?: $post->content), 160) }}",
    "image": "{{ $post->featured_image ? asset($post->featured_image) : '' }}",
    "datePublished": "{{ $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    @if($post->user)
    "author": {
        "@type": "Person",
        "name": "{{ $post->user->name }}"
    },
    @endif
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name', 'TokoOnline') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('marketing/og-default.jpg') }}"
        }
    }
}
</script>

@push('scripts')
<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        const btn = document.getElementById('copyText');
        btn.textContent = 'Tersalin!';
        setTimeout(() => btn.textContent = 'Salin Link', 2000);
    }
</script>
@endpush
@endsection
