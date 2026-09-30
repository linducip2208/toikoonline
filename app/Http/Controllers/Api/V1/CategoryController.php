<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection(Category::orderBy('name')->get())->resolve(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $cat = Category::where('slug', $slug)->firstOrFail();

        return response()->json(['success' => true, 'data' => (new CategoryResource($cat))->resolve()]);
    }
}
