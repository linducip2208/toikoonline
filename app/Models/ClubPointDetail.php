<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubPointDetail extends Model
{
    protected $fillable = [
        'club_point_id',
        'user_id',
        'order_id',
        'points',
        'converted',
    ];

    protected function casts(): array
    {
        return [
            'converted' => 'boolean',
        ];
    }
}
