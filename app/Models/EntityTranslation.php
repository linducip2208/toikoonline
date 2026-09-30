<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityTranslation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'tkey',
        'tvalue',
        'status',
    ];

    protected $casts = [
        'translatable_id' => 'integer',
    ];

    public function translatable()
    {
        return $this->morphTo();
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }
}
