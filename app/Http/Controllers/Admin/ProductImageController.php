<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(private readonly ImageUploadService $images) {}

    public function update(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $request->validate([
            'product_color_id' => ['nullable', 'integer', 'exists:product_colors,id'],
        ]);

        if ($request->boolean('make_primary')) {
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        }

        if ($request->has('product_color_id')) {
            $image->update(['product_color_id' => $request->input('product_color_id') ?: null]);
        }

        return back()->with('status', __('admin.image_updated'));
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $this->images->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', __('admin.image_deleted'));
    }
}
