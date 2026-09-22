<?php

namespace App\Http\Controllers\Storefront;

use App\Enums\TrackingEventName;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\MetaConversionApiService;
use App\Services\PromoCodeService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PromoCodeService $promoCodeService,
        private readonly SettingsService $settings,
        private readonly MetaConversionApiService $tracking,
    ) {}

    public function index(): View
    {
        return view('storefront.cart', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'discount' => $this->cart->discount($this->promoCodeService),
            'shipping' => $this->cart->shippingFee($this->settings),
            'total' => $this->cart->total($this->promoCodeService, $this->settings),
            'promoCode' => $this->cart->promoCode(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $qty = $request->integer('qty', 1);
        $this->cart->add($request->integer('variant_id'), $qty);
        $this->trackAddToCart($request->integer('variant_id'), $qty);

        return back()->with('status', __('storefront.added_to_cart'));
    }

    public function buyNow(Request $request): RedirectResponse
    {
        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $qty = $request->integer('qty', 1);
        $this->cart->add($request->integer('variant_id'), $qty);
        $this->trackAddToCart($request->integer('variant_id'), $qty);

        return redirect()->route('checkout.index');
    }

    public function update(Request $request, int $variantId): RedirectResponse
    {
        $request->validate(['qty' => ['required', 'integer', 'min:1', 'max:100']]);

        $this->cart->update($variantId, $request->integer('qty'));

        return back();
    }

    public function remove(int $variantId): RedirectResponse
    {
        $this->cart->remove($variantId);

        return back();
    }

    public function applyPromo(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $result = $this->promoCodeService->validate($request->string('code'), $this->cart->subtotal());

        if (! $result['valid']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        $this->cart->setPromoCode($result['promoCode']);

        return back()->with('status', __('storefront.promo_applied'));
    }

    public function removePromo(): RedirectResponse
    {
        $this->cart->setPromoCode(null);

        return back();
    }

    private function trackAddToCart(int $variantId, int $qty): void
    {
        $variant = ProductVariant::with('product')->find($variantId);

        if (! $variant) {
            return;
        }

        $payload = [
            'content_ids' => [$variant->product->id],
            'value' => (float) $variant->product->price * $qty,
            'currency' => 'TND',
        ];

        $eventId = $this->tracking->track(TrackingEventName::AddToCart, $payload, product: $variant->product);

        session()->flash('pixel_event', ['name' => 'AddToCart', 'id' => $eventId, 'payload' => $payload]);
    }
}
