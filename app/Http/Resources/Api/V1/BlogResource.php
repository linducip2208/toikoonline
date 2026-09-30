<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'content' => $request->routeIs('api.v1.blogs.show') ? $this->content : null,
            'featured_image' => $this->featured_image,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
