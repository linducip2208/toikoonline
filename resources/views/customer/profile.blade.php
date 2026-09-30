@extends('customer.layout')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-extrabold text-stone-900 mb-6">Profil Saya</h1>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-sm font-semibold text-green-800" role="status">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 sm:p-8 shadow-sm mb-6">
        <h2 class="font-bold text-stone-900 mb-6">Data Diri</h2>
        <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="nama" class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Lengkap</label>
                <input type="text" id="nama" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="telepon" class="block text-sm font-semibold text-stone-700 mb-1.5">No. HP / Telepon</label>
                    <input type="tel" id="telepon" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-stone-700 mb-1.5">Alamat</label>
                <textarea id="alamat" name="address" rows="2" autocomplete="street-address"
                    class="w-full px-4 py-3 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('address') border-red-400 @enderror">{{ old('address', $user->address) }}</textarea>
                @error('address')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="kota" class="block text-sm font-semibold text-stone-700 mb-1.5">Kota</label>
                    <input type="text" id="kota" name="city" value="{{ old('city', $user->city) }}" autocomplete="address-level2"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                </div>
                <div>
                    <label for="kodepos" class="block text-sm font-semibold text-stone-700 mb-1.5">Kode Pos</label>
                    <input type="text" id="kodepos" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" autocomplete="postal-code"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                </div>
                <div>
                    <label for="negara" class="block text-sm font-semibold text-stone-700 mb-1.5">Negara</label>
                    <input type="text" id="negara" name="country" value="{{ old('country', $user->country) }}" autocomplete="country-name"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="sandi" class="block text-sm font-semibold text-stone-700 mb-1.5">Kata Sandi Baru <span class="font-normal text-stone-400">(opsional)</span></label>
                    <input type="password" id="sandi" name="password" autocomplete="new-password"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('password') border-red-400 @enderror">
                    @error('password')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sandi-konfirmasi" class="block text-sm font-semibold text-stone-700 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" id="sandi-konfirmasi" name="password_confirmation" autocomplete="new-password"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400">
                </div>
            </div>

            <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="bg-white border border-stone-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <h2 class="font-bold text-stone-900 mb-4">Alamat Tersimpan</h2>
        @if(isset($addresses) && $addresses->count() > 0)
            <ul class="space-y-3" aria-label="Daftar alamat tersimpan">
                @foreach($addresses as $address)
                    <li class="border border-stone-200 rounded-xl p-4 text-sm">
                        <p class="font-semibold text-stone-800">{{ $address->address }}</p>
                        <p class="text-stone-500 mt-1">{{ $address->city->name ?? '' }} {{ $address->postal_code }} — {{ $address->phone }}</p>
                        @if($address->set_default)
                            <span class="inline-block mt-2 px-2.5 py-1 text-xs font-bold rounded-full bg-brand-50 text-brand-700">Utama</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-stone-500" role="status">Belum ada alamat tersimpan. Tambahkan alamat saat checkout.</p>
        @endif
    </div>
</div>
@endsection
