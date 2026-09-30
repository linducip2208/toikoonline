<?php

namespace App\Services\Search;

/**
 * Scout-ready search driver contract. Default binding: DatabaseDriver
 * (zero-dependency LIKE + relevance scoring). Swap to Algolia/Meilisearch
 * by binding this interface to a new driver — see docs/MARKETING.md? no:
 * docs/SEO.md? Search swap documented in docs/MARKETING.md under Search.
 */
interface SearchDriverInterface
{
    /**
     * @return array{ids:array<int>, scores:array<int,float>, total:int}
     */
    public function search(string $query, array $filters = [], string $sort = 'relevance', int $limit = 50): array;

    /** Lightweight suggestion rows for autocomplete. */
    public function suggest(string $query, int $limit = 8): array;
}
