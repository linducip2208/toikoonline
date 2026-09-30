@extends('storefront.layouts.app')
@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-4 text-xs uppercase tracking-wide text-amber-600 font-bold">{{ __('cms.preview') }} — {{ $page->title }}</div>
    <h1 class="text-2xl font-bold mb-4">{{ $page->title }}</h1>
    @include('storefront.partials.page-blocks', ['blocks' => $page->blocks])
    @if(empty($page->blocks))
        <div class="prose max-w-none">{!! $page->content !!}</div>
    @endif
</div>
@endsection
