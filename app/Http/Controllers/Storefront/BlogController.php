<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::published()->with('category')->latest('published_at')->paginate(12);

        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->get();

        $recentPosts = Blog::published()->latest('published_at')->take(5)->get();

        return view('storefront.blog.index', compact('blogs', 'categories', 'recentPosts'));
    }

    public function show($slug)
    {
        $blog = Blog::published()->with('category')->where('slug', $slug)->firstOrFail();

        $blog->increment('views');

        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->get();
        $recentPosts = Blog::published()->where('id', '!=', $blog->id)->latest('published_at')->take(5)->get();

        return view('storefront.blog.show', compact('blog', 'categories', 'recentPosts'));
    }

    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $blogs = Blog::published()->where('category_id', $category->id)->with('category')->latest('published_at')->paginate(12);

        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->get();
        $recentPosts = Blog::published()->latest('published_at')->take(5)->get();

        return view('storefront.blog.index', compact('blogs', 'categories', 'recentPosts', 'category'));
    }
}
