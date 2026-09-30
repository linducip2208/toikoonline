<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Page;
use Illuminate\Console\Command;

/**
 * Report-only broken internal link checker for pages + blogs.
 * Prints dead internal links; never modifies content.
 */
class BrokenLinkCheck extends Command
{
    protected $signature = 'seo:broken-links {--limit=500 : Max records to scan}';

    protected $description = 'Report broken internal links in pages and blogs';

    public function handle(): int
    {
        $base = rtrim(config('app.url'), '/');
        $broken = 0;
        $checked = 0;

        $records = Page::where('status', true)->limit((int) $this->option('limit'))->get(['id', 'slug', 'content'])
            ->map(fn ($p) => ['kind' => 'page', 'ref' => $p->slug, 'html' => (string) $p->content])
            ->concat(
                Blog::published()->limit((int) $this->option('limit'))->get(['id', 'slug', 'content'])
                    ->map(fn ($b) => ['kind' => 'blog', 'ref' => $b->slug, 'html' => (string) $b->content])
            );

        foreach ($records as $rec) {
            foreach ($this->internalLinks((string) $rec['html'], $base) as $link) {
                $checked++;
                if (!$this->resolves($link)) {
                    $broken++;
                    $this->warn("[{$rec['kind']}:{$rec['ref']}] broken: {$link}");
                }
            }
        }

        $this->info("Checked {$checked} internal links, {$broken} broken.");

        return self::SUCCESS;
    }

    protected function internalLinks(string $html, string $base): array
    {
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $m);
        $out = [];

        foreach ($m[1] as $href) {
            if (str_starts_with($href, $base . '/')) {
                $out[] = $href;
            } elseif (str_starts_with($href, '/') && !str_starts_with($href, '//')) {
                $out[] = $base . $href;
            }
        }

        return array_values(array_unique($out));
    }

    protected function resolves(string $url): bool
    {
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(10)->head($url);

            return $res->status() < 400;
        } catch (\Exception) {
            return false;
        }
    }
}
