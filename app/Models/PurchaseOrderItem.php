<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'stock_id',
        'qty_ordered', 'qty_received', 'unit_cost',
    ];

    protected $casts = ['unit_cost' => 'decimal:2'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(ProductStock::class, 'stock_id');
    }
}
