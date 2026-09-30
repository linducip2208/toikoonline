<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageRevision extends Model
{
    protected $fillable = ['page_id', 'title', 'slug', 'content', 'blocks', 'created_by'];

    protected $casts = [
        'blocks' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
