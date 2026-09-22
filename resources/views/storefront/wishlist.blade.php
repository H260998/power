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

<script nonce="{{ $cspNonce }}">
(function () {
    const STORAGE_KEY = 'power_wishlist';
    let ids = [];
    try {
        ids = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
            .map(Number)
            .filter(Number.isInteger)
            .slice(0, 50);
    } catch (_) {}
    const grid = document.getElementById('wishlist-grid');
    const empty = document.getElementById('wishlist-empty');
    const wishlistEndpoint = @js(route('ajax.wishlist.products'));
    const cartEndpoint = @js(route('cart.add'));
    const csrfToken = @js(csrf_token());

    function element(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function sameOriginUrl(value) {
        try {
            const url = new URL(value, window.location.origin);
            return url.origin === window.location.origin ? url.href : '#';
        } catch (_) {
            return '#';
        }
    }

    if (!ids.length) {
        empty.hidden = false;
        return;
    }

    fetch(wishlistEndpoint + '?ids=' + encodeURIComponent(ids.join(',')), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    })
        .then(r => {
            if (!r.ok) throw new Error('Wishlist request failed');
            return r.json();
        })
        .then(products => {
            if (!products.length) { empty.hidden = false; return; }

            products.forEach((product) => {
                const card = element('div');
                const media = element('div', 'relative bg-cream rounded-[20px] aspect-square overflow-hidden mb-3.5');
                const mediaLink = element('a', 'block w-full h-full');
                mediaLink.href = sameOriginUrl(product.url);

                const image = element('div', 'w-full h-full bg-center bg-contain bg-no-repeat');
                if (typeof product.image === 'string' && /^https?:\/\//i.test(product.image)) {
                    image.style.backgroundImage = `url(${JSON.stringify(product.image)})`;
                }
                mediaLink.append(image);

                const remove = element('button', 'absolute top-3.5 right-3.5 w-[34px] h-[34px] bg-white rounded-full flex items-center justify-center', '✕');
                remove.type = 'button';
                remove.dataset.remove = String(Number(product.id));
                media.append(mediaLink, remove);

                const title = element('a', 'block text-[15px] font-bold', String(product.name ?? ''));
                title.href = sameOriginUrl(product.url);
                const category = element('div', 'text-[13px] text-muted my-0.5 mb-2', String(product.category ?? ''));
                const row = element('div', 'flex justify-between items-center');
                row.append(element('div', 'text-[15px] font-extrabold', String(product.priceLabel ?? '')));

                if (Number.isInteger(Number(product.variantId)) && Number(product.variantId) > 0) {
                    const form = element('form');
                    form.method = 'POST';
                    form.action = cartEndpoint;
                    for (const [name, value] of [['_token', csrfToken], ['variant_id', product.variantId]]) {
                        const input = element('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = String(value);
                        form.append(input);
                    }
                    const add = element('button', 'w-[30px] h-[30px] bg-gold rounded-full flex items-center justify-center font-extrabold', '+');
                    add.type = 'submit';
                    form.append(add);
                    row.append(form);
                }

                card.append(media, title, category, row);
                grid.append(card);
            });

            grid.querySelectorAll('[data-remove]').forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = Number(btn.dataset.remove);
                const next = ids.filter(i => i !== id);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
                location.reload();
            }));
        })
        .catch(() => { empty.hidden = false; });
})();
</script>
@endsection
