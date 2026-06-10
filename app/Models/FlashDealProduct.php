<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDealProduct extends Model
{
    protected $fillable = [
        'flash_deal_id',
        'product_id',
        'discount',
        'discount_type',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'decimal:2',
        ];
    }

    public function flashDeal()
    {
        return $this->belongsTo(FlashDeal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
