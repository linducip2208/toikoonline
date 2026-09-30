<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GlobalBlock extends Model
{
    protected $fillable = [
        'key',
        'title',
        'blocks',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Inner blocks array (same Builder schema as pages.blocks).
     */
    public function innerBlocks(): array
    {
        return is_array($this->blocks) ? $this->blocks : [];
    }
}
