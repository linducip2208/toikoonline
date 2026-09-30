<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    public const STATUSES = ['draft', 'ordered', 'partially_received', 'received', 'cancelled'];

    protected $fillable = [
        'code', 'supplier_id', 'warehouse_id', 'status',
        'subtotal', 'note', 'created_by', 'ordered_at', 'received_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'ordered_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receiveItem(PurchaseOrderItem $item, int $qty, ?int $userId = null): PurchaseOrderItem
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($item, $qty, $userId) {
            if ($item->purchase_order_id !== $this->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'item' => 'Item tidak termasuk PO ini.',
                ]);
            }
            if ($qty <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty' => 'Jumlah terima harus > 0.',
                ]);
            }
            if ($item->qty_received + $qty > $item->qty_ordered) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty' => 'Melebihi sisa yang dipesan.',
                ]);
            }

            $stock = $item->stock_id
                ? \App\Models\ProductStock::where('id', $item->stock_id)->lockForUpdate()->firstOrFail()
                : null;

            if ($stock) {
                $stock->increment('qty', $qty);
            }

            $item->increment('qty_received', $qty);

            \App\Models\StockMovement::create([
                'warehouse_id' => $this->warehouse_id,
                'product_id' => $item->product_id,
                'stock_id' => $item->stock_id,
                'type' => 'receive',
                'qty' => $qty,
                'qty_after' => $stock ? (int) $stock->fresh()->qty : null,
                'ref_type' => 'purchase_order',
                'ref_id' => $this->id,
                'note' => 'Terima PO '.$this->code,
                'user_id' => $userId ?? auth()->id(),
            ]);

            $this->recomputeStatus();

            return $item->fresh();
        });
    }

    public function recomputeStatus(): string
    {
        $ordered = (int) $this->items()->sum('qty_ordered');
        $received = (int) $this->items()->sum('qty_received');

        if ($this->status === 'cancelled') {
            return $this->status;
        }

        if ($received <= 0) {
            $next = $this->status === 'draft' ? 'draft' : 'ordered';
        } elseif ($received >= $ordered && $ordered > 0) {
            $next = 'received';
        } else {
            $next = 'partially_received';
        }

        if ($next !== $this->status) {
            $this->update(['status' => $next] + ($next === 'received' ? ['received_at' => now()] : []));
        }

        return $this->fresh()->status;
    }
}
