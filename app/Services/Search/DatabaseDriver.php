<?php

namespace App\Services\Search;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

/**
 * Zero-dependency database driver: SKU/barcode exact-match boost,
 * name/category/brand/tags LIKE with relevance score, typo tolerance via
 * levenshtein over a capped token candidate set, filters + sorts.
 */
class DatabaseDriver implements SearchDriverInterface
{
    public function search(string $query, array $filters = [], string $sort = 'relevance', int $limit = 50): array
    {
        $tokens = SearchService::tokenize($query);
        $base = Product::published()->approved()->with(['category', 'brand']);

        $this->applyFilters($base, $filters);

        if ($tokens === []) {
            $base = $this->applySort($base, $sort === 'relevance' ? 'latest' : $sort);
            $ids = (clone $base)->limit($limit)->pluck('id')->all();

            return ['ids' => $ids, 'scores' => array_fill_keys($ids, 0.0), 'total' => count($ids)];
        }

        // Capped candidate set for scoring (LIKE prefilter + levenshtein).
        $candidates = (clone $base)->limit(500)->get([
            'id', 'name', 'slug', 'tags', 'barcode', 'unit_price', 'num_of_sale', 'rating', 'created_at', 'category_id', 'brand_id',
        ]);

        $catNames = $this->mapNames(\App\Models\Category::class, $candidates->pluck('category_id')->filter()->unique()->all());
        $brandNames = $this->mapNames(\App\Models\Brand::class, $candidates->pluck('brand_id')->filter()->unique()->all());

        $scored = [];
        foreach ($candidates as $p) {
            $score = $this->score($p, $tokens, $catNames[$p->category_id] ?? '', $brandNames[$p->brand_id] ?? '');
            if ($score > 0) {
                $scored[$p->id] = $score;
            }
        }

        if ($sort !== 'relevance') {
            $rows = $candidates->whereIn('id', array_keys($scored));
            $rows = $this->sortCollection($rows, $sort);
            $ids = $rows->take($limit)->pluck('id')->all();
        } else {
            arsort($scored);
            $ids = array_slice(array_keys($scored), 0, $limit);
        }

        return ['ids' => $ids, 'scores' => $scored, 'total' => count($scored)];
    }

    public function suggest(string $query, int $limit = 8): array
    {
        $q = trim($query);
        if (strlen($q) < 2) {
            return [];
        }

        return Product::published()->approved()
            ->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('tags', 'like', "%{$q}%"))
            ->orderBy('num_of_sale', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'thumbnail_img', 'unit_price'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'thumbnail_url' => $p->thumbnail_img ? asset($p->thumbnail_img) : null,
                'price_formatted' => 'Rp ' . number_format((float) $p->unit_price, 0, ',', '.'),
            ])->all();
    }

    protected function applyFilters(mixed $query, array $filters): void
    {
        if (!empty($filters['category'])) {
            $slug = $filters['category'];
            $query->where(fn ($q) => $q->whereHas('categories', fn ($qq) => $qq->where('slug', $slug))
                ->orWhereHas('category', fn ($qq) => $qq->where('slug', $slug)));
        }
        if (!empty($filters['brand'])) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $filters['brand']));
        }
        if (isset($filters['min_price'])) {
            $query->where('unit_price', '>=', (int) $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('unit_price', '<=', (int) $filters['max_price']);
        }
        if (!empty($filters['in_stock']) && Schema::hasTable('product_stocks')) {
            $query->whereHas('stocks', fn ($q) => $q->where('qty', '>', 0));
        }
    }

    protected function applySort(mixed $query, string $sort): mixed
    {
        return match ($sort) {
            'cheapest' => $query->orderBy('unit_price', 'asc'),
            'expensive' => $query->orderBy('unit_price', 'desc'),
            'popular' => $query->orderBy('num_of_sale', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            default => $query->latest(),
        };
    }

    protected function sortCollection(mixed $rows, string $sort): mixed
    {
        return match ($sort) {
            'cheapest' => $rows->sortBy(fn ($p) => (float) $p->unit_price),
            'expensive' => $rows->sortByDesc(fn ($p) => (float) $p->unit_price),
            'popular' => $rows->sortByDesc(fn ($p) => (int) $p->num_of_sale),
            'rating' => $rows->sortByDesc(fn ($p) => (float) $p->rating),
            'latest' => $rows->sortByDesc(fn ($p) => (string) $p->created_at),
            default => $rows,
        };
    }

    protected function mapNames(string $class, array $ids): array
    {
        if ($ids === [] || !class_exists($class)) {
            return [];
        }
        try {
            return $class::whereIn('id', $ids)->pluck('name', 'id')->all();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Relevance: SKU/barcode exact = 100; name exact word = 20/token;
     * name substring = 10; category/brand = 6; tags = 4; slug = 3;
     * levenshtein<=2 on name words = 5 (typo tolerance).
     */
    protected function score(mixed $p, array $tokens, string $catName, string $brandName): float
    {
        $score = 0.0;
        $nameLower = strtolower((string) $p->name);
        $nameWords = preg_split('/\s+/', $nameLower, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($tokens as $tok) {
            if ($tok === '') {
                continue;
            }
            if (strtolower((string) ($p->barcode ?? '')) === $tok || strtolower((string) $p->slug) === $tok) {
                $score += 100;
                continue;
            }
            if (in_array($tok, $nameWords, true)) {
                $score += 20;
            } elseif (str_contains($nameLower, $tok)) {
                $score += 10;
            } else {
                foreach ($nameWords as $w) {
                    if (strlen($tok) >= 4 && strlen($w) >= 4 && levenshtein($tok, $w) <= 2) {
                        $score += 5;
                        break;
                    }
                }
            }
            if ($catName !== '' && str_contains(strtolower($catName), $tok)) {
                $score += 6;
            }
            if ($brandName !== '' && str_contains(strtolower($brandName), $tok)) {
                $score += 6;
            }
            if (str_contains(strtolower((string) ($p->tags ?? '')), $tok)) {
                $score += 4;
            }
            if (str_contains(strtolower((string) $p->slug), $tok)) {
                $score += 3;
            }
        }

        return $score;
    }
}
