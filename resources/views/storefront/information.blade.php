@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-14 pb-20">
    <div class="mx-auto max-w-[900px]">
        <div class="mb-7 text-[13.5px] text-muted">
            <a href="{{ route('home') }}" class="hover:text-ink">{{ __('storefront.nav_home') }}</a> /
            <span class="font-bold text-ink">{{ $content['title'] }}</span>
        </div>

        <header class="mb-10 max-w-[720px]">
            <h1 class="mb-4 text-[32px] font-extrabold leading-tight md:text-[42px]">{{ $content['title'] }}</h1>
            <p class="text-[16px] leading-7 text-muted">{{ $content['intro'] }}</p>
        </header>

        @if ($pageKey === 'contact' && ($storeEmail || $storePhone))
            <div class="mb-8 grid gap-4 sm:grid-cols-2">
                @if ($storeEmail)
                    <a href="mailto:{{ $storeEmail }}" class="rounded-2xl border border-border-light bg-cream-soft p-5 hover:border-gold">
                        <div class="mb-1 text-xs font-extrabold uppercase tracking-wide text-muted">Email</div>
                        <div class="font-bold text-ink">{{ $storeEmail }}</div>
                    </a>
                @endif
                @if ($storePhone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $storePhone) }}" class="rounded-2xl border border-border-light bg-cream-soft p-5 hover:border-gold">
                        <div class="mb-1 text-xs font-extrabold uppercase tracking-wide text-muted">{{ __('storefront.phone') }}</div>
                        <div class="font-bold text-ink" dir="ltr">{{ $storePhone }}</div>
                    </a>
                @endif
            </div>
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($content['sections'] as $section)
                <section class="rounded-[20px] border border-border-light bg-white p-6 md:p-7">
                    <h2 class="mb-3 text-[18px] font-extrabold">{{ $section['title'] }}</h2>
                    <p class="whitespace-pre-line text-[14.5px] leading-7 text-muted">{{ $section['text'] }}</p>
                </section>
            @endforeach
        </div>

        <div class="mt-10 flex flex-wrap gap-3">
            @if ($pageKey === 'help' || $pageKey === 'contact')
                <a href="{{ route('order.track') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-bold text-white">{{ __('storefront.track_order') }}</a>
            @endif
            <a href="{{ route('shop.index') }}" class="rounded-full bg-gold px-6 py-3 text-sm font-bold text-ink">{{ __('storefront.continue_shopping') }}</a>
        </div>
    </div>
</div>
@endsection
