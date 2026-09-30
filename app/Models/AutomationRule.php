<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    public const OPERATORS = ['=', '!=', '>', '>=', '<', '<=', 'contains'];

    public const ACTION_KINDS = ['notify', 'email', 'coupon', 'webhook'];

    protected $fillable = [
        'name', 'event', 'conditions', 'actions', 'is_active', 'last_run_at',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'actions' => 'array',
            'is_active' => 'boolean',
            'last_run_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeForEvent(Builder $query, string $event): void
    {
        $query->where('event', $event);
    }
}
