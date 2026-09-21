<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('admin.dashboard') }} — {{ config('brand.name') }} Admin</title>
    <link rel="icon" type="image/webp" href="{{ asset(config('brand.logo')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-ink font-sans antialiased overflow-x-hidden">

@php
    $navItem = function (string $routeName, string $label, string $icon) {
        $active = request()->routeIs($routeName.'*');
        return compact('active', 'label', 'icon', 'routeName');
    };
    $navItems = [
        $navItem('admin.dashboard', __('admin.nav_dashboard'), 'dashboard'),
        $navItem('admin.products.*', __('admin.nav_products'), 'products'),
        $navItem('admin.categories.*', __('admin.nav_categories'), 'categories'),
        $navItem('admin.orders.*', __('admin.nav_orders'), 'orders'),
        $navItem('admin.promo-codes.*', __('admin.nav_promo_codes'), 'promo'),
        $navItem('admin.marketing', __('admin.nav_marketing'), 'marketing'),
        $navItem('admin.statistics', __('admin.nav_statistics'), 'statistics'),
        $navItem('admin.settings.edit', __('admin.nav_settings'), 'settings'),
    ];
@endphp

<div class="flex items-center justify-between bg-ink text-white px-5 py-4 md:hidden">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
        <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.name') }}" class="h-10 w-auto object-contain brightness-0 invert">
        <span class="text-[17px] font-extrabold">{{ config('brand.name') }}</span>
    </a>
    <button type="button" data-admin-nav-toggle class="text-sm font-bold cursor-pointer">☰ {{ __('admin.menu') }}</button>
</div>

<div class="flex flex-col md:flex-row min-h-screen">
    <aside data-admin-sidebar class="hidden md:flex w-full md:w-[260px] md:shrink-0 bg-ink text-white p-6 md:p-8 md:pt-8 flex-col gap-1">
        <a href="{{ route('admin.dashboard') }}" class="hidden md:flex items-center gap-2.5 mb-9 pl-2">
            <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.name') }}" class="h-12 w-auto object-contain brightness-0 invert">
            <span class="text-[17px] font-extrabold">{{ config('brand.name') }}</span>
        </a>

        @foreach ($navItems as $item)
            @if (\Illuminate\Support\Facades\Route::has(str_replace('.*', '.index', $item['routeName'])) || \Illuminate\Support\Facades\Route::has($item['routeName']))
                <a href="{{ \Illuminate\Support\Facades\Route::has($item['routeName']) ? route($item['routeName']) : route(str_replace('.*', '.index', $item['routeName'])) }}"
                   class="px-3.5 py-3 rounded-[10px] text-[14.5px] font-semibold {{ $item['active'] ? 'bg-gold text-ink' : 'text-white hover:bg-white/10' }}">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="px-3.5 py-3 rounded-[10px] text-[14.5px] font-semibold text-gray-500 cursor-not-allowed">{{ $item['label'] }}</span>
            @endif
        @endforeach

        <div class="md:mt-auto pt-6 border-t border-white/10">
            <a href="{{ route('home') }}" class="text-[13.5px] text-gray-400 font-semibold hover:text-white">← {{ __('admin.back_to_storefront') }}</a>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-[13.5px] text-gray-400 font-semibold hover:text-white">{{ __('admin.logout') }}</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 min-w-0 p-6 md:p-10 lg:p-12 overflow-x-auto">
        @if (session('status'))
            <div class="mb-6 bg-[#DCFCE7] text-[#16A34A] text-sm font-semibold px-5 py-3 rounded-xl">{{ session('status') }}</div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </div>
</div>

</body>
</html>
