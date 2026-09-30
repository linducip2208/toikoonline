<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartAbandoned
{
    use Dispatchable, SerializesModels;

    public function __construct(public User $user, public int $itemCount = 0) {}

    public function payload(): array
    {
        return [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'items' => $this->itemCount,
            'abandoned_at' => now()->toIso8601String(),
        ];
    }
}
