<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicPopup extends Model
{
    protected $fillable = [
        'status',
        'title',
        'summary',
        'banner',
        'btn_link',
        'btn_text',
        'btn_text_color',
        'btn_background_color',
        'show_subscribe_form',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
