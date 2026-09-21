@extends('layouts.admin')

@section('content')
    @php $isEdit = $promoCode->exists; @endphp

    <div class="text-[28px] font-extrabold mb-1.5">{{ $isEdit ? __('admin.edit_promo') : __('admin.add_promo') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.nav_promo_codes') }}</div>

    @if ($errors->any())
        <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.promo-codes.update', $promoCode) : route('admin.promo-codes.store') }}" class="max-w-xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="mb-5">
            <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_code') }}</label>
            <input type="text" name="code" value="{{ old('code', $promoCode->code) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm uppercase">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_type') }}</label>
                <select name="type" required class="w-full border border-border rounded-xl px-4 py-3 text-sm bg-white">
                    @foreach (\App\Enums\PromoType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $promoCode->type?->value) === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_value') }}</label>
                <input type="number" step="0.001" min="0" name="value" value="{{ old('value', $promoCode->value) }}" required class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_min_order') }}</label>
                <input type="number" step="0.001" min="0" name="min_order_amount" value="{{ old('min_order_amount', $promoCode->min_order_amount) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_max_uses') }}</label>
                <input type="number" min="1" name="max_uses" value="{{ old('max_uses', $promoCode->max_uses) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_starts_at') }}</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $promoCode->starts_at?->format('Y-m-d')) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.promo_expires_at') }}</label>
                <input type="date" name="expires_at" value="{{ old('expires_at', $promoCode->expires_at?->format('Y-m-d')) }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
            </div>
        </div>

        <label class="flex items-center gap-2 text-[13px] font-semibold mb-7">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promoCode->exists ? $promoCode->is_active : true)) class="rounded border-border">
            {{ __('admin.active') }}
        </label>

        <div class="flex gap-3">
            <button type="submit" class="bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
            <a href="{{ route('admin.promo-codes.index') }}" class="px-7 py-3.5 rounded-full text-sm font-bold border border-border">{{ __('admin.cancel') }}</a>
        </div>
    </form>
@endsection
