<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Blog;
use App\Models\Page;

class SitemapController extends Controller
{
    /**
     * Build the full URL set with hreflang alternates (id/en) and lastmod.
     * Shared by the live index() response and the sitemap:generate command.
     *
     * @return array<int, array{url:string, alternates:array<string,string>, priority:string, changefreq:string, lastmod:string}>
     */
    public static function urlSet(): array
    {
        $appUrl = rtrim(config('app.url'), '/');
        $locales = ['id', 'en'];

        $add = function (string $path, string $priority, string $changefreq, ?string $lastmod = null) use ($appUrl, $locales) {
            $alternates = [];
            foreach ($locales as $l) {
                $alternates[$l] = $appUrl . '/' . $l . $path;
            }

            return [
                'url' => url($path === '' ? '/' : $path),
                'alternates' => $alternates,
                'priority' => $priority,
                'changefreq' => $changefreq,
                'lastmod' => $lastmod ?? date('Y-m-d'),
            ];
        };

        $urls = [];
        $urls[] = $add('', '1.0', 'daily');
        $urls[] = $add('/products', '0.9', 'daily');
        $urls[] = $add('/blog', '0.7', 'weekly');
        $urls[] = $add('/docs', '0.6', 'monthly');

        foreach (Category::where('featured', true)->get() as $cat) {
            $urls[] = $add("/categories/{$cat->slug}", '0.8', 'weekly', $cat->updated_at?->format('Y-m-d'));
        }

        foreach (Brand::get() as $brand) {
            $urls[] = $add("/brands/{$brand->slug}", '0.7', 'weekly', $brand->updated_at?->format('Y-m-d'));
        }

        foreach (Product::published()->approved()->get(['slug', 'updated_at']) as $product) {
            $urls[] = $add("/products/{$product->slug}", '0.7', 'weekly', $product->updated_at?->format('Y-m-d'));
        }

        foreach (Blog::published()->get(['slug', 'updated_at']) as $blog) {
            $urls[] = $add("/blog/{$blog->slug}", '0.6', 'monthly', $blog->updated_at?->format('Y-m-d'));
        }

        foreach (Page::where('status', true)->get(['slug', 'updated_at']) as $page) {
            $urls[] = $add("/page/{$page->slug}", '0.5', 'monthly', $page->updated_at?->format('Y-m-d'));
        }

        return $urls;
    }

    public function index()
    {
        $urls = array_map(fn ($u) => [
            'url' => $u['url'],
            'priority' => $u['priority'],
            'changefreq' => $u['changefreq'],
        ], static::urlSet());

        return response()->view('sitemap', compact('urls'))->header('Content-Type', 'text/xml');
    }
}
