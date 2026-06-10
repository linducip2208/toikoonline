@extends('layouts.storefront')

@section('title', 'Daftar')

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
        <div class="absolute -bottom-20 -right-20 text-[20rem] opacity-10 select-none">🎉</div>

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
            <h2 class="font-display text-5xl font-bold leading-tight mb-4">Gabung Jadi Member</h2>
            <p class="text-brand-100 text-lg leading-relaxed mb-8 max-w-md">
                Dapatkan pengalaman belanja terbaik dengan berbagai keuntungan eksklusif untuk member TokoOnline.
            </p>
            <div class="grid grid-cols-3 gap-4 max-w-md">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-shopping-bag text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Mulai Belanja</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-box text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Lacak Pesanan</span>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/10">
                    <i class="fas fa-gift text-2xl mb-2 block text-brand-200"></i>
                    <span class="text-xs font-semibold text-white">Dapatkan Promo</span>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="relative text-brand-200/70 text-xs">
            &copy; {{ date('Y') }} TokoOnline &middot; Powered by Laravel
        </div>
    </div>

    {{-- Right: Register Form --}}
    <div class="flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">
            <h1 class="font-display text-4xl font-bold text-stone-900 mb-2">Daftar</h1>
            <p class="text-stone-500 mb-8">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:underline">Masuk di sini</a>
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

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               placeholder="Nama Anda" required autofocus
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               placeholder="nama@email.com" required
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-semibold text-stone-700 mb-1.5">Nomor Telepon <span class="text-stone-400 font-normal">(opsional)</span></label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               placeholder="0812-3456-7890"
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

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-stone-700 mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Ulangi kata sandi" required
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-stone-300 bg-white text-sm
                                      focus:outline-none focus:ring-3 focus:ring-brand-500/20 focus:border-brand-400
                                      transition-all placeholder:text-stone-400">
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-500 hover:to-brand-600
                               text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25
                               hover:shadow-brand-500/40 transition-all hover:-translate-y-0.5
                               active:translate-y-0 active:shadow-md">
                    <i class="fas fa-user-plus mr-2"></i>Daftar
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-stone-200"></div>
                <span class="text-xs text-stone-400 font-medium">keuntungan member</span>
                <div class="flex-1 h-px bg-stone-200"></div>
            </div>

            {{-- Member Benefits --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3">
                    <i class="fas fa-percent text-amber-600 text-lg"></i>
                    <div>
                        <span class="text-xs font-semibold text-amber-800 block">Voucher Khusus</span>
                        <span class="text-[10px] text-amber-600">Diskon member eksklusif</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-3">
                    <i class="fas fa-star text-green-600 text-lg"></i>
                    <div>
                        <span class="text-xs font-semibold text-green-800 block">Poin Belanja</span>
                        <span class="text-[10px] text-green-600">Kumpulkan & tukar hadiah</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <i class="fas fa-truck-fast text-blue-600 text-lg"></i>
                    <div>
                        <span class="text-xs font-semibold text-blue-800 block">Gratis Ongkir</span>
                        <span class="text-[10px] text-blue-600">Min. belanja tertentu</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-purple-50 border border-purple-200 rounded-xl p-3">
                    <i class="fas fa-headset text-purple-600 text-lg"></i>
                    <div>
                        <span class="text-xs font-semibold text-purple-800 block">Prioritas Support</span>
                        <span class="text-[10px] text-purple-600">CS khusus member</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
