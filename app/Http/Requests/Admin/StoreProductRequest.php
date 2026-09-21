<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'array'],
            'name.fr' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')],
            'description' => ['nullable', 'array'],
            'description.fr' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_on_podium' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'sizes' => ['nullable', 'string', 'max:500'],
            'colors' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ];
    }
}
