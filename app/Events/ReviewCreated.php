<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Review $review) {}

    public function payload(): array
    {
        return [
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'user_id' => $this->review->user_id,
            'rating' => (int) $this->review->rating,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
