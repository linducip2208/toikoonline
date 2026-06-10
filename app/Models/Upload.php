<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_original_name',
        'file_name',
        'user_id',
        'file_size',
        'extension',
        'type',
        'external_link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
