<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index()
    {
        $auctions = Auction::with(['product', 'bids'])
            ->withCount('bids')
            ->whereIn('status', ['active'])
            ->where('start_date', '<=', now())
            ->where('end_date', '>', now())
            ->orderBy('end_date')
            ->paginate(12);

        return view('storefront.auctions.index', compact('auctions'));
    }

    public function show($auction)
    {
        $auction = Auction::with(['product.category', 'bids.user', 'winner'])
            ->withCount('bids')
            ->findOrFail($auction);

        $bids = $auction->bids()->with('user')->orderBy('amount', 'desc')->get();

        return view('storefront.auctions.show', compact('auction', 'bids'));
    }

    public function bid(Request $request, $auction)
    {
        $auction = Auction::with('product')->findOrFail($auction);

        if ($auction->status !== 'active') {
            return back()->with('error', 'Lelang tidak aktif.');
        }

        if (now()->greaterThan($auction->end_date)) {
            return back()->with('error', 'Lelang telah berakhir.');
        }

        $minBid = ($auction->current_bid ?? $auction->starting_bid) + $auction->bid_increment;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $minBid],
        ]);

        $bid = AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
        ]);

        $auction->update([
            'current_bid' => $request->amount,
        ]);

        return back()->with('success', 'Tawaran berhasil diajukan!');
    }
}
