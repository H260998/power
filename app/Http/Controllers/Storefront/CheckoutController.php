<?php

namespace App\Http\Controllers\Storefront;

use App\Enums\TrackingEventName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Services\CartService;
use App\Services\MetaConversionApiService;
use App\Services\OrderService;
use App\Services\PromoCodeService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PromoCodeService $promoCodeService,
        private readonly SettingsService $settings,
        private readonly MetaConversionApiService $tracking,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $checkoutPayload = [
            'content_ids' => $this->cart->lines()->pluck('product.id')->all(),
            'value' => $this->cart->subtotal(),
            'currency' => 'TND',
        ];
        $eventId = $this->tracking->track(TrackingEventName::InitiateCheckout, $checkoutPayload);

        return view('storefront.checkout', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'discount' => $this->cart->discount($this->promoCodeService),
            'shipping' => $this->cart->shippingFee($this->settings),
            'total' => $this->cart->total($this->promoCodeService, $this->settings),
            'eventId' => $eventId,
            'checkoutPayload' => $checkoutPayload,
        ]);
    }

    public function store(CheckoutRequest $request, OrderService $orders): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        try {
            $order = $orders->createFromCart($request->validated());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['cart' => __('storefront.checkout_stock_error')])->withInput();
        }

        $eventId = $this->tracking->track(TrackingEventName::Purchase, [
            'content_ids' => $order->items->pluck('product_id')->filter()->all(),
            'value' => (float) $order->total,
            'currency' => 'TND',
        ], order: $order);

        $order->update(['meta' => ['purchase_event_id' => $eventId]]);

        return redirect()->route('order.confirmation', $order->order_number);
    }
}
