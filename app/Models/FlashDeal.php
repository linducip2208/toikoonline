<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDeal extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'status',
        'featured',
        'background_color',
        'text_color',
        'banner',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    public function flashDealProducts()
    {
        return $this->hasMany(FlashDealProduct::class);
    }
}
