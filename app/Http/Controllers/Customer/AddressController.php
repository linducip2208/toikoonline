<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->with(['country', 'state', 'city', 'area'])
            ->latest()
            ->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('customer.addresses.form', [
            'address' => new Address(['country_id' => 1]),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'states' => State::orderBy('name')->get(['id', 'name', 'country_id']),
            'cities' => City::orderBy('name')->limit(500)->get(['id', 'name', 'state_id']),
            'areas' => Area::orderBy('name')->limit(500)->get(['id', 'name', 'city_id']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:500',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area_id' => 'nullable|integer|exists:areas,id',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'set_default' => 'nullable|boolean',
            'set_billing' => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();
        $data['set_default'] = (bool) ($data['set_default'] ?? false);
        $data['set_billing'] = (bool) ($data['set_billing'] ?? false);

        DB::transaction(function () use ($data, &$address) {
            if ($data['set_default']) {
                Address::where('user_id', Auth::id())->update(['set_default' => false]);
            }
            $address = Address::create($data);
        });

        return redirect()->route('customer.addresses')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function edit(Address $address)
    {
        $this->authorizeOwner($address);

        return view('customer.addresses.form', [
            'address' => $address,
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'states' => State::orderBy('name')->get(['id', 'name', 'country_id']),
            'cities' => City::orderBy('name')->limit(500)->get(['id', 'name', 'state_id']),
            'areas' => Area::orderBy('name')->limit(500)->get(['id', 'name', 'city_id']),
        ]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeOwner($address);

        $data = $request->validate([
            'address' => 'required|string|max:500',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area_id' => 'nullable|integer|exists:areas,id',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'required|string|max:20',
            'set_default' => 'nullable|boolean',
            'set_billing' => 'nullable|boolean',
        ]);

        $data['set_default'] = (bool) ($data['set_default'] ?? false);
        $data['set_billing'] = (bool) ($data['set_billing'] ?? false);

        DB::transaction(function () use ($address, $data) {
            if ($data['set_default']) {
                Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['set_default' => false]);
            }
            $address->update($data);
        });

        return redirect()->route('customer.addresses')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeOwner($address);

        $wasDefault = (bool) $address->set_default;
        $address->delete();

        if ($wasDefault) {
            $next = Address::where('user_id', Auth::id())->latest()->first();
            if ($next) {
                $next->update(['set_default' => true]);
            }
        }

        return redirect()->route('customer.addresses')->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeOwner($address);

        DB::transaction(function () use ($address) {
            Address::where('user_id', Auth::id())->update(['set_default' => false]);
            $address->update(['set_default' => true]);
        });

        return redirect()->route('customer.addresses')->with('success', 'Alamat utama berhasil diubah.');
    }

    private function authorizeOwner(Address $address): void
    {
        if ((int) $address->user_id !== (int) Auth::id()) {
            abort(403, 'Alamat ini bukan milik Anda.');
        }
    }
}
