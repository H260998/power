@extends('layouts.admin')

@section('content')
    <div class="text-[28px] font-extrabold mb-1.5">{{ __('admin.nav_marketing') }}</div>
    <div class="text-muted text-[15px] mb-8">{{ __('admin.marketing_subtitle') }}</div>

    @if (session('status'))
        <div class="mb-5 bg-[#DCFCE7] text-[#16A34A] text-sm font-semibold px-4 py-3 rounded-xl max-w-2xl">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.marketing.update') }}" class="max-w-2xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-8">
        @csrf @method('PUT')

        <div class="text-[17px] font-extrabold mb-2">{{ __('admin.marketing_meta_title') }}</div>
        <p class="text-muted text-[13px] mb-5">{{ __('admin.marketing_meta_help') }}</p>

        <div class="mb-5">
            <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.marketing_pixel_id') }}</label>
            <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $metaPixelId) }}" placeholder="1234567890123456" class="w-full border border-border rounded-xl px-4 py-3 text-sm">
        </div>
        <div class="mb-7">
            <label class="block text-[13px] font-bold mb-1.5">{{ __('admin.marketing_capi_token') }}</label>
            <input type="password" name="conversion_api_token" value="{{ old('conversion_api_token', $conversionApiToken) }}" placeholder="EAAG..." class="w-full border border-border rounded-xl px-4 py-3 text-sm">
        </div>

        <button type="submit" class="bg-ink text-white px-7 py-3.5 rounded-full text-sm font-bold">{{ __('admin.save') }}</button>
    </form>

    <div class="max-w-2xl bg-white border border-border-light rounded-[20px] p-8 shadow-[0_20px_40px_rgba(17,17,17,0.04)] mb-8">
        <div class="text-[17px] font-extrabold mb-5">{{ __('admin.marketing_events_summary') }}</div>
        <div class="grid grid-cols-4 gap-4">
            @foreach (['ViewContent', 'AddToCart', 'InitiateCheckout', 'Purchase'] as $eventName)
                <div class="bg-cream rounded-2xl p-4 text-center">
                    <div class="text-[22px] font-extrabold">{{ $eventCounts[$eventName] ?? 0 }}</div>
                    <div class="text-[11.5px] text-muted font-semibold mt-1">{{ $eventName }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="max-w-2xl bg-white border border-border-light rounded-[20px] px-7 py-2 shadow-[0_20px_40px_rgba(17,17,17,0.04)]">
        <div class="text-[17px] font-extrabold pt-5 mb-4 px-0">{{ __('admin.marketing_recent_events') }}</div>
        <div class="grid grid-cols-[1fr_1fr_1fr] gap-3 py-3 border-b border-border-light text-muted text-[12px] font-bold uppercase tracking-wide">
            <div>{{ __('admin.marketing_event') }}</div>
            <div>{{ __('admin.date') }}</div>
            <div>ID</div>
        </div>
        @forelse ($recentEvents as $event)
            <div class="grid grid-cols-[1fr_1fr_1fr] gap-3 py-3 border-b border-[#F8F8F8] text-[13px]">
                <div class="font-semibold">{{ $event->event_name }}</div>
                <div class="text-muted">{{ $event->created_at->format('d/m/Y H:i') }}</div>
                <div class="text-muted truncate">{{ str($event->event_id)->limit(12) }}</div>
            </div>
        @empty
            <div class="py-8 text-center text-muted text-sm">{{ __('admin.marketing_no_events') }}</div>
        @endforelse
    </div>
@endsection
