<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'code',
        'user_id',
        'subject',
        'details',
        'files',
        'status',
        'viewed',
        'client_viewed',
    ];

    protected function casts(): array
    {
        return [
            'viewed' => 'boolean',
            'client_viewed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
