<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'price',
        'qty',
        'image',
        'color_code',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function imageFile()
    {
        return $this->belongsTo(Upload::class, 'image');
    }
}
