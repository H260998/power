@extends('layouts.admin')

@section('content')
    <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_dashboard') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.dashboard_welcome') }}</div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-9">
        @foreach ($stats as $stat)
            <div class="bg-cream rounded-[20px] p-6">
                <div class="text-muted text-[13.5px] font-semibold mb-2.5">{{ $stat['label'] }}</div>
                <div class="text-[22px] md:text-[26px] font-extrabold">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-6">
        <div class="bg-white border border-border-light rounded-[20px] p-7 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.recent_orders') }}</div>
            <div class="grid grid-cols-[1.2fr_1.4fr_1fr_0.8fr] gap-3 pb-3 border-b border-border-light text-muted text-[12.5px] font-bold uppercase tracking-wide">
                <div>{{ __('admin.order') }}</div>
                <div>{{ __('admin.customer') }}</div>
                <div>{{ __('admin.total') }}</div>
                <div>{{ __('admin.status') }}</div>
            </div>
            @forelse ($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="grid grid-cols-[1.2fr_1.4fr_1fr_0.8fr] gap-3 py-3.5 border-b border-[#F8F8F8] items-center text-[13.5px] hover:bg-cream-soft -mx-7 px-7">
                    <div class="font-bold">#{{ $order->order_number }}</div>
                    <div class="text-gray-700">{{ $order->fullName() }}</div>
                    <div class="font-bold">{{ number_format($order->total, 3) }}</div>
                    <div><span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></div>
                </a>
            @empty
                <div class="py-8 text-center text-muted text-sm">{{ __('admin.no_orders') }}</div>
            @endforelse
        </div>

        <div class="bg-white border border-border-light rounded-[20px] p-7 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.low_stock') }}</div>
            @forelse ($lowStockVariants as $variant)
                <div class="flex justify-between items-center py-2.5 border-b border-[#F8F8F8] last:border-0 text-[13.5px]">
                    <div>
                        <div class="font-semibold">{{ $variant->product->name }}</div>
                        <div class="text-muted text-[12px]">{{ $variant->size?->label }} · {{ $variant->color?->name }}</div>
                    </div>
                    <span class="text-[11.5px] font-bold px-2.5 py-1 rounded-full {{ $variant->stock_quantity === 0 ? 'bg-[#FEE2E2] text-[#DC2626]' : 'bg-[#FEF3C7] text-[#B45309]' }}">
                        {{ $variant->stock_quantity }}
                    </span>
                </div>
            @empty
                <div class="py-8 text-center text-muted text-sm">{{ __('admin.no_low_stock') }}</div>
            @endforelse
        </div>
    </div>
@endsection
