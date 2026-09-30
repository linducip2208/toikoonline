<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id', 'gateway_id', 'order_code', 'amount', 'currency',
        'status', 'idempotency_key', 'gateway_reference', 'redirect_url',
        'raw', 'failed_signature_count',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'raw' => 'array',
            'failed_signature_count' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGatewayConfig::class, 'gateway_id');
    }

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }
}
