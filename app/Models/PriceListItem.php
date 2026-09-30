<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = ['price_list_id', 'product_id', 'variant', 'price', 'min_qty'];

    protected $casts = ['price' => 'integer', 'min_qty' => 'integer'];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
