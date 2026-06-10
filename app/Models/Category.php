<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'commision_rate',
        'banner',
        'icon',
        'featured',
        'top',
        'digital',
        'slug',
        'meta_title',
        'meta_description',
        'filter_attributes',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'top' => 'boolean',
            'digital' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function sizeCharts()
    {
        return $this->hasMany(SizeChart::class);
    }

    public function categoryDiscount()
    {
        return $this->hasOne(CategoryDiscount::class);
    }

    public function scopeActive(Builder $query): void
    {
        //
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('featured', true);
    }
}
