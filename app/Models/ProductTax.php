<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTax extends Model
{
    protected $fillable = [
        'product_id',
        'tax_id',
        'tax',
        'tax_type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
