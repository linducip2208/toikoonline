<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('product.category', 'product.brand')
            ->latest()
            ->paginate(12);

        return view('customer.wishlist', compact('wishlists'));
    }
}
