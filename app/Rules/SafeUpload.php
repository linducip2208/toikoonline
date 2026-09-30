<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Blocks dangerous uploads: php/exe/sh/bat/com/msi, svg with script/event handlers,
 * and double extensions (e.g. photo.jpg.php). Validates by extension + mime sniff.
 */
class SafeUpload implements ValidationRule
{
    public const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
        'exe', 'msi', 'com', 'bat', 'cmd', 'sh', 'bash', 'zsh',
        'py', 'pl', 'rb', 'jar', 'dll', 'so', 'dmg', 'apk',
        'html', 'htm', 'xhtml', 'js', 'swf',
    ];

    public const ALLOWED_MIMES = [
        // images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
        'image/svg+xml',
        // video / audio
        'video/mp4', 'video/webm', 'video/ogg',
        'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/webm',
        // docs
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv',
        // archives
        'application/zip',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = $value;
        if (is_string($file)) {
            // Already-stored path (edit form with existing value) — nothing to check.
            if ($this->hasTraversal($file)) {
                $fail('Path file tidak valid.');
            }

            return;
        }

        if (! $file || ! method_exists($file, 'getClientOriginalName')) {
            return;
        }

        $original = (string) $file->getClientOriginalName();
        $parts = array_map('strtolower', explode('.', $original));

        foreach ($parts as $part) {
            if (in_array($part, static::BLOCKED_EXTENSIONS, true)) {
                $fail('Jenis file :ext tidak diizinkan.');

                return;
            }
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (in_array($ext, static::BLOCKED_EXTENSIONS, true)) {
            $fail('Jenis file tidak diizinkan.');

            return;
        }

        if ($this->hasTraversal($original)) {
            $fail('Nama file tidak valid.');

            return;
        }

        try {
            $mime = (string) $file->getMimeType();
        } catch (\Throwable) {
            $mime = '';
        }

        if ($mime !== '' && ! in_array($mime, static::ALLOWED_MIMES, true)) {
            $fail('Tipe MIME file (:mime) tidak diizinkan.');

            return;
        }

        // SVG may embed scripts — reject script tags / event-handler attributes.
        if ($ext === 'svg' || $mime === 'image/svg+xml') {
            try {
                $contents = (string) file_get_contents($file->getRealPath());
            } catch (\Throwable) {
                $contents = '';
            }
            if ($contents !== '' && preg_match('/<\s*script\b|on\w+\s*=/i', $contents)) {
                $fail('File SVG mengandung script dan tidak diizinkan.');
            }
        }
    }

    protected function hasTraversal(string $path): bool
    {
        return str_contains($path, '..') || str_contains($path, '/') && preg_match('#(^|/)\.\.(/|$)#', $path) === 1;
    }
}
