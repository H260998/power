<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['label' => ['required', 'string', 'max:50']]);

        $nextOrder = (int) $product->sizes()->max('sort_order') + 1;

        $product->sizes()->firstOrCreate(
            ['label' => $request->string('label')],
            ['sort_order' => $nextOrder]
        );

        $this->stock->syncVariants($product->fresh(['sizes', 'colors']));

        return back()->with('status', __('admin.size_added'));
    }

    public function destroy(Product $product, ProductSize $size): RedirectResponse
    {
        abort_unless($size->product_id === $product->id, 404);

        $size->delete();

        return back()->with('status', __('admin.size_removed'));
    }
}
