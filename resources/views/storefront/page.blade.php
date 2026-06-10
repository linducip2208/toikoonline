@extends('layouts.storefront')

@section('title', ($page->meta_title ?: $page->title) . ' — TokoOnline')
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 160))

@push('styles')
<style>
    .page-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: #1c1917;
    }
    .page-content h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: #292524;
    }
    .page-content p {
        margin-bottom: 1rem;
        line-height: 1.8;
        color: #44403c;
    }
    .page-content ul, .page-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
        color: #44403c;
    }
    .page-content li {
        margin-bottom: 0.4rem;
        line-height: 1.7;
    }
    .page-content img {
        border-radius: 12px;
        margin: 1.5rem 0;
        max-width: 100%;
        height: auto;
    }
    .page-content blockquote {
        border-left: 4px solid #6366f1;
        padding: 1rem 1.25rem;
        margin: 1.5rem 0;
        background: #eef2ff;
        border-radius: 0 12px 12px 0;
        color: #4338ca;
        font-style: italic;
    }
    .page-content a {
        color: #4f46e5;
        text-decoration: underline;
    }
    .page-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    .page-content th, .page-content td {
        border: 1px solid #e7e5e4;
        padding: 0.75rem;
        text-align: left;
    }
    .page-content th {
        background: #fafaf9;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="bg-white border-b border-stone-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-stone-800 font-medium">{{ $page->title }}</span>
        </nav>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-stone-900 mb-8">{{ $page->title }}</h1>

    <div class="page-content text-stone-700 leading-relaxed text-[15px] bg-white border border-stone-200 rounded-2xl p-8 shadow-sm">
        {!! $page->content !!}
    </div>
</div>
@endsection
