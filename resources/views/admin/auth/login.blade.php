<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.login') }} — POWER Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-soft text-ink font-sans antialiased min-h-screen flex items-center justify-center px-4">

<div class="w-full max-w-[400px]">
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="text-[26px] font-extrabold tracking-wide">POWER<span class="text-gold">.</span></a>
        <div class="text-muted text-sm mt-2">{{ __('admin.login_subtitle') }}</div>
    </div>

    <div class="bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.06)]">
        @if ($errors->any())
            <div class="mb-5 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}" class="flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-border rounded-xl px-4 py-3 text-sm font-sans">
            </div>
            <div>
                <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.password') }}</label>
                <input type="password" name="password" required
                       class="w-full border border-border rounded-xl px-4 py-3 text-sm font-sans">
            </div>
            <label class="flex items-center gap-2 text-[13px] text-muted">
                <input type="checkbox" name="remember" class="rounded border-border">
                {{ __('admin.remember_me') }}
            </label>
            <button type="submit" class="bg-ink text-white rounded-full py-3.5 text-[15px] font-bold mt-2">
                {{ __('admin.login_button') }}
            </button>
        </form>
    </div>
</div>

</body>
</html>
