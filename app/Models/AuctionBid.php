<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
