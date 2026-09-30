@extends('customer.layout')
@section('title', $address->exists ? __('customer.edit_address') : __('customer.add_address_title'))
@section('page-title', $address->exists ? __('customer.edit_address') : __('customer.add_address_title'))

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-extrabold text-stone-900 mb-6">{{ $address->exists ? __('customer.edit_address') : __('customer.add_new_address') }}</h1>

    <div class="bg-white border border-stone-200 rounded-2xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ $address->exists ? route('customer.addresses.update', $address) : route('customer.addresses.store') }}" class="space-y-5">
            @csrf
            @if($address->exists) @method('PUT') @endif

            <div>
                <label for="alamat-lengkap" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.full_address') }}</label>
                <textarea id="alamat-lengkap" name="address" rows="3" required autocomplete="street-address" placeholder="{{ __('customer.address_ph') }}"
                    class="w-full px-4 py-3 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('address') border-red-400 @enderror">{{ old('address', $address->address) }}</textarea>
                @error('address')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="negara" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.country') }}</label>
                    <select id="negara" name="country_id" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('country_id') border-red-400 @enderror">
                        <option value="">{{ __('customer.choose_country') }}</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" @selected((string) old('country_id', $address->country_id) === (string) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="provinsi" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.province') }}</label>
                    <select id="provinsi" name="state_id" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('state_id') border-red-400 @enderror">
                        <option value="">{{ __('customer.choose_province') }}</option>
                        @foreach($states as $s)
                            <option value="{{ $s->id }}" @selected((string) old('state_id', $address->state_id) === (string) $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('state_id')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kota" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.city') }}</label>
                    <select id="kota" name="city_id" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('city_id') border-red-400 @enderror">
                        <option value="">{{ __('customer.choose_city') }}</option>
                        @foreach($cities as $c)
                            <option value="{{ $c->id }}" @selected((string) old('city_id', $address->city_id) === (string) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="area" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.district') }}</label>
                    <select id="area" name="area_id" class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('area_id') border-red-400 @enderror">
                        <option value="">{{ __('customer.choose_district') }}</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}" @selected((string) old('area_id', $address->area_id) === (string) $a->id)>{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kodepos" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.postal_code') }}</label>
                    <input type="text" id="kodepos" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" autocomplete="postal-code" inputmode="numeric"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('postal_code') border-red-400 @enderror">
                    @error('postal_code')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="telepon" class="block text-sm font-semibold text-stone-700 mb-1.5">{{ __('customer.recipient_phone') }}</label>
                    <input type="tel" id="telepon" name="phone" value="{{ old('phone', $address->phone) }}" required autocomplete="tel"
                        class="w-full px-4 py-3 min-h-[44px] border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-xs text-red-600 mt-1.5" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <label class="inline-flex items-center gap-2.5 text-sm text-stone-700 cursor-pointer min-h-[44px]">
                    <input type="checkbox" name="set_default" value="1" @checked(old('set_default', $address->set_default)) class="w-5 h-5 rounded accent-indigo-600">
                    {{ __('customer.set_primary') }}
                </label>
                <label class="inline-flex items-center gap-2.5 text-sm text-stone-700 cursor-pointer min-h-[44px]">
                    <input type="checkbox" name="set_billing" value="1" @checked(old('set_billing', (bool) $address->set_billing)) class="w-5 h-5 rounded accent-indigo-600">
                    {{ __('customer.set_billing') }}
                </label>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                    {{ $address->exists ? __('customer.save_changes') : __('customer.save_address') }}
                </button>
                <a href="{{ route('customer.addresses') }}" class="inline-flex items-center justify-center min-h-[44px] px-8 py-3 text-sm font-bold text-stone-600 bg-stone-100 rounded-xl hover:bg-stone-200 transition">
                    {{ __('customer.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
