@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-7">
        <div>
            <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_promo_codes') }}</div>
            <div class="text-muted text-[15px]">{{ __('admin.promo_codes_count', ['count' => $promoCodes->total()]) }}</div>
        </div>
        <a href="{{ route('admin.promo-codes.create') }}" class="bg-ink text-white px-6 py-3.5 rounded-full text-sm font-bold">+ {{ __('admin.add_promo') }}</a>
    </div>

    <div class="bg-white border border-border-light rounded-[20px] px-7 py-2 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        <div class="grid grid-cols-[1.2fr_1fr_1fr_1fr_1fr_1.2fr] gap-3 py-4 border-b border-border-light text-muted text-[12.5px] font-bold uppercase tracking-wide">
            <div>{{ __('admin.promo_code') }}</div>
            <div>{{ __('admin.promo_value') }}</div>
            <div>{{ __('admin.promo_usage') }}</div>
            <div>{{ __('admin.promo_expires') }}</div>
            <div>{{ __('admin.status') }}</div>
            <div></div>
        </div>

        @forelse ($promoCodes as $promo)
            <div class="grid grid-cols-[1.2fr_1fr_1fr_1fr_1fr_1.2fr] gap-3 py-4 border-b border-[#F8F8F8] items-center">
                <div class="font-bold text-[14.5px] tracking-wide">{{ $promo->code }}</div>
                <div class="text-sm">{{ $promo->type === \App\Enums\PromoType::Percentage ? rtrim(rtrim($promo->value, '0'), '.').'%' : number_format($promo->value, 3).' '.__('storefront.currency') }}</div>
                <div class="text-sm text-muted">{{ $promo->used_count }}{{ $promo->max_uses ? ' / '.$promo->max_uses : '' }}</div>
                <div class="text-sm text-muted">{{ $promo->expires_at?->format('d/m/Y') ?? '—' }}</div>
                <div>
                    <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full {{ $promo->isCurrentlyValid() ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#F1F5F9] text-[#334155]' }}">
                        {{ $promo->isCurrentlyValid() ? __('admin.active') : __('admin.inactive') }}
                    </span>
                </div>
                <div class="flex gap-3 justify-end text-sm font-semibold">
                    <form method="POST" action="{{ route('admin.promo-codes.toggle', $promo) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="hover:text-gold">{{ $promo->is_active ? __('admin.disable') : __('admin.activate') }}</button>
                    </form>
                    <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="hover:text-gold">{{ __('admin.edit') }}</a>
                    <form method="POST" action="{{ route('admin.promo-codes.destroy', $promo) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[#DC2626] hover:opacity-70">{{ __('admin.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-muted text-sm">{{ __('admin.no_promos') }}</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $promoCodes->links() }}</div>
@endsection
