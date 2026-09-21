@extends('layouts.admin')

@section('content')
    @php $isEdit = $category->exists; @endphp

    <div class="text-[28px] font-extrabold mb-1.5">{{ $isEdit ? __('admin.edit_category') : __('admin.add_category') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.nav_categories') }}</div>

    @if ($errors->any())
        <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="max-w-2xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-3 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_fr') }}</label>
                <input type="text" name="name[fr]" value="{{ old('name.fr', $category->getTranslation('name', 'fr', false)) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_en') }}</label>
                <input type="text" name="name[en]" value="{{ old('name.en', $category->getTranslation('name', 'en', false)) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.name_ar') }}</label>
                <input type="text" name="name[ar]" value="{{ old('name.ar', $category->getTranslation('name', 'ar', false)) }}" required dir="rtl" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.category_slug') }}</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.parent_category') }}</label>
                <select name="parent_id" class="w-full border border-border rounded-xl px-4 py-3 text-sm bg-white">
                    <option value="">{{ __('admin.none') }}</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.image') }}</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-border rounded-xl px-4 py-2.5 text-sm">
                @if ($category->image)
                    <img src="{{ $category->imageUrl() }}" class="mt-2 w-16 h-16 object-cover rounded-lg">
                @endif
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 text-[13px] font-semibold mb-7">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true)) class="rounded border-border">
            {{ __('admin.active') }}
        </label>

        <div class="flex gap-3">
            <button type="submit" class="bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.categories.index') }}" class="px-7 py-3.5 rounded-full text-sm font-bold border border-border">{{ __('admin.cancel') }}</a>
        </div>
    </form>
@endsection
