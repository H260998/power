<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'stock' => ['required', 'array'],
            'stock.*' => ['required', 'integer', 'min:0'],
        ]);

        $variantIds = $product->variants()->pluck('id')->all();
        $stocks = collect($request->input('stock'))
            ->only($variantIds)
            ->all();

        $this->stock->updateStock($stocks);

        return back()->with('status', __('admin.stock_updated'));
    }
}
