@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-24 md:py-36 text-center">
    <div class="text-[80px] md:text-[120px] font-extrabold text-cream leading-none mb-2" style="-webkit-text-stroke: 2px #111111; color: transparent;">404</div>
    <div class="text-[24px] md:text-[28px] font-extrabold mb-3">{{ __('storefront.not_found_title') }}</div>
    <div class="text-muted text-[15px] mb-9 max-w-md mx-auto">{{ __('storefront.not_found_text') }}</div>
    <a href="{{ route('home') }}" class="inline-block bg-ink text-white px-8 py-4 rounded-full font-bold text-[14.5px]">{{ __('storefront.back_home') }}</a>
</div>
@endsection
