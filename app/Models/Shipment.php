<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    public const STATUSES = ['packed', 'shipped', 'delivered'];

    protected $fillable = [
        'order_id', 'courier', 'tracking_number', 'status',
        'shipped_at', 'delivered_at', 'note',
    ];

    protected $casts = ['shipped_at' => 'datetime', 'delivered_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    protected static function booted(): void
    {
        static::created(fn (self $shipment) => $shipment->syncToOrder());
        static::updated(fn (self $shipment) => $shipment->syncToOrder());
    }

    public function syncToOrder(): void
    {
        $order = $this->order()->first();
        if (! $order) {
            return;
        }

        $updates = [];
        if ($this->tracking_number) {
            $updates['tracking_number'] = $this->tracking_number;
        }
        if ($this->courier) {
            $updates['courier'] = $this->courier;
        }
        if ($updates !== []) {
            $order->update($updates);
        }

        $statusLabel = match ($this->status) {
            'packed' => 'Paket dikemas',
            'shipped' => 'Paket dikirim'.($this->tracking_number ? ' — resi '.$this->tracking_number : ''),
            'delivered' => 'Paket diterima',
            default => $this->status,
        };

        $already = $order->deliveryHistories()
            ->where('delivery_status', 'shipment_'.$this->status)
            ->where('note', $this->tracking_number ?? '')
            ->exists();

        if (! $already) {
            $order->deliveryHistories()->create([
                'order_detail_id' => $this->items()->first()?->order_detail_id,
                'delivery_boy_id' => null,
                'delivery_status' => 'shipment_'.$this->status,
                'status' => $statusLabel,
                'note' => $this->tracking_number,
            ]);
        }
    }
}
