<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');
        :root {
            --brand-primary: #6366f1;
            --brand-dark: #3730a3;
        }
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body { margin: 0; background: #f8fafc; }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }
        @keyframes floatPulse {
            0%, 100% { transform: scale(1) translateY(0); }
            50% { transform: scale(1.05) translateY(-8px); }
        }
        @keyframes fadeSlideUp {
            0% { transform: translateY(28px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideInLeft {
            0% { transform: translateX(-60px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
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
        .anim-float { animation: floatSlow 6s ease-in-out infinite; }
        .anim-pulse { animation: floatPulse 5s ease-in-out infinite; }
        .anim-fade-up { animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) both; }
        .anim-slide-left { animation: slideInLeft .7s cubic-bezier(.16,1,.3,1) both; }
        .anim-scale-in { animation: scaleInBounce .6s cubic-bezier(.16,1,.3,1) both; }
        .stg-1 { animation-delay: .05s; }
        .stg-2 { animation-delay: .12s; }
        .stg-3 { animation-delay: .2s; }
        .stg-4 { animation-delay: .28s; }
        .stg-5 { animation-delay: .36s; }
        .stg-6 { animation-delay: .44s; }
        .stg-7 { animation-delay: .52s; }
        .card-lift {
            transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s;
        }
        .card-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,.15);
        }
        .btn-shine {
            position: relative; overflow: hidden;
        }
        .btn-shine::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.15) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: shimmerGradient 3s ease-in-out infinite;
        }
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(99,102,241,.12), 0 0 20px rgba(99,102,241,.06);
        }
        @media (max-width: 1023px) {
            .login-left { min-height: 240px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body class="min-h-screen">
<div class="min-h-screen grid lg:grid-cols-2 bg-stone-50">

    {{-- LEFT: Hero Brand --}}
    <div class="login-left hidden lg:flex relative bg-gradient-to-br from-indigo-600 via-indigo-800 to-stone-900 p-12 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 opacity-30"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.15) 0%, transparent 50%),
                               radial-gradient(circle at 80% 70%, rgba(255,255,255,.1) 0%, transparent 50%),
                               radial-gradient(circle at 50% 50%, rgba(99,102,241,.3) 0%, transparent 70%);">
        </div>
        <div class="absolute top-16 left-8 w-72 h-72 rounded-full bg-white/5 anim-float"></div>
        <div class="absolute bottom-40 right-12 w-48 h-48 rounded-full bg-indigo-400/10 anim-pulse"></div>
        <div class="absolute bottom-16 right-8 w-56 h-56 rounded-full bg-white/3 anim-float" style="animation-delay: 3s;"></div>
        <div class="absolute -bottom-16 -right-16 text-[16rem] opacity-[.06] select-none">⚡</div>

        {{-- Logo --}}
        <div class="relative anim-slide-left">
            <div class="flex items-center gap-2.5 text-white">
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-store text-white text-lg"></i>
                </div>
                <span class="font-bold text-2xl tracking-tight">{{ config('app.name') }}</span>
            </div>
        </div>

        {{-- Tagline --}}
        <div class="relative text-white">
            <h2 class="font-extrabold text-4xl lg:text-5xl leading-tight mb-4 anim-fade-up stg-1">Admin Panel</h2>
            <p class="text-indigo-100 text-lg leading-relaxed mb-8 max-w-md anim-fade-up stg-2">
                Kelola toko online Anda dengan panel administrasi yang lengkap dan mudah digunakan.
            </p>
            <div class="grid grid-cols-3 gap-3 max-w-md">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 card-lift anim-fade-up stg-3">
                    <i class="fas fa-box text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-white font-semibold" style="font-size: 11px;">Produk</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 card-lift anim-fade-up stg-4">
                    <i class="fas fa-chart-bar text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-white font-semibold" style="font-size: 11px;">Laporan</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 card-lift anim-fade-up stg-5">
                    <i class="fas fa-cog text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-white font-semibold" style="font-size: 11px;">Sistem</span>
                </div>
            </div>
        </div>

        <div class="relative text-indigo-200/60 text-xs anim-fade-up stg-7">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Laravel + Filament
        </div>
    </div>

    {{-- RIGHT: Login Form --}}
    <div class="flex items-center justify-center p-6 lg:p-16 bg-white">
        <div class="w-full max-w-md">
            {{-- Mobile brand --}}
            <div class="lg:hidden text-center mb-8 anim-scale-in">
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-store text-indigo-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-stone-900">{{ config('app.name') }}</h2>
            </div>

            <h1 class="font-extrabold text-3xl text-stone-900 mb-1 anim-fade-up">Masuk Admin</h1>
            <p class="text-stone-500 mb-8 anim-fade-up stg-1">Panel administrasi {{ config('app.name') }}</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700 anim-scale-in">
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

            <form method="POST" action="{{ url('/admin/login') }}" class="space-y-5">
                @csrf

                <div class="anim-fade-up stg-2">
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="admin@tokoonline.test" required autofocus
                               class="input-glow w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400
                                      transition-all duration-300">
                    </div>
                </div>

                <div class="anim-fade-up stg-3" x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input :type="show ? 'text' : 'password'" name="password"
                               placeholder="Masukkan kata sandi" required
                               class="input-glow w-full pl-11 pr-12 py-3 rounded-xl border border-stone-300 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400
                                      transition-all duration-300">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 transition-colors">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between anim-fade-up stg-4">
                    <label class="flex items-center gap-2 text-sm text-stone-600 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-stone-300 text-indigo-600 focus:ring-indigo-500">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline font-medium">Lupa kata sandi?</a>
                </div>

                <button type="submit"
                        class="btn-shine anim-fade-up stg-5 w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700
                               hover:from-indigo-500 hover:to-indigo-600 text-white rounded-xl font-semibold text-sm
                               shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40
                               transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            <div class="flex items-center gap-3 my-8 anim-fade-up stg-6">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">demo</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            <div class="bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm anim-scale-in stg-7 card-lift">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-flask text-amber-500"></i>
                    <span class="font-semibold text-stone-800">Demo Login</span>
                </div>
                <div class="space-y-1.5 text-stone-600 text-xs font-mono" style="font-family: 'JetBrains Mono', monospace;">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md font-semibold" style="font-size: 10px; font-family: Inter, sans-serif;">Admin</span>
                        <span>admin@tokoonline.test / password</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 rounded-md font-semibold" style="font-size: 10px; font-family: Inter, sans-serif;">Customer</span>
                        <span>customer@tokoonline.test / password</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 anim-fade-up stg-7">
                <a href="{{ url('/') }}" class="text-sm text-stone-400 hover:text-stone-600 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke toko
                </a>
            </div>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</body>
</html>
