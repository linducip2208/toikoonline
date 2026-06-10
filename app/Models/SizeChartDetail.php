<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeChartDetail extends Model
{
    protected $fillable = [
        'size_chart_id',
        'measurement_point_id',
        'attribute_value_id',
        'inch_value',
        'cen_value',
    ];

    public function sizeChart()
    {
        return $this->belongsTo(SizeChart::class);
    }

    public function measurementPoint()
    {
        return $this->belongsTo(MeasurementPoint::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
