<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalePrice extends Model
{
    protected $fillable = [
        'product_id',
        'min_qty',
        'max_qty',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
