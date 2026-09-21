@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20 max-w-xl mx-auto">
    <div class="text-[28px] md:text-[34px] font-extrabold mb-8 text-center">{{ __('storefront.track_order') }}</div>

    <form method="POST" action="{{ route('order.track.lookup') }}" class="flex flex-col gap-4 mb-8">
        @csrf
        <input type="text" name="order_number" value="{{ old('order_number') }}" placeholder="{{ __('storefront.order_number') }}" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('storefront.phone') }}" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
        <button type="submit" class="bg-ink text-white py-4 rounded-full font-bold text-[15px]">{{ __('storefront.track_button') }}</button>
    </form>

    @if (($notFound ?? false))
        <div class="bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl text-center">{{ __('storefront.order_not_found') }}</div>
    @endif

    @if ($order ?? null)
        <div class="bg-cream rounded-3xl p-7">
            <div class="flex justify-between items-center mb-4">
                <div class="font-extrabold">{{ $order->order_number }}</div>
                <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
            </div>
            @foreach ($order->items as $item)
                <div class="flex justify-between text-[13.5px] mb-2">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="font-bold">{{ number_format($item->line_total, 3) }} {{ __('storefront.currency') }}</span>
                </div>
            @endforeach
            <div class="border-t border-black/10 mt-3 pt-3 flex justify-between font-extrabold text-[15px]">
                <span>{{ __('storefront.total') }}</span><span>{{ number_format($order->total, 3) }} {{ __('storefront.currency') }}</span>
            </div>
        </div>
    @endif
</div>
@endsection
