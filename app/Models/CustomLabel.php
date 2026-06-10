<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomLabel extends Model
{
    protected $fillable = [
        'user_id',
        'text',
        'background_color',
        'text_color',
        'seller_access',
    ];
}
