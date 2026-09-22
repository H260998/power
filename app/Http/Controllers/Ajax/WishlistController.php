<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function products(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['nullable', 'string', 'max:500', 'regex:/\A[0-9,]*\z/'],
        ]);

        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn (string $id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(50)
            ->all();

        $products = Product::with(['category', 'images', 'variants'])
            ->active()
            ->whereIn('id', $ids)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'category' => $product->category->name,
                'priceLabel' => number_format($product->price, 3).' '.__('storefront.currency'),
                'image' => $product->primaryImage() ? $product->primaryImage()->url() : null,
                'url' => route('shop.product', $product->slug),
                'variantId' => $product->defaultVariant()?->id,
            ]);

        return response()->json($products->values());
    }
}
