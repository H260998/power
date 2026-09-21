<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * Ensure a ProductVariant row exists for every size x color combination of the product.
     * Missing combinations are created with zero stock; existing ones are left untouched.
     */
    public function syncVariants(Product $product): void
    {
        $sizeIds = $product->sizes()->pluck('id');
        $colorIds = $product->colors()->pluck('id');

        foreach ($sizeIds as $sizeId) {
            foreach ($colorIds as $colorId) {
                ProductVariant::firstOrCreate([
                    'product_id' => $product->id,
                    'product_size_id' => $sizeId,
                    'product_color_id' => $colorId,
                ], [
                    'stock_quantity' => 0,
                ]);
            }
        }
    }

    /**
     * Bulk-set stock quantities from the admin grid, keyed by variant id.
     */
    public function updateStock(array $variantStocks): void
    {
        DB::transaction(function () use ($variantStocks) {
            foreach ($variantStocks as $variantId => $quantity) {
                ProductVariant::whereKey($variantId)->update(['stock_quantity' => max(0, (int) $quantity)]);
            }
        });
    }

    /**
     * Decrement stock for a single variant inside a row lock, refusing to oversell.
     */
    public function decrement(int $variantId, int $quantity): void
    {
        DB::transaction(function () use ($variantId, $quantity) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);

            if ($variant->stock_quantity < $quantity) {
                throw new RuntimeException("Insufficient stock for variant {$variantId}.");
            }

            $variant->decrement('stock_quantity', $quantity);
        });
    }

    public function lowStockVariants(int $threshold = self::LOW_STOCK_THRESHOLD): Collection
    {
        return ProductVariant::with(['product', 'size', 'color'])
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity')
            ->get();
    }
}
