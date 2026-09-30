<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->where('status', true)->firstOrFail();

        return response()->json(['success' => true, 'data' => (new PageResource($page))->resolve()]);
    }
}
