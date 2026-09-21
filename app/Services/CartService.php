<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';
    private const PROMO_SESSION_KEY = 'cart_promo_code';

    public function add(int $variantId, int $qty = 1): void
    {
        $variant = ProductVariant::findOrFail($variantId);
        $cart = $this->raw();

        $newQty = min(($cart[$variantId] ?? 0) + $qty, max($variant->stock_quantity, 0));

        if ($newQty <= 0) {
            return;
        }

        $cart[$variantId] = $newQty;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $variantId, int $qty): void
    {
        $cart = $this->raw();

        if (! isset($cart[$variantId])) {
            return;
        }

        $variant = ProductVariant::find($variantId);
        $max = $variant ? $variant->stock_quantity : 0;
        $qty = max(1, min($qty, $max));

        $cart[$variantId] = $qty;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::PROMO_SESSION_KEY);
    }

    public function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function lines(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        return ProductVariant::with(['product.images', 'size', 'color'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function (ProductVariant $variant) use ($cart) {
                $qty = min($cart[$variant->id], $variant->stock_quantity);

                return (object) [
                    'variant' => $variant,
                    'product' => $variant->product,
                    'qty' => $qty,
                    'unitPrice' => (float) $variant->product->price,
                    'lineTotal' => round((float) $variant->product->price * $qty, 3),
                ];
            });
    }

    public function subtotal(): float
    {
        return round($this->lines()->sum('lineTotal'), 3);
    }

    public function promoCode(): ?PromoCode
    {
        $id = Session::get(self::PROMO_SESSION_KEY);

        return $id ? PromoCode::find($id) : null;
    }

    public function setPromoCode(?PromoCode $promoCode): void
    {
        if ($promoCode) {
            Session::put(self::PROMO_SESSION_KEY, $promoCode->id);
        } else {
            Session::forget(self::PROMO_SESSION_KEY);
        }
    }

    public function discount(PromoCodeService $promoCodeService): float
    {
        $promo = $this->promoCode();

        return $promo ? $promoCodeService->calculateDiscount($promo, $this->subtotal()) : 0.0;
    }

    public function shippingFee(SettingsService $settings): float
    {
        if ($this->isEmpty()) {
            return 0.0;
        }

        $subtotal = $this->subtotal();
        $threshold = (float) $settings->get('free_shipping_threshold', 0);

        if ($threshold > 0 && $subtotal >= $threshold) {
            return 0.0;
        }

        return (float) $settings->get('shipping_fee', 0);
    }

    public function total(PromoCodeService $promoCodeService, SettingsService $settings): float
    {
        return round($this->subtotal() - $this->discount($promoCodeService) + $this->shippingFee($settings), 3);
    }
}
