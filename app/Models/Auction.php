<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'product_id',
        'starting_bid',
        'current_bid',
        'buy_now_price',
        'bid_increment',
        'start_date',
        'end_date',
        'status',
        'winner_id',
    ];

    protected function casts(): array
    {
        return [
            'starting_bid' => 'decimal:2',
            'current_bid' => 'decimal:2',
            'buy_now_price' => 'decimal:2',
            'bid_increment' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}
