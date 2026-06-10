<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $fillable = [
        'text',
        'logo',
    ];

    public function logoFile()
    {
        return $this->belongsTo(Upload::class, 'logo');
    }
}
