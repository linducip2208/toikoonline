<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'converted',
    ];

    protected function casts(): array
    {
        return [
            'converted' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ClubPointDetail::class);
    }
}
