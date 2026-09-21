@extends('layouts.admin')

@section('content')
    <div class="mb-7">
        <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_orders') }}</div>
        <div class="text-muted text-[15px]">{{ __('admin.orders_count', ['count' => $orders->total()]) }}</div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.search_orders') }}"
               class="flex-1 min-w-[200px] border border-border rounded-full px-5 py-2.5 text-sm">
        <select name="status" onchange="this.form.submit()" class="border border-border rounded-full px-4 py-2.5 text-sm bg-white">
            <option value="">{{ __('admin.all_statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white border border-border-light rounded-[20px] px-7 py-2 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        <div class="grid grid-cols-[1.2fr_1.4fr_1fr_1fr_0.8fr] gap-3 py-4 border-b border-border-light text-muted text-[12.5px] font-bold uppercase tracking-wide">
            <div>{{ __('admin.order') }}</div>
            <div>{{ __('admin.customer') }}</div>
            <div>{{ __('admin.date') }}</div>
            <div>{{ __('admin.total') }}</div>
            <div>{{ __('admin.status') }}</div>
        </div>

        @forelse ($orders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="grid grid-cols-[1.2fr_1.4fr_1fr_1fr_0.8fr] gap-3 py-4 border-b border-[#F8F8F8] items-center text-sm hover:bg-cream-soft -mx-7 px-7">
                <div class="font-bold">#{{ $order->order_number }}</div>
                <div class="text-gray-700">{{ $order->fullName() }}</div>
                <div class="text-muted">{{ $order->created_at->format('d/m/Y') }}</div>
                <div class="font-bold">{{ number_format($order->total, 3) }} {{ __('storefront.currency') }}</div>
                <div><span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $order->status->badgeClasses() }}">{{ $order->status->label() }}</span></div>
            </a>
        @empty
            <div class="py-10 text-center text-muted text-sm">{{ __('admin.no_orders') }}</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
