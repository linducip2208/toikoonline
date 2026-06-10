<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDiscount extends Model
{
    protected $fillable = [
        'category_id',
        'discount',
        'discount_type',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
