<?php

namespace App\Services\Inventory;

use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function defaultWarehouse(): Warehouse
    {
        return Warehouse::defaultWarehouse();
    }

    protected function defaultWarehouseId(?int $warehouseId = null): int
    {
        if ($warehouseId) {
            return $warehouseId;
        }

        return $this->defaultWarehouse()->id;
    }

    protected function record(
        int $warehouseId,
        int $productId,
        ?int $stockId,
        string $type,
        int $qty,
        ?int $qtyAfter,
        ?string $refType = null,
        mixed $refId = null,
        ?string $note = null
    ): StockMovement {
        return StockMovement::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'stock_id' => $stockId,
            'type' => $type,
            'qty' => $qty,
            'qty_after' => $qtyAfter,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'note' => $note,
            'user_id' => auth()->id(),
        ]);
    }

    protected function lockStock(int $stockId): ProductStock
    {
        return ProductStock::where('id', $stockId)->lockForUpdate()->firstOrFail();
    }

    public function availableQty(int $productId, ?string $variation = null): int
    {
        $query = ProductStock::where('product_id', $productId);
        if ($variation !== null && $variation !== '') {
            $query->where('variant', $variation);
        }

        return (int) $query->sum('qty');
    }

    public function adjust(int $productId, ?int $stockId, int $qty, ?string $note = null, ?int $warehouseId = null): StockMovement
    {
        return DB::transaction(function () use ($productId, $stockId, $qty, $note, $warehouseId) {
            $wid = $this->defaultWarehouseId($warehouseId);

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                $newQty = (int) $stock->qty + $qty;
                if ($newQty < 0) {
                    throw ValidationException::withMessages([
                        'qty' => __('commerce.stock_negative'),
                    ]);
                }
                $stock->update(['qty' => $newQty]);
            } else {
                $newQty = null;
            }

            return $this->record($wid, $productId, $stockId, 'adjust', $qty, $newQty, null, null, $note);
        });
    }

    public function receive(int $productId, ?int $stockId, int $qty, ?string $refType = null, mixed $refId = null, ?string $note = null, ?int $warehouseId = null): StockMovement
    {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => __('commerce.qty_positive')]);
        }

        return DB::transaction(function () use ($productId, $stockId, $qty, $refType, $refId, $note, $warehouseId) {
            $wid = $this->defaultWarehouseId($warehouseId);
            $qtyAfter = null;

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                $qtyAfter = (int) $stock->qty + $qty;
                $stock->update(['qty' => $qtyAfter]);
            }

            return $this->record($wid, $productId, $stockId, 'receive', $qty, $qtyAfter, $refType, $refId, $note);
        });
    }

    public function reserve(int $productId, ?int $stockId, int $qty, ?string $refType = null, mixed $refId = null): StockMovement
    {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => __('commerce.qty_positive')]);
        }

        return DB::transaction(function () use ($productId, $stockId, $qty, $refType, $refId) {
            $wid = $this->defaultWarehouseId();
            $qtyAfter = null;

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                if ((int) $stock->qty < $qty) {
                    throw ValidationException::withMessages(['qty' => __('commerce.stock_insufficient')]);
                }
                $qtyAfter = (int) $stock->qty - $qty;
                $stock->update(['qty' => $qtyAfter]);
            }

            return $this->record($wid, $productId, $stockId, 'reserve', -$qty, $qtyAfter, $refType, $refId);
        });
    }

    public function release(int $productId, ?int $stockId, int $qty, ?string $refType = null, mixed $refId = null): StockMovement
    {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => __('commerce.qty_positive')]);
        }

        return DB::transaction(function () use ($productId, $stockId, $qty, $refType, $refId) {
            $wid = $this->defaultWarehouseId();
            $qtyAfter = null;

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                $qtyAfter = (int) $stock->qty + $qty;
                $stock->update(['qty' => $qtyAfter]);
            }

            return $this->record($wid, $productId, $stockId, 'release', $qty, $qtyAfter, $refType, $refId);
        });
    }

    public function commit(int $productId, ?int $stockId, int $qty, ?string $refType = null, mixed $refId = null): StockMovement
    {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => __('commerce.qty_positive')]);
        }

        return DB::transaction(function () use ($productId, $stockId, $qty, $refType, $refId) {
            $wid = $this->defaultWarehouseId();
            $qtyAfter = null;

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                if ((int) $stock->qty < $qty) {
                    throw ValidationException::withMessages(['qty' => __('commerce.stock_insufficient')]);
                }
                $qtyAfter = (int) $stock->qty - $qty;
                if ($qtyAfter < 0) {
                    throw ValidationException::withMessages(['qty' => __('commerce.stock_negative')]);
                }
                $stock->update(['qty' => $qtyAfter]);
            }

            return $this->record($wid, $productId, $stockId, 'commit', -$qty, $qtyAfter, $refType, $refId);
        });
    }

    public function transfer(int $productId, ?int $stockId, int $qty, int $fromWarehouseId, int $toWarehouseId, ?string $note = null): array
    {
        if ($qty <= 0) {
            throw ValidationException::withMessages(['qty' => __('commerce.qty_positive')]);
        }

        return DB::transaction(function () use ($productId, $stockId, $qty, $fromWarehouseId, $toWarehouseId, $note) {
            $qtyAfter = null;

            if ($stockId) {
                $stock = $this->lockStock($stockId);
                if ((int) $stock->qty < $qty) {
                    throw ValidationException::withMessages(['qty' => __('commerce.stock_insufficient')]);
                }
                $qtyAfter = (int) $stock->qty - $qty;
                $stock->update(['qty' => $qtyAfter]);
            }

            $out = $this->record($fromWarehouseId, $productId, $stockId, 'transfer_out', -$qty, $qtyAfter, 'warehouse', $toWarehouseId, $note);
            $in = $this->record($toWarehouseId, $productId, $stockId, 'transfer_in', $qty, null, 'warehouse', $fromWarehouseId, $note);

            return [$out, $in];
        });
    }
}
