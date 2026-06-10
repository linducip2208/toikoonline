@extends('layouts.storefront')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[calc(100vh-200px)] grid lg:grid-cols-2 gap-0 -mx-4">
    {{-- Left: Hero Brand Panel --}}
    <div class="hidden lg:flex relative bg-gradient-to-br from-brand-600 via-brand-800 to-stone-900 p-12 flex-col justify-between overflow-hidden">
        {{-- Decorative gradient circles --}}
        <div class="absolute inset-0 opacity-30"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 70%, rgba(255,255,255,0.1) 0%, transparent 50%),
                              radial-gradient(circle at 50% 50%, rgba(99,102,241,0.3) 0%, transparent 70%);">
        </div>

        {{-- Decorative floating circles --}}
        <div class="absolute top-20 left-10 w-64 h-64 rounded-full bg-white/5 animate-float-slow"></div>
        <div class="absolute bottom-32 right-10 w-48 h-48 rounded-full bg-white/5 animate-float-slow-delayed"></div>

        {{-- Large decorative emoji --}}
        <div class="absolute -bottom-20 -right-20 text-[20rem] opacity-10 select-none">🛒</div>

        {{-- Logo + Brand --}}
        <div class="relative">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-store text-white text-lg"></i>
                </div>
                <span class="font-display font-bold text-2xl">TokoOnline</span>
            </a>
        </div>

        {{-- Tagline + Benefit Cards --}}
        <div class="relative text-white">
            <h2 class="font-display text-5xl font-bold leading-tight mb-4">Platform E-Commerce Terlengkap</h2>
            <p class="text-brand-100 text-lg leading-relaxed mb-8 max-w-md">
                Belanja jutaan produk dari ribuan penjual terpercaya. Harga terbaik, pengiriman cepat ke seluruh Indonesia.
            </p>
            <div class="grid grid-cols-3 gap-4 max-w-md">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-tags text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Harga Terbaik</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-shipping-fast text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Kirim Cepat</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-shield-alt text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Aman Terpercaya</span>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="relative text-brand-200/70 text-xs">
            &copy; {{ date('Y') }} TokoOnline &middot; Powered by Laravel
        </div>
    </div>

    {{-- Right: Login Form --}}
    <div class="flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">
            <h1 class="font-display text-4xl font-bold text-stone-900 mb-2">Masuk</h1>
            <p class="text-stone-500 mb-8">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:underline">Daftar gratis</a>
            </p>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <span class="font-semibold">Oops! Ada yang salah</span>
                    </div>
                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               placeholder="nama@email.com" required autofocus
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-stone-700 mb-1.5">Kata Sandi</label>
                    <div class="relative" x-data="{ show: false }">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input :type="show ? 'text' : 'password'" name="password" id="password"
                               placeholder="Minimal 8 karakter" required
                               class="w-full pl-11 pr-12 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-stone-600 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-stone-300 text-brand-600 focus:ring-brand-500">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-brand-600 hover:underline font-medium">
                        Lupa kata sandi?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-500 hover:to-brand-600
                               text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25
                               hover:shadow-brand-500/40 transition-all hover:-translate-y-0.5
                               active:translate-y-0 active:shadow-md">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-8">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">atau</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            {{-- Demo Login Box --}}
            <div class="bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-flask text-brand-500"></i>
                    <span class="font-semibold text-stone-800">Demo Login</span>
                </div>
                <div class="space-y-1.5 text-stone-600 text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-brand-100 text-brand-700 rounded-md font-semibold text-[10px]">Admin</span>
                        <span class="text-stone-500">admin@tokoonline.test / password</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-md font-semibold text-[10px]">Customer</span>
                        <span class="text-stone-500">customer@tokoonline.test / password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
