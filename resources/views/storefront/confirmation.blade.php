@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-24 md:py-36 text-center">
    <div class="w-[76px] h-[76px] rounded-full bg-gold flex items-center justify-center mx-auto mb-7">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#111111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
    </div>
    <div class="text-[26px] md:text-[30px] font-extrabold mb-3">{{ __('storefront.order_confirmed') }}</div>
    <div class="text-muted text-[15.5px] mb-2">{{ __('storefront.order_confirmed_text') }}</div>
    <div class="text-[15px] font-bold mb-9">{{ __('storefront.order_number') }}: <span class="text-gold">{{ $order->order_number }}</span></div>

    <div class="max-w-md mx-auto text-left bg-cream rounded-3xl p-7 mb-9">
        @foreach ($order->items as $item)
            <div class="flex justify-between text-[13.5px] mb-2.5">
                <span>{{ $item->product_name }}@if($item->size_label) — {{ $item->size_label }}@endif × {{ $item->quantity }}</span>
                <span class="font-bold">{{ number_format($item->line_total, 3) }} {{ __('storefront.currency') }}</span>
            </div>
        @endforeach
        <div class="border-t border-black/10 mt-3 pt-3 flex justify-between font-extrabold text-[15px]">
            <span>{{ __('storefront.total') }}</span><span>{{ number_format($order->total, 3) }} {{ __('storefront.currency') }}</span>
        </div>
    </div>

    <a href="{{ route('home') }}" class="inline-block bg-ink text-white px-8 py-4 rounded-full font-bold text-[14.5px]">{{ __('storefront.back_home') }}</a>
</div>

@if ($purchaseEventId)
    <script>
    if (typeof fbq === 'function') {
        fbq('track', 'Purchase', @json($purchasePayload), {eventID: '{{ $purchaseEventId }}'});
    }
    </script>
@endif
@endsection
