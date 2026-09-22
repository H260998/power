@extends('layouts.admin')

@section('content')
    @php $isEdit = $product->exists; @endphp

    <div class="text-[28px] font-extrabold mb-1.5">{{ $isEdit ? __('admin.edit_product') : __('admin.add_product') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.nav_products') }}</div>

    @if ($errors->any())
        <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-3xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-8">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.category') }}</label>
                <select name="category_id" required class="w-full border border-border rounded-xl px-4 py-3 text-sm bg-white">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>
                            {{ $cat->parent_id ? '— ' : '' }}{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.category_slug') }}</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_fr') }}</label>
                <input type="text" name="name[fr]" value="{{ old('name.fr', $product->getTranslation('name', 'fr', false)) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_en') }}</label>
                <input type="text" name="name[en]" value="{{ old('name.en', $product->getTranslation('name', 'en', false)) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name[ar]" value="{{ old('name.ar', $product->getTranslation('name', 'ar', false)) }}" required dir="rtl" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.description_fr') }}</label>
                <textarea name="description[fr]" rows="3" class="w-full border border-border rounded-xl px-4 py-3 text-sm">{{ old('description.fr', $product->getTranslation('description', 'fr', false)) }}</textarea>
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.description_en') }}</label>
                <textarea name="description[en]" rows="3" class="w-full border border-border rounded-xl px-4 py-3 text-sm">{{ old('description.en', $product->getTranslation('description', 'en', false)) }}</textarea>
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.description_ar') }}</label>
                <textarea name="description[ar]" rows="3" dir="rtl" class="w-full border border-border rounded-xl px-4 py-3 text-sm">{{ old('description.ar', $product->getTranslation('description', 'ar', false)) }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.price') }} ({{ __('storefront.currency') }})</label>
                <input type="number" step="0.001" min="0" name="price" value="{{ old('price', $product->price) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.compare_at_price') }}</label>
                <input type="number" step="0.001" min="0" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.sku') }}</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        @unless ($isEdit)
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.initial_sizes') }}</label>
                    <input type="text" name="sizes" value="{{ old('sizes') }}" placeholder="S, M, L, XL" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                    <p class="text-[12px] text-muted mt-1">{{ __('admin.initial_sizes_help') }}</p>
                </div>
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.initial_colors') }}</label>
                    <input type="text" name="colors" value="{{ old('colors') }}" placeholder="Black:#111111, Olive:#556B2F" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                    <p class="text-[12px] text-muted mt-1">{{ __('admin.initial_colors_help') }}</p>
                </div>
            </div>
        @endunless

        <div class="mb-5">
            <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.images') }}</label>
            <input type="file" name="images[]" accept="image/*" multiple class="w-full border border-border rounded-xl px-4 py-2.5 text-sm">
        </div>

        <div class="flex flex-wrap gap-6 mb-3">
            <label class="flex items-center gap-2 text-[13px] font-semibold">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true)) class="rounded border-border">
                {{ __('admin.active') }}
            </label>
            <label class="flex items-center gap-2 text-[13px] font-semibold">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="rounded border-border">
                {{ __('admin.featured') }}
            </label>
            <label class="flex items-center gap-2 text-[13px] font-semibold">
                <input type="hidden" name="is_on_podium" value="0">
                <input type="checkbox" name="is_on_podium" value="1" @checked(old('is_on_podium', $product->is_on_podium)) aria-describedby="podium-help" class="rounded border-border">
                {{ __('admin.on_podium') }}
            </label>
        </div>
        <p id="podium-help" class="text-[12px] text-muted mb-7">{{ __('admin.on_podium_help') }}</p>

        <div class="flex gap-3">
            <button type="submit" class="bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.products.index') }}" class="px-7 py-3.5 rounded-full text-sm font-bold border border-border">{{ __('admin.cancel') }}</a>
        </div>
    </form>

    @if ($isEdit)
        {{-- Images management --}}
        <div class="max-w-3xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-8">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.gallery') }}</div>
            <div class="grid grid-cols-4 gap-4">
                @forelse ($product->images as $image)
                    <div class="border border-border-light rounded-xl p-3">
                        <div class="aspect-square bg-cream rounded-lg bg-center bg-contain bg-no-repeat mb-2" style="background-image:url('{{ $image->url() }}')"></div>
                        <form method="POST" action="{{ route('admin.products.images.update', [$product, $image]) }}" class="mb-1.5">
                            @csrf @method('PATCH')
                            <select name="product_color_id" data-submit-on-change class="w-full text-[11.5px] border border-border rounded-lg px-2 py-1.5 mb-1.5">
                                <option value="">{{ __('admin.no_color') }}</option>
                                @foreach ($product->colors as $color)
                                    <option value="{{ $color->id }}" @selected($image->product_color_id === $color->id)>{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        <div class="flex justify-between items-center">
                            @if ($image->is_primary)
                                <span class="text-[10.5px] font-bold text-gold">{{ __('admin.primary') }}</span>
                            @else
                                <form method="POST" action="{{ route('admin.products.images.update', [$product, $image]) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="make_primary" value="1">
                                    <button type="submit" class="text-[10.5px] font-semibold text-muted hover:text-ink">{{ __('admin.make_primary') }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" data-confirm="{{ __('admin.confirm_delete') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[10.5px] font-semibold text-[#DC2626]">{{ __('admin.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-muted text-sm">{{ __('admin.no_images') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Sizes & Colors management --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                <div class="text-[17px] font-extrabold mb-4">{{ __('admin.sizes') }}</div>
                <div class="flex flex-wrap gap-2 mb-4">
                    @forelse ($product->sizes as $size)
                        <form method="POST" action="{{ route('admin.products.sizes.destroy', [$product, $size]) }}" class="flex items-center gap-1.5 border border-border rounded-full pl-3 pr-1.5 py-1.5 text-[13px] font-semibold">
                            @csrf @method('DELETE')
                            {{ $size->label }}
                            <button type="submit" class="w-5 h-5 rounded-full bg-cream flex items-center justify-center text-[11px]">×</button>
                        </form>
                    @empty
                        <span class="text-muted text-sm">{{ __('admin.no_sizes') }}</span>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('admin.products.sizes.store', $product) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="label" required placeholder="{{ __('admin.new_size_placeholder') }}" class="flex-1 border border-border rounded-xl px-3 py-2 text-sm">
                    <button type="submit" class="bg-ink text-white px-4 py-2 rounded-xl text-sm font-bold">+</button>
                </form>
            </div>

            <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                <div class="text-[17px] font-extrabold mb-4">{{ __('admin.colors') }}</div>
                <div class="flex flex-wrap gap-2 mb-4">
                    @forelse ($product->colors as $color)
                        <form method="POST" action="{{ route('admin.products.colors.destroy', [$product, $color]) }}" class="flex items-center gap-1.5 border border-border rounded-full pl-2 pr-1.5 py-1.5 text-[13px] font-semibold">
                            @csrf @method('DELETE')
                            @if ($color->hex_code)
                                <span class="w-4 h-4 rounded-full border border-border-light" style="background-color:{{ $color->hex_code }}"></span>
                            @endif
                            {{ $color->name }}
                            <button type="submit" class="w-5 h-5 rounded-full bg-cream flex items-center justify-center text-[11px]">×</button>
                        </form>
                    @empty
                        <span class="text-muted text-sm">{{ __('admin.no_colors') }}</span>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('admin.products.colors.store', $product) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" required placeholder="{{ __('admin.new_color_name_placeholder') }}" class="flex-1 border border-border rounded-xl px-3 py-2 text-sm">
                    <input type="color" name="hex_code" value="#111111" class="w-11 border border-border rounded-xl">
                    <button type="submit" class="bg-ink text-white px-4 py-2 rounded-xl text-sm font-bold">+</button>
                </form>
            </div>
        </div>

        {{-- Variant stock grid --}}
        <div class="max-w-3xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.stock_management') }}</div>

            @if ($product->sizes->isEmpty() || $product->colors->isEmpty())
                <p class="text-muted text-sm">{{ __('admin.stock_needs_sizes_colors') }}</p>
            @else
                <form method="POST" action="{{ route('admin.products.variants.update', $product) }}">
                    @csrf @method('PATCH')
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr>
                                    <th class="text-left pb-3 text-muted text-[12px] font-bold uppercase">{{ __('admin.size') }} \ {{ __('admin.color') }}</th>
                                    @foreach ($product->colors as $color)
                                        <th class="text-center pb-3 text-muted text-[12px] font-bold uppercase px-2">{{ $color->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->sizes as $size)
                                    <tr>
                                        <td class="py-2 font-bold">{{ $size->label }}</td>
                                        @foreach ($product->colors as $color)
                                            @php
                                                $variant = $product->variants->first(fn ($v) => $v->product_size_id === $size->id && $v->product_color_id === $color->id);
                                            @endphp
                                            <td class="py-2 px-2 text-center">
                                                @if ($variant)
                                                    <input type="number" min="0" name="stock[{{ $variant->id }}]" value="{{ $variant->stock_quantity }}"
                                                           class="w-20 border border-border rounded-lg px-2 py-1.5 text-center text-sm">
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="mt-5 bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.update_stock') }}</button>
                </form>
            @endif
        </div>
    @endif
@endsection
