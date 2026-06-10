<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLog extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_user_id',
        'order_id',
        'order_detail_id',
        'amount',
        'commission',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateUser()
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
