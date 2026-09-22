<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('brand.name') }} — {{ __('storefront.tagline') }}</title>
    <link rel="icon" type="image/webp" href="{{ asset(config('brand.logo')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($metaPixelId)
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $metaPixelId }}');
            fbq('track', 'PageView');
            @if (session('pixel_event'))
                @php $pe = session('pixel_event'); @endphp
                fbq('track', '{{ $pe['name'] }}', @json($pe['payload']), {eventID: '{{ $pe['id'] }}'});
            @endif
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1" /></noscript>
    @endif
</head>
<body class="bg-white text-ink font-sans antialiased min-h-screen flex flex-col overflow-x-hidden">

<header class="sticky top-0 z-50 bg-white border-b border-black/[0.06]">
    @if ($activePromo)
        @php
            $promoMessage = $activePromo->type === \App\Enums\PromoType::Percentage
                ? __('storefront.promo_banner_percentage', ['value' => (float) $activePromo->value])
                : __('storefront.promo_banner_fixed', ['value' => (float) $activePromo->value, 'currency' => __('storefront.currency')]);
        @endphp
        <a href="{{ route('shop.index') }}" class="block bg-gold text-ink px-4 py-2.5 text-center">
            <span class="mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-x-3 gap-y-1 text-[12px] sm:text-[13px]">
                <span aria-hidden="true">✦</span>
                <strong class="font-extrabold">{{ $promoMessage }}</strong>
                @if ($activePromo->min_order_amount)
                    <span class="border-s border-black/25 ps-3">{{ __('storefront.promo_banner_min_order', ['amount' => (float) $activePromo->min_order_amount, 'currency' => __('storefront.currency')]) }}</span>
                @endif
                @if ($activePromo->expires_at)
                    <span class="border-s border-black/25 ps-3">{{ __('storefront.promo_banner_expires', ['date' => $activePromo->expires_at->format('d/m/Y')]) }}</span>
                @endif
                <span class="font-bold underline underline-offset-2">{{ __('storefront.promo_banner_cta') }} →</span>
                <span aria-hidden="true">✦</span>
            </span>
        </a>
    @endif
    <div class="flex items-center justify-between h-[88px] px-6 lg:px-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="{{ config('brand.name') }}">
            <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.name') }}" class="h-11 w-auto object-contain">
            <span class="hidden sm:inline text-[18px] font-extrabold tracking-wide">{{ config('brand.name') }}</span>
        </a>

        <nav class="hidden md:flex items-center gap-5 lg:gap-10">
            <a href="{{ route('home') }}" class="text-[15px] font-semibold hover:text-gold">{{ __('storefront.nav_home') }}</a>
            <a href="{{ url('/boutique') }}" class="text-[15px] font-semibold hover:text-gold">{{ __('storefront.nav_collections') }}</a>
            <a href="{{ url('/order/track') }}" class="text-[15px] font-semibold hover:text-gold">{{ __('storefront.track_order') }}</a>
        </nav>

        <div class="flex items-center gap-4 md:gap-5">
            <div class="hidden sm:flex items-center gap-1 text-[12.5px] font-semibold text-muted">
                @foreach (['fr' => 'FR', 'ar' => 'AR', 'en' => 'EN'] as $code => $label)
                    <a href="{{ route('locale.switch', $code) }}"
                       class="px-1.5 py-1 rounded {{ app()->getLocale() === $code ? 'text-ink font-extrabold' : 'hover:text-ink' }}">{{ $label }}</a>@if (!$loop->last)<span class="text-border">·</span>@endif
                @endforeach
            </div>

            <button type="button" data-search-toggle aria-label="{{ __('storefront.search_placeholder') }}" class="text-ink">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>

            <a href="{{ url('/wishlist') }}" aria-label="{{ __('storefront.wishlist') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>
            </a>

            <a href="{{ url('/cart') }}" class="relative" aria-label="{{ __('storefront.cart') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                @if(($cartCount ?? 0) > 0)
                    <span class="absolute -top-2 -right-2.5 bg-gold text-ink text-[10.5px] font-extrabold w-[17px] h-[17px] rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                @endif
            </a>

        </div>
    </div>

    <div data-search-panel hidden class="border-t border-black/[0.06] bg-cream-soft px-6 lg:px-16 py-4">
        <form action="{{ url('/boutique') }}" method="GET" class="flex gap-3 max-w-xl mx-auto">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('storefront.search_placeholder') }}" autofocus
                   class="flex-1 border border-border rounded-full px-5 py-2.5 text-sm font-sans bg-white">
            <button type="submit" class="bg-ink text-white px-6 py-2.5 rounded-full text-sm font-bold">{{ __('storefront.search_button') }}</button>
        </form>
    </div>
</header>

<main class="flex-1">
    {{ $slot ?? '' }}
    @yield('content')
</main>

<footer class="bg-ink text-white px-6 md:px-16 pt-16 pb-8">
    <div class="flex flex-wrap justify-between gap-10 mb-12">
        <div class="max-w-[220px]">
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ asset(config('brand.logo')) }}" alt="{{ config('brand.name') }}" class="h-14 w-auto object-contain brightness-0 invert">
                <span class="text-lg font-extrabold">{{ config('brand.name') }}</span>
            </div>
            <div class="text-gray-400 text-[13.5px] leading-relaxed">{{ __('storefront.footer_tagline') }}</div>
        </div>
        <div>
            <div class="font-bold mb-3.5 text-sm">{{ __('storefront.footer_shop') }}</div>
            <div class="flex flex-col gap-2.5 text-gray-400 text-[13.5px]">
                <a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="hover:text-white">{{ __('storefront.footer_new_in') }}</a>
                <a href="{{ route('shop.index', ['category' => 't-shirts-polos']) }}" class="hover:text-white">{{ __('storefront.footer_tshirts') }}</a>
                <a href="{{ route('shop.index', ['category' => 'jackets-coats']) }}" class="hover:text-white">{{ __('storefront.footer_outerwear') }}</a>
                <a href="{{ route('shop.index', ['category' => 'sneakers']) }}" class="hover:text-white">{{ __('storefront.footer_footwear') }}</a>
            </div>
        </div>
        <div>
            <div class="font-bold mb-3.5 text-sm">{{ __('storefront.footer_support') }}</div>
            <div class="flex flex-col gap-2.5 text-gray-400 text-[13.5px]">
                <a href="{{ route('pages.help') }}" class="hover:text-white">{{ __('storefront.footer_help') }}</a>
                <a href="{{ route('order.track') }}" class="hover:text-white">{{ __('storefront.footer_track_order') }}</a>
            </div>
        </div>
        <div>
            <div class="font-bold mb-3.5 text-sm">{{ __('storefront.footer_company') }}</div>
            <div class="flex flex-col gap-2.5 text-gray-400 text-[13.5px]">
                <a href="{{ route('pages.about') }}" class="hover:text-white">{{ __('storefront.footer_about') }}</a>
                <a href="{{ route('pages.contact') }}" class="hover:text-white">{{ __('storefront.footer_contact') }}</a>
            </div>
        </div>
        <div class="min-w-[220px]">
            <div class="font-bold mb-3.5 text-sm">{{ __('storefront.footer_newsletter') }}</div>
            <div class="text-gray-400 text-[13.5px] mb-3">{{ __('storefront.footer_newsletter_text') }}</div>
            <form class="flex gap-2">
                <input type="email" placeholder="{{ __('storefront.footer_newsletter_placeholder') }}" class="flex-1 border-0 rounded-full px-4 py-2.5 text-[13px] font-sans text-ink">
                <button type="submit" class="bg-gold text-ink w-[38px] h-[38px] rounded-full flex items-center justify-center font-extrabold shrink-0">→</button>
            </form>
        </div>
    </div>
    <div class="border-t border-white/10 pt-6 flex flex-wrap justify-between gap-3 text-gray-400 text-[12.5px]">
        <span>&copy; {{ date('Y') }} {{ config('brand.name') }}. {{ __('storefront.footer_rights') }}</span>
        <div class="flex gap-5">
            <a href="{{ route('pages.privacy') }}" class="hover:text-white">{{ __('storefront.footer_privacy') }}</a>
            <a href="{{ route('pages.terms') }}" class="hover:text-white">{{ __('storefront.footer_terms') }}</a>
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('storefront.footer_admin') }}</a>
        </div>
    </div>
</footer>

</body>
</html>
