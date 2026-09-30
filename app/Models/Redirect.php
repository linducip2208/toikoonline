<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from_path', 'to_path', 'code', 'hits', 'is_active'];

    protected $casts = [
        'code' => 'integer',
        'hits' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function normalize(string $path): string
    {
        $path = trim($path);
        if ($path === '' || $path === '/') {
            return '/';
        }
        $path = '/'.ltrim($path, '/');

        return rtrim($path, '/') === '' ? '/' : rtrim($path, '/');
    }
}
