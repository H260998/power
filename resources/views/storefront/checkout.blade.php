@extends('layouts.storefront')

@section('content')
<div class="px-6 md:px-16 py-10 md:py-12 pb-20">
    <div class="text-[28px] md:text-[34px] font-extrabold mb-8">{{ __('storefront.checkout') }}</div>

    @if ($errors->any())
        <div class="mb-6 bg-[#FEE2E2] text-[#DC2626] text-sm font-semibold px-4 py-3 rounded-xl max-w-xl">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" class="flex flex-col lg:flex-row gap-10 items-start">
        @csrf
        <div class="flex-1 w-full">
            <div class="text-base font-extrabold mb-2">{{ __('storefront.customer_info') }}</div>
            <p class="text-sm text-muted mb-5">{{ __('storefront.required_fields') }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('storefront.first_name') }} *" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
                <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('storefront.last_name') }} *" required class="border border-border rounded-xl px-4 py-3.5 text-sm">
            </div>
            <div class="mb-4">
                <div dir="ltr" class="flex items-center border border-border rounded-xl focus-within:ring-2 focus-within:ring-gold">
                    <span class="pl-4 pr-3 text-sm text-muted" aria-hidden="true">+216</span>
                    <input id="checkout-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ __('storefront.phone') }} *" aria-label="{{ __('storefront.phone') }}" aria-describedby="checkout-phone-hint{{ $errors->has('phone') ? ' checkout-phone-error' : '' }}" @error('phone') aria-invalid="true" @enderror inputmode="numeric" autocomplete="tel-national" pattern="[234579][0-9]{7}" minlength="8" maxlength="8" title="{{ __('storefront.phone_tunisia_invalid') }}" required class="w-full min-w-0 rounded-xl pr-4 py-3.5 text-sm focus:outline-none">
                </div>
                <p id="checkout-phone-hint" class="mt-1.5 text-xs text-muted">{{ __('storefront.phone_tunisia_hint') }}</p>
                @error('phone')
                    <p id="checkout-phone-error" class="mt-1.5 text-xs text-[#DC2626]">{{ $message }}</p>
                @enderror
            </div>
            <input type="text" name="address" value="{{ old('address') }}" placeholder="{{ __('storefront.address') }} *" required class="w-full border border-border rounded-xl px-4 py-3.5 text-sm mb-4">
            <select name="city" required class="w-full border border-border rounded-xl px-4 py-3.5 text-sm mb-4 bg-white">
                <option value="" disabled {{ old('city') ? '' : 'selected' }}>{{ __('storefront.city') }} *</option>
                @foreach (['Ariana','Béja','Ben Arous','Bizerte','Gabès','Gafsa','Jendouba','Kairouan','Kasserine','Kébili','Le Kef','Mahdia','Manouba','Médenine','Monastir','Nabeul','Sfax','Sidi Bouzid','Siliana','Sousse','Tataouine','Tozeur','Tunis','Zaghouan'] as $city)
                    <option value="{{ $city }}" {{ old('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
            <textarea name="notes" placeholder="{{ __('storefront.notes') }}" rows="3" class="w-full border border-border rounded-xl px-4 py-3.5 text-sm resize-y">{{ old('notes') }}</textarea>
        </div>

        <div class="w-full lg:w-[360px] shrink-0 bg-cream rounded-3xl p-7">
            <div class="text-[17px] font-extrabold mb-5">{{ __('storefront.order_summary') }}</div>
            <div class="flex flex-col gap-3.5 mb-5">
                @foreach ($lines as $line)
                    <div class="flex justify-between text-[13.5px]">
                        <span>{{ $line->product->name }} × {{ $line->qty }}</span>
                        <span class="font-bold">{{ number_format($line->lineTotal, 3) }} {{ __('storefront.currency') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-black/10 pt-3.5 flex justify-between text-[17px] font-extrabold mb-5">
                <span>{{ __('storefront.total') }}</span><span>{{ number_format($subtotal - $discount + $shipping, 3) }} {{ __('storefront.currency') }}</span>
            </div>
            <div class="bg-ink text-white rounded-xl px-3.5 py-3 text-[12.5px] font-bold text-center mb-5">💵 {{ __('storefront.cod_only') }}</div>
            <button type="submit" class="w-full bg-gold text-ink py-4 rounded-full font-bold text-[15px]">{{ __('storefront.place_order') }}</button>
        </div>
    </form>
</div>

<script nonce="{{ $cspNonce }}">
if (typeof fbq === 'function') {
    fbq('track', 'InitiateCheckout', @json($checkoutPayload), {eventID: '{{ $eventId }}'});
}
</script>
@endsection
