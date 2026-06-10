<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class BlogRssController extends Controller
{
    public function index()
    {
        $posts = Blog::published()->latest('published_at')->take(20)->get();

        $feed = [
            'title' => config('app.name') . ' Blog',
            'link' => route('blog.index'),
            'description' => 'Blog resmi ' . config('app.name'),
            'language' => 'id-ID',
            'lastBuildDate' => $posts->first()?->published_at?->toRfc2822String() ?? now()->toRfc2822String(),
            'posts' => $posts,
        ];

        return response()->view('blog.rss', $feed)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
