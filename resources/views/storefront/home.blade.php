@extends('layouts.storefront')

@php
    $t = fn (string $key, string $locale = null) => data_get($settings->get($key), $locale ?? app()->getLocale());
@endphp

@section('content')
<div class="flex flex-col md:flex-row items-center gap-14 px-6 md:px-16 py-16 md:py-20 bg-cream-soft">
    <div class="flex-1 max-w-[600px]">
        <div class="text-[32px] md:text-[40px] leading-tight font-extrabold">
            {{ $t('hero_title') }}<br>
            <span class="text-[52px] md:text-[68px]">{{ $t('hero_strength_word') }}</span><br>
            <span class="text-gold text-[38px] md:text-[50px]">{{ $t('hero_highlight') }}</span>
        </div>
        <div class="text-muted text-lg leading-relaxed my-6 max-w-[460px]">{{ $t('hero_subtitle') }}</div>
        <div class="flex flex-wrap gap-4 mb-8">
            <a href="{{ route('shop.index') }}" class="bg-ink text-white px-7 py-4 rounded-full text-base font-bold flex items-center gap-2">{{ __('storefront.hero_shop_now') }} →</a>
            <a href="{{ route('shop.index') }}" class="border-[1.3px] border-ink text-ink px-7 py-4 rounded-full text-base font-bold">{{ __('storefront.hero_explore') }}</a>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex">
                <div class="w-[38px] h-[38px] rounded-full bg-ink border-2 border-cream-soft -mr-2.5"></div>
                <div class="w-[38px] h-[38px] rounded-full bg-gold border-2 border-cream-soft -mr-2.5"></div>
                <div class="w-[38px] h-[38px] rounded-full bg-muted border-2 border-cream-soft -mr-2.5"></div>
                <div class="w-[38px] h-[38px] rounded-full bg-[#E5E1D6] border-2 border-cream-soft"></div>
            </div>
            <div>
                <div class="text-base font-bold">{{ __('storefront.social_proof') }}</div>
                <div class="text-gold text-[15px]">★★★★★</div>
            </div>
        </div>
    </div>

    <div class="flex-none md:flex-1 relative h-[420px] md:h-[520px] w-full flex items-center justify-center overflow-hidden" style="perspective:1200px">
        <div class="absolute inset-0" style="transform:translateY(75px)">
            <div class="absolute top-[38%] left-[38%] w-[260px] h-[260px] rounded-full bg-[#F4E6C8] opacity-55" style="transform:translate(-50%,-50%)"></div>
            <div class="absolute top-1/2 left-1/2 w-[360px] h-[300px] border border-dashed border-[#E7C878] rounded-full opacity-45" style="transform:translate(-50%,-50%) rotate(-8deg)"></div>
            <div class="absolute top-[8%] right-[8%] text-gold text-xl opacity-80">✦</div>
            <div class="absolute top-[22%] right-0 text-gold text-xs opacity-60">✦</div>
            <div class="absolute bottom-[16%] left-[4%] text-gold text-sm opacity-60">✦</div>
            <div class="absolute top-[2%] left-[34%] text-gold text-[13px] opacity-65">⚡</div>
            <div class="absolute bottom-[30%] right-[2%] text-gold text-[15px] opacity-60">⚡</div>
            <div class="absolute bottom-[2%] right-[30%] text-gold text-[13px] opacity-55">⚡</div>
            <div class="absolute top-[14%] left-[10%] text-gold text-base opacity-50">✦</div>
            <div class="absolute bottom-[8%] right-[14%] text-gold text-[11px] opacity-60">✦</div>
            <img id="hero-podium" src="{{ asset('assets/podium-transparent.png') }}" alt="" class="absolute top-1/2 left-1/2 w-[420px] md:w-[602px]" style="max-width:94%;transform:translate(-50%,-50%) scale(1.5);transform-origin:50% 84%;">
            @if ($heroSlides->isNotEmpty())
                <a id="hero-product-link" href="{{ $heroSlides->first()['url'] }}" class="absolute top-1/2 left-1/2 block" style="width:min(72%,360px);height:72%;max-height:350px;transform:translate(-50%,-80%);transition:opacity 0.5s ease;">
                    <img id="hero-product-img" src="{{ $heroSlides->first()['image'] }}" alt="{{ $heroSlides->first()['name'] }}" class="w-full h-full object-contain object-bottom" fetchpriority="high">
                </a>
            @endif
        </div>
    </div>
</div>

<div class="px-6 md:px-16 py-8 md:py-20">
    <div class="flex justify-between items-end mb-8 flex-wrap gap-4">
        <div>
            <div class="text-gold text-[13px] font-extrabold tracking-wide uppercase mb-2">{{ __('storefront.new_arrival') }}</div>
            <div class="text-[28px] md:text-[32px] font-extrabold">{{ __('storefront.new_collection') }}</div>
        </div>
        <a href="{{ route('shop.index') }}" class="bg-ink text-white px-5 py-3 rounded-full text-sm font-bold">{{ __('storefront.view_all') }} →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-6">
        @foreach ($featuredProducts as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</div>

<div class="flex flex-col md:flex-row gap-10 md:gap-14 px-6 md:px-16 pb-16 md:pb-24 items-center">
    @if ($featuredProducts->first()?->primaryImage())
        <img src="{{ $featuredProducts->first()->primaryImage()->url() }}" class="w-full md:w-[400px] h-[300px] md:h-[420px] rounded-3xl bg-cream object-contain p-5">
    @endif
    <div class="flex-1">
        <div class="text-gold text-[13px] font-extrabold tracking-wide uppercase mb-2">{{ __('storefront.about_label') }}</div>
        <div class="text-[28px] md:text-[32px] font-extrabold mb-4">{{ $t('about_title') }}</div>
        <div class="text-muted text-[15.5px] leading-relaxed max-w-[480px] mb-8">{{ $t('about_text') }}</div>
        <div class="flex flex-wrap gap-8">
            @for ($i = 1; $i <= 3; $i++)
                <div class="max-w-[150px]">
                    <div class="text-[13.5px] font-extrabold mb-1">{{ $t('feature'.$i.'_title') }}</div>
                    <div class="text-[12.5px] text-muted">{{ $t('feature'.$i.'_text') }}</div>
                </div>
            @endfor
        </div>
    </div>
</div>

<div class="mx-6 md:mx-16 mb-16 bg-cream rounded-3xl p-8 md:p-12 flex flex-col md:flex-row justify-between items-center gap-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gold rounded-full flex items-center justify-center shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111111" stroke-width="1.8"><path d="M3 5h18v14H3z"></path><path d="m3 6 9 7 9-7"></path></svg>
        </div>
        <div>
            <div class="text-[17px] font-extrabold">{{ $t('newsletter_title') }}</div>
            <div class="text-[13.5px] text-muted">{{ $t('newsletter_text') }}</div>
        </div>
    </div>
    <form class="flex gap-2.5 w-full md:w-auto">
        <input type="email" placeholder="{{ __('storefront.footer_newsletter_placeholder') }}" class="border-0 rounded-full px-5 py-3.5 flex-1 md:w-[260px] text-sm">
        <button type="submit" class="bg-ink text-white px-6 py-3.5 rounded-full text-sm font-bold whitespace-nowrap">{{ __('storefront.subscribe') }} →</button>
    </form>
</div>

<script type="application/json" id="podium-slides">@json($heroSlides)</script>
@endsection
