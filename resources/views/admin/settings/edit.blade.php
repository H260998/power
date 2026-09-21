@extends('layouts.admin')

@section('content')
    @php
        $t = fn (string $key, string $locale) => data_get($settings->get($key), $locale);
    @endphp

    <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_settings') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.settings_subtitle') }}</div>

    @if ($errors->any())
        <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl flex flex-col gap-6">
        @csrf @method('PUT')

        <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.settings_store_info') }}</div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.settings_store_name') }}</label>
                    <input type="text" name="store_name" value="{{ $settings->get('store_name') }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.settings_store_email') }}</label>
                    <input type="email" name="store_email" value="{{ $settings->get('store_email') }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.settings_store_phone') }}</label>
                    <input type="text" name="store_phone" value="{{ $settings->get('store_phone') }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.settings_shipping') }}</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.settings_shipping_fee') }} ({{ __('storefront.currency') }})</label>
                    <input type="number" step="0.001" min="0" name="shipping_fee" value="{{ $settings->get('shipping_fee') }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.settings_free_shipping_threshold') }}</label>
                    <input type="number" step="0.001" min="0" name="free_shipping_threshold" value="{{ $settings->get('free_shipping_threshold') }}" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
            <div class="text-[17px] font-extrabold mb-5">{{ __('admin.settings_homepage') }}</div>

            @foreach ([
                ['hero_title', __('admin.settings_hero_title')],
                ['hero_strength_word', __('admin.settings_hero_strength_word')],
                ['hero_highlight', __('admin.settings_hero_highlight')],
                ['hero_subtitle', __('admin.settings_hero_subtitle')],
                ['about_title', __('admin.settings_about_title')],
                ['about_text', __('admin.settings_about_text')],
                ['feature1_title', __('admin.settings_feature_title', ['n' => 1])],
                ['feature1_text', __('admin.settings_feature_text', ['n' => 1])],
                ['feature2_title', __('admin.settings_feature_title', ['n' => 2])],
                ['feature2_text', __('admin.settings_feature_text', ['n' => 2])],
                ['feature3_title', __('admin.settings_feature_title', ['n' => 3])],
                ['feature3_text', __('admin.settings_feature_text', ['n' => 3])],
                ['newsletter_title', __('admin.settings_newsletter_title')],
                ['newsletter_text', __('admin.settings_newsletter_text')],
            ] as [$key, $label])
                <div class="mb-5 pb-5 border-b border-border-light last:border-0 last:mb-0 last:pb-0">
                    <div class="text-[13px] font-bold mb-2">{{ $label }}</div>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" name="{{ $key }}[fr]" value="{{ $t($key, 'fr') }}" placeholder="FR" class="w-full border border-border rounded-xl px-3 py-2.5 text-sm">
                        <input type="text" name="{{ $key }}[en]" value="{{ $t($key, 'en') }}" placeholder="EN" class="w-full border border-border rounded-xl px-3 py-2.5 text-sm">
                        <input type="text" name="{{ $key }}[ar]" value="{{ $t($key, 'ar') }}" placeholder="AR" dir="rtl" class="w-full border border-border rounded-xl px-3 py-2.5 text-sm">
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            <button type="submit" class="bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
        </div>
    </form>
@endsection
