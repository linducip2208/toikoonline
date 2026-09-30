<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class BlogCategory extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['name', 'description'];
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
