@extends('layouts.admin')

@section('content')
    <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_statistics') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.statistics_subtitle') }}</div>

    <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-8">
        <div class="text-[17px] font-extrabold mb-6">{{ __('admin.statistics_revenue_over_time') }}</div>
        <div class="flex items-end gap-2 h-[180px]">
            @foreach ($revenueSeries as $point)
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2 group">
                    <div class="text-[10px] text-muted opacity-0 group-hover:opacity-100">{{ number_format($point['value'], 1) }}</div>
                    <div class="w-full bg-gold rounded-t-md" style="height: {{ max(4, round($point['value'] / $maxRevenue * 150)) }}px"></div>
                    <div class="text-[10px] text-muted">{{ $point['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.statistics_top_products') }}</div>
            @forelse ($topProducts as $product)
                <div class="flex justify-between items-center py-3 border-b border-[#F8F8F8] last:border-0 text-sm">
                    <div>
                        <div class="font-semibold">{{ $product->product_name }}</div>
                        <div class="text-muted text-[12px]">{{ $product->units }} {{ __('admin.statistics_units_sold') }}</div>
                    </div>
                    <div class="font-bold">{{ number_format($product->revenue, 3) }} {{ __('storefront.currency') }}</div>
                </div>
            @empty
                <div class="py-8 text-center text-muted text-sm">{{ __('admin.statistics_no_data') }}</div>
            @endforelse
        </div>

        <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.statistics_category_breakdown') }}</div>
            @forelse ($categoryBreakdown as $row)
                <div class="mb-4 last:mb-0">
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="font-semibold">{{ $row['name'] }}</span>
                        <span class="font-bold">{{ number_format($row['revenue'], 3) }} {{ __('storefront.currency') }}</span>
                    </div>
                    <div class="h-2 bg-cream rounded-full overflow-hidden">
                        <div class="h-full bg-gold rounded-full" style="width: {{ round($row['revenue'] / $maxCategoryRevenue * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-muted text-sm">{{ __('admin.statistics_no_data') }}</div>
            @endforelse
        </div>
    </div>
@endsection
