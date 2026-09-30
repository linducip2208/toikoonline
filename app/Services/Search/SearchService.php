<?php

namespace App\Services\Search;

/**
 * SearchService — driver facade. Default driver is DatabaseDriver;
 * swap via container binding on SearchDriverInterface (Algolia/Meilisearch).
 *
 * API: search($query, $filters, $sort, $limit) => {ids, scores, total}
 *      suggest($query, $limit) => lightweight rows
 *      tokenize($query) => lowercase ID/EN tokens (pure, unit-testable)
 */
class SearchService
{
    public function __construct(protected SearchDriverInterface $driver = new DatabaseDriver()) {}

    public function driver(): SearchDriverInterface
    {
        return $this->driver;
    }

    /**
     * Lowercase alphanumeric tokens; splits on non-letters/digits.
     * Pure helper.
     *
     * @return array<int,string>
     */
    public static function tokenize(string $query): array
    {
        $query = mb_strtolower(trim($query));
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique($parts));
    }

    public function search(string $query, array $filters = [], string $sort = 'relevance', int $limit = 50): array
    {
        return $this->driver->search($query, $filters, $sort, $limit);
    }

    public function suggest(string $query, int $limit = 8): array
    {
        return $this->driver->suggest($query, $limit);
    }
}
