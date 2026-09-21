@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-7">
        <div>
            <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_products') }}</div>
            <div class="text-muted text-[15px]">{{ __('admin.products_count', ['count' => $products->total()]) }}</div>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-ink text-white px-6 py-3.5 rounded-full text-sm font-bold">+ {{ __('admin.add_product') }}</a>
    </div>

    <form method="GET" class="mb-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.search_products') }}"
               class="w-full max-w-sm border border-border rounded-full px-5 py-2.5 text-sm">
    </form>

    <div class="bg-white border border-border-light rounded-[20px] px-7 py-2 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        <div class="grid grid-cols-[2.4fr_1.2fr_1fr_1fr_1fr_0.8fr] gap-3 py-4 border-b border-border-light text-muted text-[12.5px] font-bold uppercase tracking-wide">
            <div>{{ __('admin.product') }}</div>
            <div>{{ __('admin.category') }}</div>
            <div>{{ __('admin.price') }}</div>
            <div>{{ __('admin.stock') }}</div>
            <div>{{ __('admin.status') }}</div>
            <div></div>
        </div>

        @forelse ($products as $product)
            @php $stock = $product->variants->sum('stock_quantity'); @endphp
            <div class="grid grid-cols-[2.4fr_1.2fr_1fr_1fr_1fr_0.8fr] gap-3 py-4 border-b border-[#F8F8F8] items-center">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-[10px] bg-cream bg-center bg-cover shrink-0" @if($product->primaryImage()) style="background-image:url('{{ $product->primaryImage()->url() }}')" @endif></div>
                    <span class="font-bold text-[14.5px]">{{ $product->name }}</span>
                </div>
                <div class="text-muted text-sm">{{ $product->category->name }}</div>
                <div class="font-bold text-sm">{{ number_format($product->price, 3) }} {{ __('storefront.currency') }}</div>
                <div class="text-sm">{{ $stock }}</div>
                <div>
                    <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $stock === 0 ? 'bg-[#FEE2E2] text-[#DC2626]' : 'bg-[#DCFCE7] text-[#16A34A]' }}">
                        {{ $stock === 0 ? __('admin.out_of_stock') : __('admin.in_stock') }}
                    </span>
                </div>
                <div class="flex gap-4 justify-end text-sm font-semibold">
                    <a href="{{ route('admin.products.edit', $product) }}" class="hover:text-gold">{{ __('admin.edit') }}</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[#DC2626] hover:opacity-70">{{ __('admin.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-muted text-sm">{{ __('admin.no_products') }}</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection
