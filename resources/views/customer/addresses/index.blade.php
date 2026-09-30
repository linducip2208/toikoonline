@extends('customer.layout')
@section('title', __('customer.addresses'))
@section('page-title', __('customer.addresses'))

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-stone-900">{{ __('customer.addresses') }}</h1>
        <a href="{{ route('customer.addresses.create') }}" class="inline-flex items-center justify-center min-h-[44px] gap-2 px-5 py-2.5 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('customer.add_address') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-sm font-semibold text-green-800" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($addresses->count() > 0)
        <ul class="space-y-4" aria-label="Daftar alamat tersimpan">
            @foreach($addresses as $address)
                <li class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm min-w-0">
                            <p class="font-semibold text-stone-800">{{ $address->address }}</p>
                            <p class="text-stone-500 mt-1">
                                {{ $address->area->name ?? '' }}{{ isset($address->area->name) ? ', ' : '' }}{{ $address->city->name ?? '' }}{{ isset($address->city->name) ? ', ' : '' }}{{ $address->state->name ?? '' }} {{ $address->postal_code }}
                            </p>
                            <p class="text-stone-500 mt-0.5">{{ $address->phone }}</p>
                            <div class="flex gap-2 mt-2">
                                @if($address->set_default)
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full bg-brand-50 text-brand-700">{{ __('customer.primary_badge') }}</span>
                                @endif
                                @if($address->set_billing)
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full bg-stone-100 text-stone-600">{{ __('customer.billing_badge') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4">
                        @if(! $address->set_default)
                            <form method="POST" action="{{ route('customer.addresses.default', $address) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center min-h-[44px] px-4 py-2 text-xs font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition">
                                    {{ __('customer.make_primary') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('customer.addresses.edit', $address) }}" class="inline-flex items-center min-h-[44px] px-4 py-2 text-xs font-bold text-stone-700 bg-stone-100 rounded-lg hover:bg-stone-200 transition">
                            {{ __('customer.edit') }}
                        </a>
                        <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}" onsubmit="return confirm('{{ __('customer.addresses.confirm_delete') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center min-h-[44px] px-4 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                {{ __('customer.delete') }}
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-16 bg-white border border-stone-200 rounded-2xl" role="status">
            <div class="text-6xl mb-4" aria-hidden="true">📍</div>
            <h2 class="text-xl font-bold text-stone-800 mb-2">{{ __('customer.no_address_title') }}</h2>
            <p class="text-stone-500 mb-6">{{ __('customer.no_address_hint') }}</p>
            <a href="{{ route('customer.addresses.create') }}" class="inline-flex items-center justify-center min-h-[44px] gap-2 px-6 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 hover:shadow-lg transition">
                {{ __('customer.add_first_address') }}
            </a>
        </div>
    @endif
</div>
@endsection
