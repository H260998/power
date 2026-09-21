@props(['product'])

@php
    $image = $product->primaryImage();
    $variant = $product->defaultVariant();
@endphp

<div class="group">
    <a href="{{ route('shop.product', $product->slug) }}" class="block relative bg-cream rounded-[20px] aspect-square overflow-hidden mb-3.5">
        <div class="w-full h-full bg-center bg-contain bg-no-repeat" @if($image) style="background-image:url('{{ $image->url() }}')" @endif></div>
        <button type="button" data-wishlist-toggle="{{ $product->id }}"
                class="absolute top-3.5 right-3.5 w-[34px] h-[34px] bg-white rounded-full flex items-center justify-center">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#111111" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>
        </button>
    </a>
    <a href="{{ route('shop.product', $product->slug) }}" class="block text-[15px] font-bold">{{ $product->name }}</a>
    <div class="text-[13px] text-muted my-0.5 mb-2">{{ $product->category->name }}</div>
    <div class="flex justify-between items-center">
        <div class="text-[15px] font-extrabold">{{ number_format($product->price, 3) }} {{ __('storefront.currency') }}</div>
        @if ($variant && $variant->stock_quantity > 0)
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                <button type="submit" class="w-[30px] h-[30px] bg-gold rounded-full flex items-center justify-center font-extrabold">+</button>
            </form>
        @endif
    </div>
</div>
