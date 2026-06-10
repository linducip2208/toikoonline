<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProduct extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'unit_price',
        'description',
        'photos',
        'thumbnail_img',
        'published',
        'status',
        'condition',
        'location',
        'tags',
        'video_provider',
        'video_link',
        'slug',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'published' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
