<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public const TYPES = [
        'receive', 'adjust', 'transfer_in', 'transfer_out',
        'reserve', 'release', 'commit', 'sale', 'return',
    ];

    protected $fillable = [
        'warehouse_id', 'product_id', 'stock_id', 'type',
        'qty', 'qty_after', 'ref_type', 'ref_id', 'note', 'user_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(ProductStock::class, 'stock_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
