<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use App\Services\Seo\IndexNowService;
use Illuminate\Console\Command;

class SitemapGenerate extends Command
{
    protected $signature = 'sitemap:generate {--ping : Ping search engines after generating}';

    protected $description = 'Generate public/sitemap*.xml + hreflang (id/en) index from DB';

    public function handle(IndexNowService $indexNow): int
    {
        $urls = SitemapController::urlSet();

        $chunks = array_chunk($urls, 2000);
        $indexParts = [];

        foreach ($chunks as $i => $chunk) {
            $n = $i + 1;
            $file = "sitemap-{$n}.xml";
            file_put_contents(public_path($file), $this->renderUrls($chunk));
            $indexParts[] = $file;
            $this->info("Wrote {$file} (" . count($chunk) . ' urls)');
        }

        file_put_contents(public_path('sitemap.xml'), $this->renderIndex($indexParts));
        $this->info('Wrote sitemap.xml index (' . count($indexParts) . ' parts)');

        if ($this->option('ping')) {
            $indexNow->pingSitemap(rtrim(config('app.url'), '/') . '/sitemap.xml');
            $this->info('Pinged search engines.');
        }

        return self::SUCCESS;
    }

    protected function renderUrls(array $chunk): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($chunk as $u) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . e($u['url']) . '</loc>' . "\n";
            foreach ($u['alternates'] ?? [] as $locale => $href) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . e($locale) . '" href="' . e($href) . '"/>' . "\n";
            }
            $xml .= '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . e($u['changefreq']) . '</changefreq>' . "\n";
            $xml .= '    <priority>' . e($u['priority']) . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        return $xml . '</urlset>' . "\n";
    }

    protected function renderIndex(array $parts): string
    {
        $base = rtrim(config('app.url'), '/');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($parts as $file) {
            $xml .= '  <sitemap><loc>' . e($base . '/' . $file) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod></sitemap>' . "\n";
        }

        return $xml . '</sitemapindex>' . "\n";
    }
}
