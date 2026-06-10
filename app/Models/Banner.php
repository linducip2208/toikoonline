<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
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
