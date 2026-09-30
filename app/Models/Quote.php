<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    public const STATUSES = ['draft', 'sent', 'approved', 'rejected', 'expired'];

    protected $fillable = [
        'company_id', 'user_id', 'status', 'valid_until', 'notes', 'coupon_id',
    ];

    protected $casts = ['valid_until' => 'datetime'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function totalAmount(): int
    {
        return (int) $this->items->sum(fn ($i) => (int) $i->price * (int) $i->qty);
    }
}
