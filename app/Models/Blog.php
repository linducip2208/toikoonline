<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Blog extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['title', 'content'];

    protected static function booted(): void
    {
        static::saving(function (Blog $blog) {
            $blog->content = \App\Services\Content\SafeHtml::clean($blog->content);
        });
    }
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
        'tags',
        'meta_image',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'views' => 'integer',
            'tags' => 'array',
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

    /**
     * Published AND scheduled time reached (null published_at = immediately visible).
     * Additive — scopePublished() behavior unchanged.
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Penulis: display name via user relation (no new table).
     */
    public function authorName(): string
    {
        return $this->user->name ?? 'Admin';
    }
}
