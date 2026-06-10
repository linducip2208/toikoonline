<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuery extends Model
{
    protected $fillable = [
        'customer_id',
        'seller_id',
        'product_id',
        'question',
        'reply',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
