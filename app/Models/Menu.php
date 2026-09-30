<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Translatable;

class Menu extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['label'];
    protected $fillable = [
        'location', 'label', 'url', 'icon',
        'badge_text', 'badge_color', 'visibility', 'language_code', 'mega_menu',
        'parent_id', 'sort_order', 'is_active', 'open_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
        'mega_menu' => 'boolean',
        'visibility' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }

    public function scopeLocation(Builder $q, string $location): void
    {
        $q->where('location', $location);
    }

    public static function forLocation(string $location, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        $items = static::active()->location($location)->orderBy('sort_order')->get();

        // Language-specific fallback: menu for locale X else default (null language_code).
        $scoped = $items->filter(fn (Menu $m) => $m->language_code === null || $m->language_code === $locale);
        if ($scoped->isEmpty()) {
            $scoped = $items;
        }

        // Optional visibility filter: ['guest' => true] / ['auth' => true] / ['roles' => [...]].
        return $scoped->filter(fn (Menu $m) => $m->isVisibleFor(auth()->user()))->values();
    }

    /**
     * Visibility rules stored in `visibility` json, e.g.
     * ['guest' => true] (guests only), ['auth' => true] (logged-in only),
     * ['roles' => ['admin','seller']]. Null/empty = visible to all.
     */
    public function isVisibleFor($user): bool
    {
        $rules = $this->visibility;
        if (empty($rules) || ! is_array($rules)) {
            return true;
        }
        // Normalize both assoc (['auth' => true]) and list (['auth']) formats.
        $flags = [];
        foreach ($rules as $k => $v) {
            if (is_int($k)) {
                $flags[$v] = true;
            } else {
                $flags[$k] = (bool) $v;
            }
        }
        if (! empty($flags['guest']) && $user) {
            return false;
        }
        if (! empty($flags['auth']) && ! $user) {
            return false;
        }
        if (! empty($flags['roles']) && is_array($flags['roles'])) {
            $role = $user->user_type ?? $user->role ?? null;
            if (! in_array($role, $flags['roles'], true)) {
                return false;
            }
        }

        return true;
    }
}
