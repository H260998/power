@extends('layouts.storefront')

@section('content')
@php
    $images = $product->images;
    $primary = $product->primaryImage();
@endphp
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[13.5px] text-muted mb-7">
        <a href="{{ route('home') }}" class="hover:text-ink">{{ __('storefront.nav_home') }}</a> /
        <a href="{{ route('shop.index') }}" class="hover:text-ink">{{ __('storefront.nav_collections') }}</a> /
        <span class="text-ink font-bold">{{ $product->name }}</span>
    </div>

    <div class="flex flex-col md:flex-row gap-10 md:gap-14 mb-16 md:mb-20">
        <div class="flex-1 max-w-full md:max-w-[480px]">
            <div class="bg-cream rounded-3xl aspect-square flex items-center justify-center mb-4 overflow-hidden">
                <div id="pdp-main-image" class="w-4/5 h-4/5 bg-center bg-contain bg-no-repeat" @if($primary) style="background-image:url('{{ $primary->url() }}')" @endif></div>
            </div>
            <div class="flex gap-3 flex-wrap">
                @foreach ($images as $image)
                    <button type="button" data-thumb data-src="{{ $image->url() }}" data-color="{{ $image->product_color_id }}"
                            class="w-20 h-20 bg-cream rounded-2xl p-2.5 {{ $image->is_primary ? 'border-2 border-ink' : 'opacity-60' }}">
                        <div class="w-full h-full bg-center bg-contain bg-no-repeat" style="background-image:url('{{ $image->url() }}')"></div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex-1 max-w-full md:max-w-[480px]">
            <div class="text-gold text-[13px] font-extrabold uppercase tracking-wide mb-2.5">{{ $product->category->name }}</div>
            <div class="text-[26px] md:text-[30px] font-extrabold mb-2.5">{{ $product->name }}</div>
            <div class="text-gold text-sm mb-3.5">★★★★★ <span class="text-muted">({{ __('storefront.reviews_count') }})</span></div>
            <div class="flex items-center gap-3 mb-5">
                <div class="text-2xl md:text-[26px] font-extrabold">{{ number_format($product->price, 3) }} {{ __('storefront.currency') }}</div>
                @if ($product->compare_at_price)
                    <div class="text-muted text-base line-through">{{ number_format($product->compare_at_price, 3) }} {{ __('storefront.currency') }}</div>
                @endif
            </div>
            <div class="text-muted text-[14.5px] leading-relaxed mb-7">{{ $product->description }}</div>

            <form id="pdp-form" method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="variant_id" id="pdp-variant-id" value="">
                <input type="hidden" name="qty" id="pdp-qty-input" value="1">

                @if ($product->sizes->isNotEmpty())
                    <div class="mb-6">
                        <div class="text-[13.5px] font-extrabold mb-3">{{ __('storefront.size') }}</div>
                        <div class="flex flex-wrap gap-2.5" id="pdp-sizes">
                            @foreach ($product->sizes as $size)
                                <button type="button" data-size-id="{{ $size->id }}"
                                        class="pdp-size-chip px-4 py-2.5 rounded-[10px] text-[13.5px] font-bold border-[1.5px] border-border">
                                    {{ $size->label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($product->colors->isNotEmpty())
                    <div class="mb-6">
                        <div class="text-[13.5px] font-extrabold mb-3">{{ __('storefront.color') }}</div>
                        <div class="flex flex-wrap gap-2.5" id="pdp-colors">
                            @foreach ($product->colors as $color)
                                <button type="button" data-color-id="{{ $color->id }}" data-color-name="{{ $color->name }}"
                                        class="pdp-color-chip flex items-center gap-2 px-3.5 py-2 rounded-[10px] text-[13px] font-bold border-[1.5px] border-border">
                                    @if ($color->hex_code)
                                        <span class="w-4 h-4 rounded-full border border-border-light" style="background-color:{{ $color->hex_code }}"></span>
                                    @endif
                                    {{ $color->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="text-[13.5px] font-extrabold mb-3">{{ __('storefront.quantity') }}</div>
                    <div class="flex items-center gap-4">
                        <button type="button" id="pdp-qty-dec" class="w-9 h-9 border border-border rounded-[10px] flex items-center justify-center font-extrabold">−</button>
                        <div id="pdp-qty-display" class="text-[15px] font-bold w-5 text-center">1</div>
                        <button type="button" id="pdp-qty-inc" class="w-9 h-9 border border-border rounded-[10px] flex items-center justify-center font-extrabold">+</button>
                    </div>
                </div>

                <div id="pdp-stock-msg" class="text-[13px] font-semibold mb-4"></div>

                <div class="flex gap-3.5">
                    <button type="submit" id="pdp-add-btn" class="flex-1 bg-ink text-white py-4 rounded-full text-[15px] font-bold text-center">{{ __('storefront.add_to_cart') }}</button>
                    <button type="submit" formaction="{{ route('cart.buyNow') }}" id="pdp-buy-btn" class="flex-1 bg-gold text-ink py-4 rounded-full text-[15px] font-bold text-center">{{ __('storefront.buy_now') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($similarProducts->isNotEmpty())
        <div class="text-xl md:text-2xl font-extrabold mb-6">{{ __('storefront.similar_products') }}</div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-5 md:gap-6">
            @foreach ($similarProducts as $item)
                <x-product-card :product="$item" />
            @endforeach
        </div>
    @endif
</div>

@php
    $variantsForJs = $product->variants->map(fn ($v) => [
        'id' => $v->id,
        'sizeId' => $v->product_size_id,
        'colorId' => $v->product_color_id,
        'stock' => $v->stock_quantity,
    ]);
@endphp
<script>
(function () {
    const variants = @json($variantsForJs);
    const sizes = document.querySelectorAll('.pdp-size-chip');
    const colors = document.querySelectorAll('.pdp-color-chip');
    const variantInput = document.getElementById('pdp-variant-id');
    const qtyInput = document.getElementById('pdp-qty-input');
    const qtyDisplay = document.getElementById('pdp-qty-display');
    const stockMsg = document.getElementById('pdp-stock-msg');
    const addBtn = document.getElementById('pdp-add-btn');
    const buyBtn = document.getElementById('pdp-buy-btn');
    const mainImage = document.getElementById('pdp-main-image');

    let selectedSizeId = sizes.length ? Number(sizes[0].dataset.sizeId) : null;
    let selectedColorId = colors.length ? Number(colors[0].dataset.colorId) : null;
    let qty = 1;

    const chipActive = 'border-ink bg-ink text-white';
    const chipInactive = 'border-border';

    function paintChips(nodeList, selectedId, dataAttr) {
        nodeList.forEach(node => {
            const isActive = Number(node.dataset[dataAttr]) === selectedId;
            node.classList.toggle('border-ink', isActive);
            node.classList.toggle('bg-ink', isActive);
            node.classList.toggle('text-white', isActive);
            node.classList.toggle('border-border', !isActive);
        });
    }

    function currentVariant() {
        return variants.find(v => v.sizeId === selectedSizeId && v.colorId === selectedColorId)
            || variants.find(v => v.sizeId === selectedSizeId)
            || variants.find(v => v.colorId === selectedColorId)
            || variants[0] || null;
    }

    function refresh() {
        paintChips(sizes, selectedSizeId, 'sizeId');
        paintChips(colors, selectedColorId, 'colorId');

        const variant = currentVariant();
        const inStock = variant && variant.stock > 0;

        variantInput.value = variant ? variant.id : '';
        qty = Math.min(qty, variant ? Math.max(variant.stock, 1) : 1);
        qtyDisplay.textContent = qty;
        qtyInput.value = qty;

        addBtn.disabled = !inStock;
        buyBtn.disabled = !inStock;
        addBtn.classList.toggle('opacity-40', !inStock);
        buyBtn.classList.toggle('opacity-40', !inStock);

        stockMsg.textContent = inStock ? '' : @json(__('storefront.out_of_stock_variant'));
        stockMsg.classList.toggle('text-[#DC2626]', !inStock);
    }

    sizes.forEach(node => node.addEventListener('click', () => {
        selectedSizeId = Number(node.dataset.sizeId);
        refresh();
    }));

    colors.forEach(node => node.addEventListener('click', () => {
        selectedColorId = Number(node.dataset.colorId);
        const thumb = document.querySelector(`[data-thumb][data-color="${selectedColorId}"]`);
        if (thumb && mainImage) {
            mainImage.style.backgroundImage = `url('${thumb.dataset.src}')`;
        }
        refresh();
    }));

    document.querySelectorAll('[data-thumb]').forEach(node => node.addEventListener('click', () => {
        if (mainImage) mainImage.style.backgroundImage = `url('${node.dataset.src}')`;
    }));

    document.getElementById('pdp-qty-inc')?.addEventListener('click', () => {
        const variant = currentVariant();
        if (variant && qty < variant.stock) { qty++; qtyDisplay.textContent = qty; qtyInput.value = qty; }
    });
    document.getElementById('pdp-qty-dec')?.addEventListener('click', () => {
        if (qty > 1) { qty--; qtyDisplay.textContent = qty; qtyInput.value = qty; }
    });

    refresh();
})();

if (typeof fbq === 'function') {
    fbq('track', 'ViewContent', {
        content_ids: [{{ $product->id }}],
        value: {{ (float) $product->price }},
        currency: 'TND',
    }, {eventID: '{{ $eventId }}'});
}
</script>
@endsection
