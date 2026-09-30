<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'subscription_id', 'event', 'payload', 'status',
        'attempts', 'response', 'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'attempts' => 'integer',
            'next_retry_at' => 'datetime',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(WebhookSubscription::class, 'subscription_id');
    }
}
