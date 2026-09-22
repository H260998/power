<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>{{ __('setup.title') }} — {{ config('brand.name') }}</title>
    <link rel="icon" type="image/webp" href="{{ asset(config('brand.logo')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream-soft px-4 py-10 font-sans text-ink antialiased">
    <main class="mx-auto w-full max-w-2xl">
        <div class="mb-7 text-center">
            <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.name') }}" class="mx-auto mb-3 h-20 w-auto object-contain">
            <h1 class="text-3xl font-extrabold">{{ __('setup.title') }}</h1>
            <p class="mt-2 text-sm leading-relaxed text-muted">{{ __('setup.subtitle') }}</p>
        </div>

        @if ($complete)
            <section class="rounded-[24px] border border-[#BBF7D0] bg-white p-8 text-center shadow-[0_20px_50px_rgba(17,17,17,0.06)]">
                <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-[#DCFCE7] text-2xl text-[#16A34A]">✓</div>
                <h2 class="text-2xl font-extrabold">{{ __('setup.complete_title') }}</h2>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-muted">{{ __('setup.complete_text', ['email' => $values['admin_email']]) }}</p>
                <a href="{{ route('admin.login') }}" class="mt-7 inline-flex rounded-full bg-ink px-7 py-3.5 text-sm font-bold text-white">{{ __('setup.open_admin') }}</a>
            </section>
        @else
            <section class="mb-6 rounded-[20px] border border-border-light bg-white p-6 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
                <h2 class="mb-4 text-[17px] font-extrabold">{{ __('setup.checks_title') }}</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <span>{{ __('setup.check_database') }} <span class="text-muted">({{ strtoupper($database['driver']) }}{{ $database['schema'] ? ' · '.$database['schema'] : '' }})</span></span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $database['ready'] ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#FEE2E2] text-[#DC2626]' }}">{{ $database['ready'] ? __('setup.ready') : __('setup.missing') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span>APP_KEY</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $appKeyReady ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#FEE2E2] text-[#DC2626]' }}">{{ $appKeyReady ? __('setup.ready') : __('setup.missing') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span>SETUP_TOKEN</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $tokenReady ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-[#FEE2E2] text-[#DC2626]' }}">{{ $tokenReady ? __('setup.ready') : __('setup.missing') }}</span>
                    </div>
                </div>
                @if (! $database['ready'] || ! $appKeyReady || ! $tokenReady)
                    <p class="mt-5 rounded-xl bg-[#FFF7E0] px-4 py-3 text-xs font-semibold leading-relaxed text-[#8A5A00]">{{ __('setup.configure_help') }}</p>
                @endif
            </section>

            <section class="rounded-[24px] border border-border-light bg-white p-6 md:p-8 shadow-[0_20px_50px_rgba(17,17,17,0.06)]">
                <h2 class="text-xl font-extrabold">{{ __('setup.form_title') }}</h2>
                <p class="mt-1 mb-6 text-sm text-muted">{{ __('setup.form_help') }}</p>

                @if ($errors)
                    <div class="mb-5 rounded-xl bg-[#FEE2E2] px-4 py-3 text-sm font-semibold text-[#DC2626]">
                        <ul class="list-disc space-y-1 ps-5">
                            @foreach ($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('setup.store') }}" class="space-y-4">
                    <div>
                        <label for="setup-token" class="mb-1.5 block text-[13px] font-bold">{{ __('setup.token') }}</label>
                        <input id="setup-token" type="password" name="setup_token" required autocomplete="off" class="w-full rounded-xl border border-border px-4 py-3 text-sm">
                        <p class="mt-1.5 text-[11px] text-muted">{{ __('setup.token_help') }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="admin-name" class="mb-1.5 block text-[13px] font-bold">{{ __('setup.admin_name') }}</label>
                            <input id="admin-name" type="text" name="admin_name" value="{{ $values['admin_name'] ?? '' }}" required autocomplete="name" class="w-full rounded-xl border border-border px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="admin-email" class="mb-1.5 block text-[13px] font-bold">{{ __('setup.admin_email') }}</label>
                            <input id="admin-email" type="email" name="admin_email" value="{{ $values['admin_email'] ?? '' }}" required autocomplete="email" class="w-full rounded-xl border border-border px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="admin-password" class="mb-1.5 block text-[13px] font-bold">{{ __('setup.admin_password') }}</label>
                            <input id="admin-password" type="password" name="admin_password" required autocomplete="new-password" class="w-full rounded-xl border border-border px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="admin-password-confirmation" class="mb-1.5 block text-[13px] font-bold">{{ __('setup.admin_password_confirmation') }}</label>
                            <input id="admin-password-confirmation" type="password" name="admin_password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-border px-4 py-3 text-sm">
                        </div>
                    </div>
                    <p class="text-[11px] text-muted">{{ __('setup.password_help') }}</p>

                    <button type="submit" @disabled(! $database['ready'] || ! $appKeyReady || ! $tokenReady)
                            class="w-full rounded-full bg-ink px-7 py-3.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">
                        {{ __('setup.install_button') }}
                    </button>
                </form>

                <p class="mt-5 text-center text-[11px] leading-relaxed text-muted">{{ __('setup.lock_help') }}</p>
            </section>
        @endif
    </main>
</body>
</html>
