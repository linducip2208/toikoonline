<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Product;
use App\Services\Seo\IndexNowService;
use Illuminate\Console\Command;

class IndexNowSubmit extends Command
{
    protected $signature = 'seo:indexnow {--limit=100}';
    protected $description = 'Submit recent URLs to IndexNow';

    public function handle(): void
    {
        $urls = [];

        Product::published()->approved()
            ->where('created_at', '>=', now()->subDay())
            ->limit(50)
            ->get()
            ->each(fn ($p) => $urls[] = "products/{$p->slug}");

        Blog::published()
            ->where('created_at', '>=', now()->subDay())
            ->limit(30)
            ->get()
            ->each(fn ($b) => $urls[] = "blog/{$b->slug}");

        if (empty($urls)) {
            $this->info('No new URLs to submit.');
            return;
        }

        $this->info('Submitting ' . count($urls) . ' URLs...');
        IndexNowService::submitUrls($urls);
        $this->info('Done.');
    }
}
