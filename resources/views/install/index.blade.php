<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('install.title') }} — {{ config('app.name', 'TokoOnline') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-stone-100 font-sans text-stone-800 antialiased">
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-stone-900 mb-2">{{ __('install.title') }} {{ config('app.name', 'TokoOnline') }}</h1>
    <p class="text-stone-500 mb-6">{{ __('install.intro') }}</p>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-sm font-semibold text-green-800" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm font-semibold text-red-800" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800" role="alert">
            <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 mb-6">
        <h2 class="font-bold text-stone-900 mb-4">{{ __('install.step1') }}</h2>
        <ul class="space-y-2 text-sm">
            @foreach($requirements as $r)
                <li class="flex items-start gap-2">
                    <span aria-hidden="true">{{ $r['ok'] ? '✅' : '❌' }}</span>
                    <span class="{{ $r['ok'] ? 'text-stone-700' : 'text-red-700 font-semibold' }}">
                        {{ $r['label'] }}
                        @if(! $r['ok']) — {{ $r['hint'] }} @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-6 mb-6">
        <h2 class="font-bold text-stone-900 mb-4">{{ __('install.step2') }}</h2>
        <form method="POST" action="{{ route('install.database') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label for="db_host" class="block text-sm font-semibold text-stone-700 mb-1.5">DB Host</label>
                <input id="db_host" name="db_host" value="{{ old('db_host', $env['db_host'] ?? '127.0.0.1') }}" required class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="db_port" class="block text-sm font-semibold text-stone-700 mb-1.5">DB Port</label>
                <input id="db_port" name="db_port" value="{{ old('db_port', $env['db_port'] ?? '3306') }}" required inputmode="numeric" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="db_database" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.db_name') }}</label>
                <input id="db_database" name="db_database" value="{{ old('db_database', $env['db_database'] ?? '') }}" required class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="db_username" class="block text-sm font-semibold text-stone-700 mb-1.5">DB Username</label>
                <input id="db_username" name="db_username" value="{{ old('db_username', $env['db_username'] ?? '') }}" required class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div class="sm:col-span-2">
                <label for="db_password" class="block text-sm font-semibold text-stone-700 mb-1.5">DB Password</label>
                <input id="db_password" type="password" name="db_password" autocomplete="new-password" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition">{{ __('install.save_migrate') }}</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-6 mb-6">
        <h2 class="font-bold text-stone-900 mb-4">{{ __('install.step3') }}</h2>
        <form method="POST" action="{{ route('install.admin') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label for="admin_name" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.name') }}</label>
                <input id="admin_name" name="name" value="{{ old('name') }}" required autocomplete="name" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="admin_email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                <input id="admin_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="admin_password" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.password_min') }}</label>
                <input id="admin_password" type="password" name="password" required autocomplete="new-password" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="admin_password2" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.confirm_password') }}</label>
                <input id="admin_password2" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition">{{ __('install.create_admin') }}</button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-6">
        <h2 class="font-bold text-stone-900 mb-4">{{ __('install.step4') }}</h2>
        <form method="POST" action="{{ route('install.store') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label for="app_name" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.store_name') }}</label>
                <input id="app_name" name="app_name" value="{{ old('app_name', $env['app_name'] ?? 'TokoOnline') }}" required class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="app_url" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.app_url') }}</label>
                <input id="app_url" name="app_url" type="url" value="{{ old('app_url', $env['app_url'] ?? 'http://localhost') }}" required class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
            </div>
            <div>
                <label for="currency" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.currency') }}</label>
                <select id="currency" name="currency" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
                    <option value="IDR" selected>IDR — Rupiah</option>
                    <option value="USD">USD — US Dollar</option>
                </select>
            </div>
            <div>
                <label for="timezone" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.timezone') }}</label>
                <select id="timezone" name="timezone" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                </select>
            </div>
            <div>
                <label for="language" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('install.language') }}</label>
                <select id="language" name="language" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm">
                    <option value="id" selected>Indonesia</option>
                    <option value="en">English</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition">{{ __('install.finish_btn') }}</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
