<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'blocks',
        'status',
        'show_in_footer',
        'meta_title',
        'meta_description',
        'keywords',
        'meta_image',
    ];

    protected $casts = [
        'blocks' => 'array',
        'status' => 'boolean',
        'show_in_footer' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('status', true);
    }
}
