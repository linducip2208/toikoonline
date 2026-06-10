@extends('layouts.storefront')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 rounded-2xl mb-4">
                <i class="fas fa-key text-brand-600 text-2xl"></i>
            </div>
            <h1 class="font-display text-3xl font-bold text-stone-900 mb-2">Lupa Kata Sandi?</h1>
            <p class="text-stone-500 text-sm leading-relaxed">
                Masukkan email Anda dan kami akan mengirimkan link untuk mereset kata sandi Anda.
            </p>
        </div>

        {{-- Status Message --}}
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-sm text-green-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

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

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
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

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-500 hover:to-brand-600
                           text-white rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/25
                           hover:shadow-brand-500/40 transition-all hover:-translate-y-0.5
                           active:translate-y-0 active:shadow-md">
                <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset
            </button>
        </form>

        {{-- Back to Login --}}
        <div class="text-center mt-8">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-brand-600 font-medium transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Kembali ke halaman masuk
            </a>
        </div>
    </div>
</div>
@endsection
