@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-start mb-8 flex-wrap gap-4">
        <div>
            <div class="text-[28px] font-extrabold mb-1.5">#{{ $order->order_number }}</div>
            <div class="text-muted text-[15px]">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.orders.delivery-note', $order) }}" target="_blank" rel="noopener" class="bg-ink text-white px-5 py-3 rounded-full text-sm font-bold">{{ __('admin.print_delivery_note') }}</a>
            <span class="text-[13px] font-bold px-4 py-2 rounded-full {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        <div class="flex-1 w-full flex flex-col gap-6">
            <div class="bg-white border border-border-light rounded-[20px] p-7 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                <div class="text-[15px] font-extrabold mb-4">{{ __('admin.customer') }}</div>
                <div class="text-sm text-gray-700 space-y-1.5">
                    <div><span class="text-muted">{{ __('storefront.first_name') }} / {{ __('storefront.last_name') }}:</span> <span class="font-semibold">{{ $order->fullName() }}</span></div>
                    <div><span class="text-muted">{{ __('storefront.phone') }}:</span> <span class="font-semibold">{{ $order->phone }}</span></div>
                    <div><span class="text-muted">{{ __('storefront.address') }}:</span> <span class="font-semibold">{{ $order->address }}, {{ $order->city }}</span></div>
                    @if ($order->notes)
                        <div><span class="text-muted">{{ __('storefront.notes') }}:</span> <span class="font-semibold">{{ $order->notes }}</span></div>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-border-light rounded-[20px] p-7 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                <div class="text-[15px] font-extrabold mb-4">{{ __('admin.order_items') }}</div>
                <div class="flex flex-col gap-3">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between items-center text-sm border-b border-[#F8F8F8] last:border-0 pb-3 last:pb-0">
                            <div>
                                <div class="font-semibold">{{ $item->product_name }}</div>
                                <div class="text-muted text-[12.5px]">
                                    @if($item->size_label) {{ __('storefront.size') }}: {{ $item->size_label }} @endif
                                    @if($item->color_name) · {{ $item->color_name }} @endif
                                    · × {{ $item->quantity }}
                                </div>
                            </div>
                            <div class="font-bold">{{ number_format($item->line_total, 3) }} {{ __('storefront.currency') }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-border-light mt-4 pt-4 flex flex-col gap-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-muted">{{ __('storefront.subtotal') }}</span><span>{{ number_format($order->subtotal, 3) }} {{ __('storefront.currency') }}</span></div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-[#16A34A]"><span>{{ __('storefront.discount') }} @if($order->promoCode)({{ $order->promoCode->code }})@endif</span><span>-{{ number_format($order->discount_amount, 3) }} {{ __('storefront.currency') }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-muted">{{ __('storefront.shipping') }}</span><span>{{ number_format($order->shipping_fee, 3) }} {{ __('storefront.currency') }}</span></div>
                    <div class="flex justify-between font-extrabold text-[15px] pt-1.5"><span>{{ __('storefront.total') }}</span><span>{{ number_format($order->total, 3) }} {{ __('storefront.currency') }}</span></div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-[300px] shrink-0 bg-white border border-border-light rounded-[20px] p-7 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[15px] font-extrabold mb-4">{{ __('admin.update_status') }}</div>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex flex-col gap-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-border rounded-xl px-4 py-3 text-sm bg-white">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-ink text-white py-3 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
            </form>
        </div>
    </div>
@endsection
