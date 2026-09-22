<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images', 'variants'])->active();

        $activeCategory = null;

        if ($request->filled('category')) {
            $activeCategory = Category::where('slug', $request->string('category'))->first();

            if ($activeCategory) {
                $categoryIds = $activeCategory->children->isNotEmpty()
                    ? $activeCategory->children->pluck('id')->push($activeCategory->id)
                    : collect([$activeCategory->id]);

                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($request->filled('size')) {
            $query->whereHas('sizes', fn ($q) => $q->where('label', $request->string('size')));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->float('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->float('price_max'));
        }

        if ($request->filled('q')) {
            $query->searchName($request->string('q')->toString());
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        $products = $query->paginate(9)->withQueryString();

        $categories = Category::with('children')->topLevel()->active()->orderBy('sort_order')->get();
        $sizes = ProductSize::select('label')->distinct()->orderBy('label')->pluck('label');

        return view('storefront.boutique', compact('products', 'categories', 'sizes', 'activeCategory'));
    }
}
