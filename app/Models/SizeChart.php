<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'fit_type',
        'stretch_type',
        'photos',
        'description',
        'measurement_points',
        'size_options',
        'measurement_option',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function details()
    {
        return $this->hasMany(SizeChartDetail::class);
    }
}
