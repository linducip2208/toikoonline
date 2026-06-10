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
        'meta_title',
        'meta_description',
        'keywords',
        'meta_image',
    ];
}
