<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_original_name',
        'file_name',
        'user_id',
        'folder_id',
        'file_size',
        'extension',
        'type',
        'external_link',
        'alt_text',
        'caption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}
