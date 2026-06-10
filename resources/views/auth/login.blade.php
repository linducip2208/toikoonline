@extends('layouts.storefront')

@section('title', 'Masuk')

@push('styles')
<style>
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-16px); }
    }
    @keyframes floatSlowDelayed {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(3deg); }
    }
    @keyframes floatPulse {
        0%, 100% { transform: scale(1) translateY(0); }
        50% { transform: scale(1.05) translateY(-8px); }
    }
    @keyframes fadeSlideUp {
        0% { transform: translateY(28px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeSlideRight {
        0% { transform: translateX(-20px); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    @keyframes scaleInBounce {
        0% { transform: scale(.85); opacity: 0; }
        60% { transform: scale(1.02); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes shimmerGradient {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes pingSlow {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.8); opacity: 0; }
    }
    @keyframes slideInLeft {
        0% { transform: translateX(-60px); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    .animate-float-slow { animation: floatSlow 6s ease-in-out infinite; }
    .animate-float-delayed { animation: floatSlowDelayed 8s ease-in-out 2s infinite; }
    .animate-float-pulse { animation: floatPulse 5s ease-in-out 1s infinite; }
    .animate-fade-up { animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) both; }
    .animate-fade-right { animation: fadeSlideRight .6s cubic-bezier(.16,1,.3,1) both; }
    .animate-fade-in { animation: fadeIn .8s ease-out both; }
    .animate-scale-in { animation: scaleInBounce .6s cubic-bezier(.16,1,.3,1) both; }
    .animate-shimmer-bg {
        background: linear-gradient(90deg, transparent 25%, rgba(255,255,255,.08) 50%, transparent 75%);
        background-size: 200% 100%;
        animation: shimmerGradient 2.5s ease-in-out infinite;
    }
    .animate-card-lift {
        transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s cubic-bezier(.16,1,.3,1);
    }
    .animate-card-lift:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 48px -12px rgba(0,0,0,.18);
    }
    .stagger-1 { animation-delay: .05s; }
    .stagger-2 { animation-delay: .12s; }
    .stagger-3 { animation-delay: .2s; }
    .stagger-4 { animation-delay: .28s; }
    .stagger-5 { animation-delay: .36s; }
    .stagger-6 { animation-delay: .44s; }
    .stagger-7 { animation-delay: .52s; }
    .hero-badge {
        animation: fadeSlideUp .6s cubic-bezier(.16,1,.3,1) both, pingSlow 2.5s ease-out 1.5s infinite;
    }
    .input-glow:focus {
        box-shadow: 0 0 0 3px rgba(99,102,241,.12), 0 0 20px rgba(99,102,241,.06);
    }
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.15) 50%, transparent 70%);
        transform: translateX(-100%);
        animation: shimmerGradient 3s ease-in-out infinite;
    }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-200px)] grid lg:grid-cols-2 gap-0 -mx-4">
    {{-- Left: Hero Brand Panel --}}
    <div class="hidden lg:flex relative bg-gradient-to-br from-brand-600 via-brand-800 to-stone-900 p-12 flex-col justify-between overflow-hidden">
        {{-- Decorative gradient circles --}}
        <div class="absolute inset-0 opacity-30 animate-fade-in"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.15) 0%, transparent 50%),
                               radial-gradient(circle at 80% 70%, rgba(255,255,255,0.1) 0%, transparent 50%),
                               radial-gradient(circle at 50% 50%, rgba(99,102,241,0.3) 0%, transparent 70%);">
        </div>

        {{-- Decorative floating circles --}}
        <div class="absolute top-20 left-10 w-72 h-72 rounded-full bg-white/4 animate-float-slow"></div>
        <div class="absolute top-1/3 right-16 w-40 h-40 rounded-full bg-brand-400/10 animate-float-delayed"></div>
        <div class="absolute bottom-44 left-20 w-52 h-52 rounded-full bg-white/3 animate-float-pulse"></div>
        <div class="absolute bottom-20 right-10 w-60 h-60 rounded-full bg-brand-300/8 animate-float-slow" style="animation-delay: 3s;"></div>

        {{-- Shimmer line --}}
        <div class="absolute top-0 left-0 right-0 h-px animate-shimmer-bg"></div>

        {{-- Large decorative emoji --}}
        <div class="absolute -bottom-20 -right-20 text-[22rem] opacity-8 select-none animate-float-pulse">🛒</div>

        {{-- Logo + Brand --}}
        <div class="relative animate-slide-in-left">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-white">
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm animate-scale-in">
                    <i class="fas fa-store text-white text-lg"></i>
                </div>
                <span class="font-display font-bold text-2xl">TokoOnline</span>
            </a>
        </div>

        {{-- Tagline + Benefit Cards --}}
        <div class="relative text-white">
            <h2 class="font-display text-5xl font-bold leading-tight mb-4 animate-fade-up stagger-1">
                Platform E-Commerce Terlengkap
            </h2>
            <p class="text-brand-100 text-lg leading-relaxed mb-8 max-w-md animate-fade-up stagger-2">
                Belanja jutaan produk dari ribuan penjual terpercaya. Harga terbaik, pengiriman cepat ke seluruh Indonesia.
            </p>
            <div class="grid grid-cols-3 gap-4 max-w-md">
                <div class="hero-badge bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 animate-card-lift stagger-3">
                    <i class="fas fa-tags text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Harga Terbaik</span>
                </div>
                <div class="hero-badge bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 animate-card-lift stagger-4">
                    <i class="fas fa-shipping-fast text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Kirim Cepat</span>
                </div>
                <div class="hero-badge bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10 animate-card-lift stagger-5">
                    <i class="fas fa-shield-alt text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Aman Terpercaya</span>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="relative text-brand-200/70 text-xs animate-fade-in stagger-7">
            &copy; {{ date('Y') }} TokoOnline &middot; Powered by Laravel
        </div>
    </div>

    {{-- Right: Login Form --}}
    <div class="flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">
            <h1 class="font-display text-4xl font-bold text-stone-900 mb-2 animate-fade-up">Masuk</h1>
            <p class="text-stone-500 mb-8 animate-fade-up stagger-1">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:underline">Daftar gratis</a>
            </p>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700 animate-scale-in">
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
                <div class="animate-fade-up stagger-2">
                    <label for="email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm transition-colors duration-200 peer-focus:text-brand-500"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               placeholder="nama@email.com" required autofocus
                               class="input-glow w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all duration-300 placeholder:text-stone-400 peer">
                    </div>
                </div>

                {{-- Password --}}
                <div class="animate-fade-up stagger-3" x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-stone-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input :type="show ? 'text' : 'password'" name="password" id="password"
                               placeholder="Minimal 8 karakter" required
                               class="input-glow w-full pl-11 pr-12 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all duration-300 placeholder:text-stone-400">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between animate-fade-up stagger-4">
                    <label class="flex items-center gap-2 text-sm text-stone-600 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-stone-300 text-brand-600 focus:ring-brand-500 transition-all">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-brand-600 hover:underline font-medium transition-colors">
                        Lupa kata sandi?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="btn-shine animate-fade-up stagger-5 w-full py-3.5 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-500 hover:to-brand-600
                               text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25
                               hover:shadow-brand-500/40 transition-all duration-300 hover:-translate-y-0.5
                               active:translate-y-0 active:shadow-md">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-8 animate-fade-up stagger-6">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">atau</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            {{-- Demo Login Box --}}
            <div class="animate-scale-in stagger-7 bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm animate-card-lift">
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
