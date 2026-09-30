<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Banner extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['title'];
    protected $fillable = [
        'title',
        'photo',
        'link',
        'position',
        'type',
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
