<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'identifier',
        'email_type',
        'subject',
        'default_text',
        'status',
        'is_status_changeable',
        'is_default_text_editable',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_status_changeable' => 'boolean',
            'is_default_text_editable' => 'boolean',
        ];
    }
}
