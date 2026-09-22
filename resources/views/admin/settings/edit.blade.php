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

    <div class="max-w-3xl mt-12">
        <div class="text-[22px] font-extrabold mb-1.5">{{ __('admin.settings_admin_accounts') }}</div>
        <div class="text-muted text-[14px] mb-6">{{ __('admin.settings_admin_accounts_help') }}</div>

        <div class="bg-white border border-border-light rounded-[20px] p-6 md:p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-6">
            <div class="text-[17px] font-extrabold mb-4">{{ __('admin.settings_existing_admins') }}</div>
            <div class="divide-y divide-border-light">
                @foreach ($admins as $admin)
                    <div class="flex flex-wrap items-center justify-between gap-2 py-3 first:pt-0 last:pb-0">
                        <div>
                            <div class="text-sm font-bold">{{ $admin->name }}</div>
                            <div class="text-[13px] text-muted">{{ $admin->email }}</div>
                        </div>
                        @if ($admin->getKey() === auth('admin')->id())
                            <span class="rounded-full bg-gold/20 px-3 py-1 text-[11px] font-extrabold">{{ __('admin.settings_you') }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <form method="POST" action="{{ route('admin.settings.admins.store') }}" class="bg-white border border-border-light rounded-[20px] p-6 md:p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                @csrf
                <div class="text-[17px] font-extrabold mb-1">{{ __('admin.settings_add_admin') }}</div>
                <div class="text-[12px] text-muted mb-5">{{ __('admin.settings_password_requirements') }}</div>

                @if ($errors->createAdmin->any())
                    <div class="mb-4 bg-[#FEE2E2] text-[#DC2626] text-[12px] font-semibold px-4 py-3 rounded-xl">{{ $errors->createAdmin->first() }}</div>
                @endif

                <label class="block text-[13px] font-bold mb-1.5" for="admin-name">{{ __('admin.settings_admin_name') }}</label>
                <input id="admin-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-4">

                <label class="block text-[13px] font-bold mb-1.5" for="admin-email">{{ __('admin.email') }}</label>
                <input id="admin-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-4">

                <label class="block text-[13px] font-bold mb-1.5" for="admin-password">{{ __('admin.password') }}</label>
                <input id="admin-password" type="password" name="password" required autocomplete="new-password" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-4">

                <label class="block text-[13px] font-bold mb-1.5" for="admin-password-confirmation">{{ __('admin.settings_confirm_password') }}</label>
                <input id="admin-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-5">

                <button type="submit" class="bg-ink text-white px-6 py-3 rounded-full text-sm font-bold">{{ __('admin.settings_add_admin_button') }}</button>
            </form>

            <form method="POST" action="{{ route('admin.settings.password.update') }}" class="bg-white border border-border-light rounded-[20px] p-6 md:p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                @csrf @method('PUT')
                <div class="text-[17px] font-extrabold mb-1">{{ __('admin.settings_change_password') }}</div>
                <div class="text-[12px] text-muted mb-5">{{ __('admin.settings_password_requirements') }}</div>

                @if ($errors->updatePassword->any())
                    <div class="mb-4 bg-[#FEE2E2] text-[#DC2626] text-[12px] font-semibold px-4 py-3 rounded-xl">{{ $errors->updatePassword->first() }}</div>
                @endif

                <label class="block text-[13px] font-bold mb-1.5" for="current-password">{{ __('admin.settings_current_password') }}</label>
                <input id="current-password" type="password" name="current_password" required autocomplete="current-password" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-4">

                <label class="block text-[13px] font-bold mb-1.5" for="new-password">{{ __('admin.settings_new_password') }}</label>
                <input id="new-password" type="password" name="new_password" required autocomplete="new-password" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-4">

                <label class="block text-[13px] font-bold mb-1.5" for="new-password-confirmation">{{ __('admin.settings_confirm_password') }}</label>
                <input id="new-password-confirmation" type="password" name="new_password_confirmation" required autocomplete="new-password" class="w-full border border-border rounded-xl px-4 py-3 text-sm mb-5">

                <button type="submit" class="bg-ink text-white px-6 py-3 rounded-full text-sm font-bold">{{ __('admin.settings_change_password_button') }}</button>
            </form>
        </div>
    </div>
@endsection
