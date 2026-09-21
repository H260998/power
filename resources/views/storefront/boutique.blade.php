@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[13.5px] text-muted mb-2.5">
        <a href="{{ route('home') }}" class="hover:text-ink">{{ __('storefront.nav_home') }}</a> /
        <span class="text-ink font-bold">{{ __('storefront.nav_collections') }}</span>
    </div>
    <div class="flex justify-between items-end flex-wrap gap-3 mb-9">
        <div class="text-[28px] md:text-[34px] font-extrabold">{{ $activeCategory?->name ?? __('storefront.shop_all') }}</div>
        <div class="text-muted text-sm">{{ __('storefront.products_count', ['count' => $products->total()]) }}</div>
    </div>

    <div class="flex flex-col md:flex-row gap-10 items-start">
        <aside class="w-full md:w-[230px] shrink-0">
            <form method="GET" id="filters-form">
                @if (request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

                <div class="text-[14.5px] font-extrabold mb-4">{{ __('storefront.filter_category') }}</div>
                <div class="flex flex-col gap-3 mb-7">
                    <a href="{{ route('shop.index', request()->except('category')) }}" class="flex items-center gap-2.5 text-sm {{ ! $activeCategory ? 'font-bold text-ink' : 'text-gray-700' }}">
                        <span class="w-4 h-4 rounded-[5px] border border-gray-300 {{ ! $activeCategory ? 'bg-ink border-ink' : '' }}"></span>
                        {{ __('storefront.all_categories') }}
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('shop.index', array_merge(request()->except(['category','page']), ['category' => $cat->slug])) }}"
                           class="flex items-center gap-2.5 text-sm {{ $activeCategory?->id === $cat->id ? 'font-bold text-ink' : 'text-gray-700' }}">
                            <span class="w-4 h-4 rounded-[5px] border border-gray-300 {{ $activeCategory?->id === $cat->id ? 'bg-ink border-ink' : '' }}"></span>
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>

                <div class="text-[14.5px] font-extrabold mb-4">{{ __('storefront.filter_size') }}</div>
                <div class="flex flex-wrap gap-2 mb-7">
                    @foreach ($sizes as $size)
                        <a href="{{ request('size') === $size ? route('shop.index', request()->except(['size','page'])) : route('shop.index', array_merge(request()->except('page'), ['size' => $size])) }}"
                           class="border rounded-lg px-3 py-1.5 text-[12.5px] font-semibold {{ request('size') === $size ? 'border-ink bg-ink text-white' : 'border-border' }}">
                            {{ $size }}
                        </a>
                    @endforeach
                </div>

                <div class="text-[14.5px] font-extrabold mb-4">{{ __('storefront.filter_price') }}</div>
                <input type="range" name="price_max" min="0" max="300" step="10" value="{{ request('price_max', 300) }}"
                       onchange="this.form.submit()" class="w-full accent-gold">
            </form>
        </aside>

        <div class="flex-1 w-full">
            <div class="flex justify-end mb-5">
                <form method="GET">
                    @foreach (request()->except(['sort','page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="border border-border rounded-full px-4 py-2.5 text-[13.5px] font-semibold text-gray-700 bg-white">
                        <option value="" @selected(!request('sort'))>{{ __('storefront.sort_featured') }}</option>
                        <option value="newest" @selected(request('sort')==='newest')>{{ __('storefront.sort_newest') }}</option>
                        <option value="price_asc" @selected(request('sort')==='price_asc')>{{ __('storefront.sort_price_asc') }}</option>
                        <option value="price_desc" @selected(request('sort')==='price_desc')>{{ __('storefront.sort_price_desc') }}</option>
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="py-16 text-center text-muted">{{ __('storefront.no_products_found') }}</div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-5 md:gap-6">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
