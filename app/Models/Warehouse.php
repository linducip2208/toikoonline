<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['code', 'name', 'city', 'area', 'address', 'is_default', 'is_active'];

    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean'];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public static function defaultWarehouse(): self
    {
        return static::firstOrCreate(
            ['code' => 'DEFAULT'],
            ['name' => 'Gudang Utama', 'is_default' => true, 'is_active' => true]
        );
    }
}
