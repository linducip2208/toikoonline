<!DOCTYPE html>
<html lang="id" class="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @filamentStyles
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|jetbrains-mono:400,700" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-dark: #3730a3;
        }
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
        @keyframes fadeSlideRight {
            0% { transform: translateX(-20px); opacity: 0; }
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
        @keyframes slideInLeft {
            0% { transform: translateX(-60px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        .animate-float { animation: floatSlow 6s ease-in-out infinite; }
        .animate-float-pulse { animation: floatPulse 5s ease-in-out infinite; }
        .animate-fade-up { animation: fadeSlideUp .7s cubic-bezier(.16,1,.3,1) both; }
        .animate-scale-in { animation: scaleInBounce .6s cubic-bezier(.16,1,.3,1) both; }
        .animate-slide-left { animation: slideInLeft .7s cubic-bezier(.16,1,.3,1) both; }
        .stagger-1 { animation-delay: .05s; }
        .stagger-2 { animation-delay: .12s; }
        .stagger-3 { animation-delay: .2s; }
        .stagger-4 { animation-delay: .28s; }
        .stagger-5 { animation-delay: .36s; }
        .stagger-6 { animation-delay: .44s; }
        .stagger-7 { animation-delay: .52s; }
        .hero-card {
            transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s;
        }
        .hero-card:hover {
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
<body class="fi-body min-h-screen antialiased">
<div class="min-h-screen grid lg:grid-cols-2">
    {{-- Left: Hero Brand Panel --}}
    <div class="login-left hidden lg:flex relative bg-gradient-to-br from-indigo-600 via-indigo-800 to-stone-900 p-12 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 opacity-30"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,.15) 0%, transparent 50%),
                               radial-gradient(circle at 80% 70%, rgba(255,255,255,.1) 0%, transparent 50%),
                               radial-gradient(circle at 50% 50%, rgba(99,102,241,.3) 0%, transparent 70%);">
        </div>
        <div class="absolute top-16 left-8 w-72 h-72 rounded-full bg-white/5 animate-float"></div>
        <div class="absolute bottom-40 right-12 w-48 h-48 rounded-full bg-indigo-400/10 animate-float-pulse"></div>
        <div class="absolute bottom-16 right-8 w-56 h-56 rounded-full bg-white/3 animate-float" style="animation-delay: 3s;"></div>
        <div class="absolute -bottom-20 -right-20 text-[18rem] opacity-10 select-none">⚡</div>

        <div class="relative animate-slide-left">
            <div class="flex items-center gap-2.5 text-white">
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-store text-white text-lg"></i>
                </div>
                <span class="font-bold text-2xl tracking-tight" style="font-family: Inter, sans-serif;">TokoOnline</span>
            </div>
        </div>

        <div class="relative text-white">
            <h2 class="font-bold text-4xl lg:text-5xl leading-tight mb-4 animate-fade-up stagger-1" style="font-family: Inter, sans-serif;">
                Admin Panel
            </h2>
            <p class="text-indigo-100 text-lg leading-relaxed mb-8 max-w-md animate-fade-up stagger-2">
                Kelola toko online Anda dengan panel administrasi yang lengkap dan mudah digunakan.
            </p>
            <div class="grid grid-cols-3 gap-3 max-w-md">
                <div class="hero-card bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 animate-fade-up stagger-3">
                    <i class="fas fa-box text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-[11px] font-semibold text-white">Produk</span>
                </div>
                <div class="hero-card bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 animate-fade-up stagger-4">
                    <i class="fas fa-chart-bar text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-[11px] font-semibold text-white">Laporan</span>
                </div>
                <div class="hero-card bg-white/10 backdrop-blur-sm rounded-xl p-3.5 text-center border border-white/10 animate-fade-up stagger-5">
                    <i class="fas fa-cog text-xl mb-1.5 block text-indigo-200"></i>
                    <span class="text-[11px] font-semibold text-white">Sistem</span>
                </div>
            </div>
        </div>

        <div class="relative text-indigo-200/60 text-xs animate-fade-up stagger-7">
            &copy; {{ date('Y') }} TokoOnline &middot; Powered by Laravel + Filament
        </div>
    </div>

    {{-- Right: Login Form --}}
    <div class="flex items-center justify-center p-6 lg:p-16 bg-white">
        <div class="w-full max-w-md">
            {{-- Mobile brand --}}
            <div class="lg:hidden text-center mb-8 animate-scale-in">
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-store text-indigo-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-stone-900">TokoOnline</h2>
            </div>

            <h1 class="font-bold text-3xl text-stone-900 mb-1 animate-fade-up" style="font-family: Inter, sans-serif;">Masuk Admin</h1>
            <p class="text-stone-500 mb-8 animate-fade-up stagger-1">Panel administrasi TokoOnline</p>

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

            <div class="animate-fade-up stagger-2">
                {{ $this->form }}
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6 animate-fade-up stagger-5">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">demo</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            {{-- Demo Login Box --}}
            <div class="animate-scale-in stagger-6 bg-stone-50 border border-stone-200 rounded-xl p-5 text-sm">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-flask text-amber-500"></i>
                    <span class="font-semibold text-stone-800">Demo Login</span>
                </div>
                <div class="space-y-1.5 text-stone-600 text-xs font-mono">
                    @foreach($demoAccounts as $account)
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 {{ $account['role'] === 'Admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }} rounded-md font-semibold text-[10px]">{{ $account['role'] }}</span>
                        <span class="text-stone-500">{{ $account['email'] }} / {{ $account['password'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-5 animate-fade-up stagger-7">
                <a href="{{ url('/') }}" class="text-sm text-stone-400 hover:text-stone-600 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke toko
                </a>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@filamentScripts
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.fi-fo-field-wrp input, .fi-fo-field-wrp select, .fi-fo-field-wrp textarea')
            .forEach(el => {
                el.style.borderRadius = '12px';
                el.style.borderColor = '#d1d5db';
                el.style.transition = 'all .3s';
            });

        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('btn-shine');
            submitBtn.style.cssText = `
                width: 100%; padding: 14px 24px; font-size: 14px; font-weight: 600;
                background: linear-gradient(135deg, #6366f1, #4f46e5);
                color: #fff; border: none; border-radius: 12px; cursor: pointer;
                box-shadow: 0 4px 16px rgba(99,102,241,.3);
                transition: all .3s; font-family: Inter, sans-serif;
            `;
            submitBtn.addEventListener('mouseenter', () => {
                submitBtn.style.transform = 'translateY(-1px)';
                submitBtn.style.boxShadow = '0 8px 24px rgba(99,102,241,.4)';
            });
            submitBtn.addEventListener('mouseleave', () => {
                submitBtn.style.transform = 'translateY(0)';
                submitBtn.style.boxShadow = '0 4px 16px rgba(99,102,241,.3)';
            });
        }
    });
</script>
</body>
</html>
