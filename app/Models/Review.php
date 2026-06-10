<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'type',
        'product_id',
        'user_id',
        'custom_reviewer_name',
        'custom_reviewer_image',
        'rating',
        'comment',
        'photos',
        'status',
        'viewed',
        'created_at_is_custom',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'viewed' => 'boolean',
            'created_at_is_custom' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
