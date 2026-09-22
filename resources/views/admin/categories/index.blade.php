@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-7">
        <div>
            <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_categories') }}</div>
            <div class="text-muted text-[15px]">{{ __('admin.categories_count', ['count' => $categories->sum(fn ($c) => 1 + $c->children->count())]) }}</div>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="bg-ink text-white px-6 py-3.5 rounded-full text-sm font-bold">+ {{ __('admin.add_category') }}</a>
    </div>

    @if ($errors->any())
        <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white border border-border-light rounded-[20px] px-7 py-2 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        <div class="grid grid-cols-[2.4fr_1fr_1fr_1fr] gap-3 py-4 border-b border-border-light text-muted text-[12.5px] font-bold uppercase tracking-wide">
            <div>{{ __('admin.category_name') }}</div>
            <div>{{ __('admin.category_slug') }}</div>
            <div>{{ __('admin.status') }}</div>
            <div></div>
        </div>

        @forelse ($categories as $category)
            <div class="grid grid-cols-[2.4fr_1fr_1fr_1fr] gap-3 py-4 border-b border-[#F8F8F8] items-center">
                <div class="font-bold text-[14.5px]">{{ $category->name }}</div>
                <div class="text-muted text-sm">{{ $category->slug }}</div>
                <div>
                    <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $category->is_active ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#F1F5F9] text-[#334155]' }}">
                        {{ $category->is_active ? __('admin.active') : __('admin.inactive') }}
                    </span>
                </div>
                <div class="flex gap-4 justify-end text-sm font-semibold">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="hover:text-gold">{{ __('admin.edit') }}</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="{{ __('admin.confirm_delete') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[#DC2626] hover:opacity-70">{{ __('admin.delete') }}</button>
                    </form>
                </div>
            </div>

            @foreach ($category->children as $child)
                <div class="grid grid-cols-[2.4fr_1fr_1fr_1fr] gap-3 py-4 border-b border-[#F8F8F8] items-center">
                    <div class="text-[14.5px] pl-6 text-gray-700">↳ {{ $child->name }}</div>
                    <div class="text-muted text-sm">{{ $child->slug }}</div>
                    <div>
                        <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $child->is_active ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#F1F5F9] text-[#334155]' }}">
                            {{ $child->is_active ? __('admin.active') : __('admin.inactive') }}
                        </span>
                    </div>
                    <div class="flex gap-4 justify-end text-sm font-semibold">
                        <a href="{{ route('admin.categories.edit', $child) }}" class="hover:text-gold">{{ __('admin.edit') }}</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" data-confirm="{{ __('admin.confirm_delete') }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[#DC2626] hover:opacity-70">{{ __('admin.delete') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="py-10 text-center text-muted text-sm">{{ __('admin.no_categories') }}</div>
        @endforelse
    </div>
@endsection
