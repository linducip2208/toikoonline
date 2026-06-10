<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'short_description',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'keywords',
        'meta_image',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'views' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }
}
