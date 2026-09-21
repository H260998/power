<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SettingsService $settings): View
    {
        $featuredProducts = Product::with(['category', 'images', 'variants.size', 'variants.color'])
            ->active()
            ->featured()
            ->latest()
            ->take(4)
            ->get();

        if ($featuredProducts->count() < 4) {
            $featuredProducts = Product::with(['category', 'images', 'variants.size', 'variants.color'])
                ->active()
                ->latest()
                ->take(4)
                ->get();
        }

        $heroSlides = Product::with('images')
            ->active()
            ->where('is_on_podium', true)
            ->whereHas('images')
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product) => [
                'image' => $product->primaryImage()->url(),
                'name' => $product->name,
                'url' => route('shop.product', $product->slug),
                'scale' => $product->slug === 'veste-teddy-noire-blanche-boston' ? 1.3 : 1,
            ])
            ->values();

        return view('storefront.home', [
            'featuredProducts' => $featuredProducts,
            'heroSlides' => $heroSlides,
            'settings' => $settings->all(),
        ]);
    }
}
