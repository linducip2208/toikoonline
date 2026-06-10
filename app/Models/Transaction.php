<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'gateway',
        'payment_type',
        'additional_content',
        'mpesa_request',
        'mpesa_receipt',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
