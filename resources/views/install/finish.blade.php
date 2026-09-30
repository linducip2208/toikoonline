<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('install.finish_title') }} — {{ $appName }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-stone-100 font-sans text-stone-800 antialiased">
<div class="max-w-xl mx-auto px-4 py-16 text-center">
    <div class="text-7xl mb-6" aria-hidden="true">🎉</div>
    <h1 class="text-3xl font-extrabold text-stone-900 mb-3">{{ __('install.title') }} {{ $appName }} {{ __('install.done') }}</h1>
    <p class="text-stone-500 mb-8">{{ __('install.lock_a') }} <code class="bg-stone-200 px-1.5 py-0.5 rounded text-xs">storage/app/installed.lock</code> {{ __('install.lock_b') }}</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="/admin" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition">{{ __('install.open_admin') }}</a>
        <a href="/" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 text-sm font-bold text-stone-600 bg-white border border-stone-300 rounded-xl hover:bg-stone-50 transition">{{ __('install.view_store') }}</a>
    </div>
</div>
</body>
</html>
