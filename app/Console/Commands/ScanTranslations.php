<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanTranslations extends Command
{
    protected $signature = 'translations:scan {--sync : Insert missing keys into translations table for active languages}';

    protected $description = 'Find __()/trans() keys missing in lang files or translations table';

    public function handle(): int
    {
        $pattern = "/(?:__|trans|@lang)\(\s*['\"]([^'\"]+)['\"]/";
        $keys = [];
        foreach (File::allFiles(app_path()) as $f) {
            if ($f->getExtension() !== 'php') {
                continue;
            }
            preg_match_all($pattern, File::get($f->getPathname()), $m);
            foreach ($m[1] as $k) {
                $keys[$k] = true;
            }
        }
        foreach (File::allFiles(resource_path('views')) as $f) {
            if ($f->getExtension() !== 'php') {
                continue;
            }
            preg_match_all($pattern, File::get($f->getPathname()), $m);
            foreach ($m[1] as $k) {
                $keys[$k] = true;
            }
        }
        $keys = array_keys($keys);
        sort($keys);

        $idJson = $this->readJson(lang_path('id.json'));
        $enJson = $this->readJson(lang_path('en.json'));
        $dbKeys = Translation::pluck('lang_key')->unique()->all();

        $missing = [];
        foreach ($keys as $k) {
            $row = ['key' => $k, 'id_json' => array_key_exists($k, $idJson), 'en_json' => array_key_exists($k, $enJson), 'db' => in_array($k, $dbKeys, true)];
            if (! $row['id_json'] || ! $row['en_json'] || ! $row['db']) {
                $missing[] = $row;
            }
        }

        if ($missing === []) {
            $this->info('No missing translation keys. Total scanned: '.count($keys));

            return self::SUCCESS;
        }

        $this->table(['Key', 'id.json', 'en.json', 'DB'], array_map(fn ($r) => [$r['key'], $r['id_json'] ? 'ok' : 'MISS', $r['en_json'] ? 'ok' : 'MISS', $r['db'] ? 'ok' : 'MISS'], $missing));

        if ($this->option('sync')) {
            $langs = \App\Models\Language::where('status', true)->pluck('code')->all();
            if ($langs === []) {
                $langs = ['id', 'en'];
            }
            foreach ($missing as $r) {
                foreach ($langs as $lang) {
                    Translation::firstOrCreate(['lang' => $lang, 'lang_key' => $r['key']], ['lang_value' => $r['key']]);
                }
            }
            $this->info('Synced '.count($missing).' missing keys into translations table.');
        }

        return self::SUCCESS;
    }

    protected function readJson(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }
        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : [];
    }
}
