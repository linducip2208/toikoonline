<?php

namespace App\Services\Seo;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IndexNowService
{
    protected string $key;

    protected array $endpoints = [
        'https://api.indexnow.org/indexnow',
        'https://www.bing.com/indexnow',
        'https://search.seznam.cz/indexnow',
        'https://search.yandex.com/indexnow',
        'https://search.naver.com/indexnow',
    ];

    public function __construct()
    {
        $this->key = $this->getKey();
    }

    public function submit(string|array $urls): void
    {
        $urls = is_array($urls) ? $urls : [$urls];
        $siteUrl = config('app.url');
        $fullUrls = array_map(fn ($url) => $siteUrl . '/' . ltrim($url, '/'), $urls);

        $submittedKey = 'indexnow_submitted_' . md5(implode(',', $fullUrls));
        if (Cache::has($submittedKey)) {
            return;
        }

        foreach ($this->endpoints as $endpoint) {
            try {
                Http::timeout(10)->post($endpoint, [
                    'host' => parse_url($siteUrl, PHP_URL_HOST),
                    'key' => $this->key,
                    'keyLocation' => $siteUrl . '/indexnow-key.txt',
                    'urlList' => $fullUrls,
                ]);
            } catch (\Exception $e) {
                //
            }
        }

        Cache::put($submittedKey, true, now()->addDays(30));
    }

    protected function getKey(): string
    {
        $keyPath = public_path('indexnow-key.txt');
        if (! file_exists($keyPath)) {
            $key = bin2hex(random_bytes(16));
            file_put_contents($keyPath, $key);
            return $key;
        }
        return trim(file_get_contents($keyPath));
    }

    public static function submitUrl(string $url): void
    {
        (new static())->submit($url);
    }

    public static function submitUrls(array $urls): void
    {
        (new static())->submit($urls);
    }
}
