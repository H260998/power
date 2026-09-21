<?php

namespace App\Http\Controllers\Storefront;

use App\Enums\TrackingEventName;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\MetaConversionApiService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug, MetaConversionApiService $tracking): View
    {
        $product = Product::with(['category', 'images.color', 'sizes', 'colors', 'variants.size', 'variants.color'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $similarProducts = Product::with(['category', 'images', 'variants'])
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(3)
            ->get();

        $eventId = $tracking->track(TrackingEventName::ViewContent, [
            'content_ids' => [$product->id],
            'content_name' => $product->getTranslation('name', 'en', false),
            'value' => (float) $product->price,
            'currency' => 'TND',
        ], product: $product);

        return view('storefront.product-show', compact('product', 'similarProducts', 'eventId'));
    }
}
