<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        $blogs = Blog::published()->latest('published_at')->paginate(15);

        return BlogResource::collection($blogs)->response();
    }

    public function show(string $slug): JsonResponse
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        return response()->json(['success' => true, 'data' => (new BlogResource($blog))->resolve()]);
    }
}
