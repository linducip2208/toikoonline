<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'payment_transaction_id', 'gateway_id_raw', 'event',
        'signature_valid', 'mapped_status', 'gateway_status_raw', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
