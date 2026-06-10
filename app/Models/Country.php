<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'zone_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
