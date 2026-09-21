<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('children')
            ->topLevel()
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::topLevel()->orderBy('sort_order')->get();

        return view('admin.categories.form', [
            'category' => new Category,
            'parents' => $parents,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['image'] = $this->storeImage($request);
        $data['is_active'] = $request->boolean('is_active');

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', __('admin.category_created'));
    }

    public function edit(Category $category): View
    {
        $parents = Category::topLevel()->where('id', '!=', $category->id)->orderBy('sort_order')->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($image = $this->storeImage($request)) {
            if ($category->image) {
                app(ImageUploadService::class)->delete($category->image);
            }
            $data['image'] = $image;
        }

        $data['is_active'] = $request->boolean('is_active');

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', __('admin.category_updated'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists() || $category->products()->exists()) {
            return back()->withErrors(['category' => __('admin.category_delete_blocked')]);
        }

        if ($category->image) {
            app(ImageUploadService::class)->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', __('admin.category_deleted'));
    }

    private function storeImage(StoreCategoryRequest|UpdateCategoryRequest $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return app(ImageUploadService::class)->store($request->file('image'), 'categories');
    }
}
