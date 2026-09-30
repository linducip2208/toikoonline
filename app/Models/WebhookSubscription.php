<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookSubscription extends Model
{
    protected $fillable = ['url', 'event', 'secret', 'is_active'];

    protected $hidden = ['secret'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function deliveries()
    {
        return $this->hasMany(WebhookDelivery::class, 'subscription_id');
    }
}
