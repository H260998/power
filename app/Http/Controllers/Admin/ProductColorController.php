<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'hex_code' => ['nullable', 'string', 'max:9'],
        ]);

        $nextOrder = (int) $product->colors()->max('sort_order') + 1;

        $product->colors()->firstOrCreate(
            ['name' => $request->string('name')],
            ['hex_code' => $request->string('hex_code') ?: null, 'sort_order' => $nextOrder]
        );

        $this->stock->syncVariants($product->fresh(['sizes', 'colors']));

        return back()->with('status', __('admin.color_added'));
    }

    public function destroy(Product $product, ProductColor $color): RedirectResponse
    {
        abort_unless($color->product_id === $product->id, 404);

        $color->delete();

        return back()->with('status', __('admin.color_removed'));
    }
}
