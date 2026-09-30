<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{
    protected $fillable = [
        'location', 'label', 'url', 'icon',
        'parent_id', 'sort_order', 'is_active', 'open_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', true);
    }

    public function scopeLocation(Builder $q, string $location): void
    {
        $q->where('location', $location);
    }

    public static function forLocation(string $location)
    {
        return static::active()->location($location)->orderBy('sort_order')->get();
    }
}
