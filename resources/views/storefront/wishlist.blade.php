@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[28px] md:text-[34px] font-extrabold mb-8">{{ __('storefront.wishlist') }}</div>

    <div id="wishlist-empty" class="text-center py-20" hidden>
        <div class="text-[19px] font-bold mb-2.5">{{ __('storefront.wishlist_empty') }}</div>
        <div class="text-muted text-[14.5px] mb-7">{{ __('storefront.wishlist_empty_text') }}</div>
        <a href="{{ route('shop.index') }}" class="inline-block bg-ink text-white px-7 py-3.5 rounded-full font-bold text-[14.5px]">{{ __('storefront.continue_shopping') }}</a>
    </div>

    <div id="wishlist-grid" class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-6"></div>
</div>

<script>
(function () {
    const STORAGE_KEY = 'power_wishlist';
    const ids = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    const grid = document.getElementById('wishlist-grid');
    const empty = document.getElementById('wishlist-empty');

    if (!ids.length) {
        empty.hidden = false;
        return;
    }

    fetch('{{ route('ajax.wishlist.products') }}?ids=' + ids.join(','))
        .then(r => r.json())
        .then(products => {
            if (!products.length) { empty.hidden = false; return; }

            grid.innerHTML = products.map(p => `
                <div>
                    <a href="${p.url}" class="block relative bg-cream rounded-[20px] aspect-square overflow-hidden mb-3.5">
                        <div class="w-full h-full bg-center bg-contain bg-no-repeat" style="background-image:url('${p.image ?? ''}')"></div>
                        <button type="button" data-remove="${p.id}" class="absolute top-3.5 right-3.5 w-[34px] h-[34px] bg-white rounded-full flex items-center justify-center">✕</button>
                    </a>
                    <a href="${p.url}" class="block text-[15px] font-bold">${p.name}</a>
                    <div class="text-[13px] text-muted my-0.5 mb-2">${p.category}</div>
                    <div class="flex justify-between items-center">
                        <div class="text-[15px] font-extrabold">${p.priceLabel}</div>
                        ${p.variantId ? `<form method="POST" action="{{ route('cart.add') }}"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="variant_id" value="${p.variantId}"><button type="submit" class="w-[30px] h-[30px] bg-gold rounded-full flex items-center justify-center font-extrabold">+</button></form>` : ''}
                    </div>
                </div>
            `).join('');

            grid.querySelectorAll('[data-remove]').forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = Number(btn.dataset.remove);
                const next = ids.filter(i => i !== id);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
                location.reload();
            }));
        });
})();
</script>
@endsection
