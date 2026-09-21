@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[28px] md:text-[34px] font-extrabold mb-8">{{ __('storefront.your_cart') }}</div>

    @if ($errors->any())
        <div class="mb-6 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl max-w-xl">{{ $errors->first() }}</div>
    @endif
    @if (session('status'))
        <div class="mb-6 bg-[#DCFCE7] text-[#16A34A] text-sm font-semibold px-4 py-3 rounded-xl max-w-xl">{{ session('status') }}</div>
    @endif

    @if ($lines->isNotEmpty())
        <div class="flex flex-col lg:flex-row gap-10 items-start">
            <div class="flex-1 w-full flex flex-col gap-5">
                @foreach ($lines as $line)
                    <div class="flex flex-wrap md:flex-nowrap gap-5 items-center p-5 border border-border-light rounded-[20px]">
                        <div class="w-[88px] h-[88px] bg-cream rounded-2xl shrink-0 bg-center bg-contain bg-no-repeat"
                             @if($line->product->primaryImage()) style="background-image:url('{{ $line->product->primaryImage()->url() }}')" @endif></div>
                        <div class="flex-1 min-w-[140px]">
                            <div class="text-[15.5px] font-bold">{{ $line->product->name }}</div>
                            <div class="text-[13px] text-muted mt-0.5">
                                {{ __('storefront.size') }}: {{ $line->variant->size?->label ?? '—' }}
                                @if ($line->variant->color) · {{ $line->variant->color->name }} @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('cart.update', $line->variant->id) }}" class="flex items-center gap-3">
                            @csrf @method('PATCH')
                            <button type="submit" name="qty" value="{{ max(1, $line->qty - 1) }}" class="w-[30px] h-[30px] border border-border rounded-lg flex items-center justify-center font-bold">−</button>
                            <div class="w-4 text-center font-bold">{{ $line->qty }}</div>
                            <button type="submit" name="qty" value="{{ $line->qty + 1 }}" class="w-[30px] h-[30px] border border-border rounded-lg flex items-center justify-center font-bold">+</button>
                        </form>
                        <div class="text-[15px] font-extrabold w-20 text-right">{{ number_format($line->lineTotal, 3) }} {{ __('storefront.currency') }}</div>
                        <form method="POST" action="{{ route('cart.remove', $line->variant->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-muted hover:text-[#DC2626]">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="w-full lg:w-[340px] shrink-0 bg-cream rounded-3xl p-7">
                <div class="text-[17px] font-extrabold mb-5">{{ __('storefront.order_summary') }}</div>

                <form method="POST" action="{{ route('cart.promo.apply') }}" class="flex gap-2.5 mb-5">
                    @csrf
                    @if ($promoCode)
                        <div class="flex-1 flex items-center justify-between bg-white rounded-xl px-3.5 py-2.5 text-[13.5px] font-bold">
                            {{ $promoCode->code }}
                            <a href="{{ route('cart.promo.remove') }}" onclick="event.preventDefault(); document.getElementById('remove-promo-form').submit();" class="text-[#DC2626] text-xs">✕</a>
                        </div>
                    @else
                        <input type="text" name="code" placeholder="{{ __('storefront.promo_placeholder') }}" class="flex-1 border-0 rounded-[10px] px-3.5 py-3 text-[13.5px]">
                        <button type="submit" class="bg-ink text-white px-4 py-3 rounded-[10px] text-[13px] font-bold">{{ __('storefront.apply') }}</button>
                    @endif
                </form>
                @if ($promoCode)
                    <form id="remove-promo-form" method="POST" action="{{ route('cart.promo.remove') }}" class="hidden">@csrf @method('DELETE')</form>
                @endif

                <div class="flex justify-between text-sm mb-2.5"><span>{{ __('storefront.subtotal') }}</span><span>{{ number_format($subtotal, 3) }} {{ __('storefront.currency') }}</span></div>
                @if ($discount > 0)
                    <div class="flex justify-between text-sm mb-2.5 text-[#16A34A]"><span>{{ __('storefront.discount') }}</span><span>-{{ number_format($discount, 3) }} {{ __('storefront.currency') }}</span></div>
                @endif
                <div class="flex justify-between text-sm mb-3.5"><span>{{ __('storefront.shipping') }}</span><span>{{ $shipping > 0 ? number_format($shipping, 3).' '.__('storefront.currency') : __('storefront.free') }}</span></div>
                <div class="border-t border-black/10 pt-3.5 flex justify-between text-[17px] font-extrabold mb-6">
                    <span>{{ __('storefront.total') }}</span><span>{{ number_format($subtotal - $discount + $shipping, 3) }} {{ __('storefront.currency') }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="block bg-ink text-white text-center py-4 rounded-full font-bold text-[15px]">{{ __('storefront.checkout') }} →</a>
            </div>
        </div>
    @else
        <div class="text-center py-20">
            <div class="text-[19px] font-bold mb-2.5">{{ __('storefront.cart_empty') }}</div>
            <div class="text-muted text-[14.5px] mb-7">{{ __('storefront.cart_empty_text') }}</div>
            <a href="{{ route('shop.index') }}" class="inline-block bg-ink text-white px-7 py-3.5 rounded-full font-bold text-[14.5px]">{{ __('storefront.continue_shopping') }}</a>
        </div>
    @endif
</div>
@endsection
