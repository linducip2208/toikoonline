<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReviewRequest;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:products,id']);

        $reviews = Review::with('user')->where('product_id', $request->product_id)
            ->where('status', true)->latest()->paginate(15);

        return ReviewResource::collection($reviews)->response();
    }

    public function store(ReviewRequest $request): JsonResponse
    {
        $review = Review::create([
            'type' => 'product',
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => true,
        ]);

        if (class_exists(\App\Events\ReviewCreated::class)) {
            try {
                event(new \App\Events\ReviewCreated($review));
            } catch (\Exception) {
            }
        }

        return response()->json(['success' => true, 'data' => (new ReviewResource($review))->resolve()], 201);
    }
}
