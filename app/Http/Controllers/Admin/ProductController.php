<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ImageUploadService $images,
        private readonly StockService $stock,
    ) {}

    public function index(Request $request): View
    {
        $products = Product::with(['category', 'variants'])
            ->when($request->filled('q'), fn ($q) => $q->searchName($request->string('q')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::flatForSelect();

        return view('admin.products.form', [
            'product' => new Product,
            'categories' => $categories,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->only([
            'category_id', 'name', 'slug', 'description', 'price', 'compare_at_price',
            'sku', 'meta_title', 'meta_description',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_on_podium'] = $request->boolean('is_on_podium');

        $product = Product::create($data);

        foreach ($this->parseSizes($request->string('sizes', '')) as $order => $label) {
            $product->sizes()->create(['label' => $label, 'sort_order' => $order]);
        }

        foreach ($this->parseColors($request->string('colors', '')) as $order => $color) {
            $product->colors()->create([...$color, 'sort_order' => $order]);
        }

        $this->stock->syncVariants($product->fresh(['sizes', 'colors']));

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', __('admin.product_created'));
    }

    public function edit(Product $product): View
    {
        $product->load(['sizes', 'colors', 'images.color', 'variants.size', 'variants.color']);
        $categories = Category::flatForSelect();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->only([
            'category_id', 'name', 'slug', 'description', 'price', 'compare_at_price',
            'sku', 'meta_title', 'meta_description',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_on_podium'] = $request->boolean('is_on_podium');

        $product->update($data);

        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', __('admin.product_updated'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            $this->images->delete($image->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', __('admin.product_deleted'));
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $nextOrder = (int) $product->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $nextOrder++;
            $product->images()->create([
                'path' => $this->images->store($file, 'products'),
                'sort_order' => $nextOrder,
                'is_primary' => ! $hasPrimary,
            ]);
            $hasPrimary = true;
        }
    }

    private function parseSizes(string $sizes): array
    {
        return collect(explode(',', $sizes))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function parseColors(string $colors): array
    {
        return collect(explode(',', $colors))
            ->map(fn ($c) => trim($c))
            ->filter()
            ->map(function ($c) {
                [$name, $hex] = array_pad(explode(':', $c, 2), 2, null);

                return ['name' => trim($name), 'hex_code' => $hex ? trim($hex) : null];
            })
            ->values()
            ->all();
    }
}
