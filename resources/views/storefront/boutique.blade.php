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

                @php
                    $priceFloor = 0;
                    $priceCeiling = 300;
                    $selectedMinPrice = min($priceCeiling - 10, max($priceFloor, (int) request('price_min', $priceFloor)));
                    $selectedMaxPrice = min($priceCeiling, max($selectedMinPrice + 10, (int) request('price_max', $priceCeiling)));
                @endphp
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="text-[14.5px] font-extrabold">{{ __('storefront.filter_price') }}</div>
                    <output id="price-range-value" for="price-min price-max"
                            class="rounded-full bg-ink px-3 py-1 text-[12px] font-extrabold text-white tabular-nums">
                        {{ number_format($selectedMinPrice, 3) }}–{{ number_format($selectedMaxPrice, 3) }} {{ __('storefront.currency') }}
                    </output>
                </div>
                <div id="price-range" class="relative h-7" data-currency="{{ __('storefront.currency') }}">
                    <div class="absolute inset-x-0 top-1/2 h-1 -translate-y-1/2 rounded-full bg-[#D1D5DB]"></div>
                    <div id="price-range-fill" class="absolute top-1/2 h-1 -translate-y-1/2 rounded-full bg-gold"></div>
                    <input id="price-min" type="range" name="price_min" min="{{ $priceFloor }}" max="{{ $priceCeiling }}" step="10" value="{{ $selectedMinPrice }}"
                           aria-label="{{ __('storefront.filter_price') }} minimum" aria-describedby="price-range-value"
                           class="price-range-thumb absolute inset-x-0 top-1/2 w-full -translate-y-1/2">
                    <input id="price-max" type="range" name="price_max" min="{{ $priceFloor }}" max="{{ $priceCeiling }}" step="10" value="{{ $selectedMaxPrice }}"
                           aria-label="{{ __('storefront.filter_price') }} maximum" aria-describedby="price-range-value"
                           class="price-range-thumb absolute inset-x-0 top-1/2 w-full -translate-y-1/2">
                </div>
                <div class="mt-1.5 flex justify-between text-[11px] font-semibold text-muted tabular-nums">
                    <span>{{ number_format($priceFloor, 3) }} {{ __('storefront.currency') }}</span>
                    <span>{{ number_format($priceCeiling, 3) }} {{ __('storefront.currency') }}</span>
                </div>
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

<style>
    .price-range-thumb {
        appearance: none;
        height: 0;
        background: transparent;
        pointer-events: none;
    }
    .price-range-thumb::-webkit-slider-thumb {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid white;
        border-radius: 9999px;
        background: #EBAF16;
        box-shadow: 0 0 0 1px rgba(17, 17, 17, .15);
        cursor: pointer;
        pointer-events: auto;
    }
    .price-range-thumb::-moz-range-thumb {
        width: 14px;
        height: 14px;
        border: 2px solid white;
        border-radius: 9999px;
        background: #EBAF16;
        box-shadow: 0 0 0 1px rgba(17, 17, 17, .15);
        cursor: pointer;
        pointer-events: auto;
    }
</style>

<script>
(() => {
    const range = document.getElementById('price-range');
    const minimum = document.getElementById('price-min');
    const maximum = document.getElementById('price-max');
    const fill = document.getElementById('price-range-fill');
    const output = document.getElementById('price-range-value');

    if (!range || !minimum || !maximum || !fill || !output) return;

    const step = Number(minimum.step) || 10;
    const floor = Number(minimum.min);
    const ceiling = Number(maximum.max);
    const currency = range.dataset.currency;

    const refresh = changed => {
        let min = Number(minimum.value);
        let max = Number(maximum.value);

        if (max - min < step) {
            if (changed === minimum) min = Math.max(floor, max - step);
            else max = Math.min(ceiling, min + step);
        }

        minimum.value = min;
        maximum.value = max;
        fill.style.left = `${((min - floor) / (ceiling - floor)) * 100}%`;
        fill.style.right = `${100 - ((max - floor) / (ceiling - floor)) * 100}%`;
        output.textContent = `${min.toFixed(3)}–${max.toFixed(3)} ${currency}`;
    };

    minimum.addEventListener('input', () => refresh(minimum));
    maximum.addEventListener('input', () => refresh(maximum));
    minimum.addEventListener('change', () => minimum.form.submit());
    maximum.addEventListener('change', () => maximum.form.submit());
    refresh();
})();
</script>
@endsection
