<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'top',
        'slug',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'top' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function logoImage()
    {
        return $this->belongsTo(Upload::class, 'logo');
    }
}
