<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\PurchaseBatch;

class InventoryService
{
    public function addPurchaseBatch(PurchaseBatch $batch): PurchaseBatch
    {
        $productBranch = ProductBranch::firstOrCreate([
            'product_id' => $batch->product_id,
            'branch_id' => $batch->branch_id,
        ], [
            'stock' => 0,
            'selling_price' => 0,
            'margin_percent' => 0,
            'is_active' => true,
        ]);

        $stockBefore = $productBranch->stock;
        $productBranch->stock += $batch->quantity;
        $productBranch->save();

        InventoryTransaction::create([
            'branch_id' => $batch->branch_id,
            'product_id' => $batch->product_id,
            'related_id' => $batch->id,
            'related_type' => PurchaseBatch::class,
            'quantity' => $batch->quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $productBranch->stock,
            'type' => 'purchase',
            'notes' => 'FIFO batch received',
        ]);

        return $batch;
    }

    public function registerSaleItem(Product $product, Branch $branch, int $quantity): void
    {
        $productBranch = ProductBranch::where('product_id', $product->id)
            ->where('branch_id', $branch->id)
            ->first();

        if (! $productBranch) {
            throw new \RuntimeException('Product not available for this branch.');
        }

        $stockBefore = $productBranch->stock;
        $productBranch->stock -= $quantity;
        $productBranch->save();

        InventoryTransaction::create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'quantity' => -$quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $productBranch->stock,
            'type' => 'sale',
            'notes' => 'Inventory adjustment from sale',
        ]);
    }
}
