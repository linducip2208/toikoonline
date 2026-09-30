<?php

namespace App\Services\Content;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only blog helpers (related posts + archives).
 *
 * Integrator wiring (BlogController@show — Agent 2 owns the controller):
 *
 *   $related = \App\Services\Content\RelatedService::relatedPosts($blog, 4);
 *   $archives = \App\Services\Content\RelatedService::archives();
 */
class RelatedService
{
    /**
     * Same-category published posts, excluding self, newest first.
     */
    public static function relatedPosts(Blog $blog, int $limit = 4): \Illuminate\Support\Collection
    {
        $limit = max(1, min(12, $limit));

        return Blog::query()
            ->where('is_published', true)
            ->where('id', '!=', $blog->getKey())
            ->when($blog->category_id, fn ($q) => $q->where('category_id', $blog->category_id))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Archive buckets: [['year' => 2026, 'month' => 9, 'count' => 5], ...]
     * newest first. Seeder/empty-DB safe (returns [] when table missing).
     */
    public static function archives(): array
    {
        try {
            if (! Schema::hasTable('blogs')) {
                return [];
            }

            $driver = DB::getDriverName();
            $year = $driver === 'sqlite' ? "strftime('%Y', published_at)" : 'YEAR(published_at)';
            $month = $driver === 'sqlite' ? "strftime('%m', published_at)" : 'MONTH(published_at)';

            return Blog::query()
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->selectRaw("{$year} as year, {$month} as month, COUNT(*) as count")
                ->groupBy('year', 'month')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get()
                ->map(fn ($r) => ['year' => (int) $r->year, 'month' => (int) $r->month, 'count' => (int) $r->count])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
