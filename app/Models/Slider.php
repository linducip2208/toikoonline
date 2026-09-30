<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Slider extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['title', 'subtitle'];
    protected $fillable = [
        'title',
        'subtitle',
        'photo',
        'link',
        'position',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function photoFile()
    {
        return $this->belongsTo(Upload::class, 'photo');
    }
}
