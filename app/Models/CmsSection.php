<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CmsSection extends Model
{
    protected $fillable = ['key', 'title', 'is_active', 'sort_order', 'data'];

    protected $casts = [
        'is_active' => 'boolean',
        'data' => 'array',
    ];

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }

    public static function ordered()
    {
        return static::orderBy('sort_order')->get();
    }
}
