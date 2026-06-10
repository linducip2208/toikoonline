<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Blog;
use App\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        $urls[] = ['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'];
        $urls[] = ['url' => url('/products'), 'priority' => '0.9', 'changefreq' => 'daily'];
        $urls[] = ['url' => url('/blog'), 'priority' => '0.7', 'changefreq' => 'weekly'];
        $urls[] = ['url' => url('/docs'), 'priority' => '0.6', 'changefreq' => 'monthly'];

        foreach (Category::where('featured', true)->get() as $cat) {
            $urls[] = ['url' => url("/categories/{$cat->slug}"), 'priority' => '0.8', 'changefreq' => 'weekly'];
        }

        foreach (Brand::get() as $brand) {
            $urls[] = ['url' => url("/brands/{$brand->slug}"), 'priority' => '0.7', 'changefreq' => 'weekly'];
        }

        foreach (Product::published()->approved()->get() as $product) {
            $urls[] = ['url' => url("/products/{$product->slug}"), 'priority' => '0.7', 'changefreq' => 'weekly'];
        }

        foreach (Blog::published()->get() as $blog) {
            $urls[] = ['url' => url("/blog/{$blog->slug}"), 'priority' => '0.6', 'changefreq' => 'monthly'];
        }

        foreach (Page::all() as $page) {
            $urls[] = ['url' => url("/page/{$page->slug}"), 'priority' => '0.5', 'changefreq' => 'monthly'];
        }

        return response()->view('sitemap', compact('urls'))->header('Content-Type', 'text/xml');
    }
}
