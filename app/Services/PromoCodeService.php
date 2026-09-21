<?php

namespace App\Services;

use App\Enums\PromoType;
use App\Models\PromoCode;

class PromoCodeService
{
    /**
     * @return array{valid: bool, message?: string, promoCode?: PromoCode}
     */
    public function validate(string $code, float $orderSubtotal): array
    {
        $promoCode = PromoCode::whereRaw('LOWER(code) = ?', [mb_strtolower(trim($code))])->first();

        if (! $promoCode) {
            return ['valid' => false, 'message' => __('storefront.promo_not_found')];
        }

        if (! $promoCode->isCurrentlyValid()) {
            return ['valid' => false, 'message' => __('storefront.promo_expired')];
        }

        if ($promoCode->min_order_amount && $orderSubtotal < $promoCode->min_order_amount) {
            return [
                'valid' => false,
                'message' => __('storefront.promo_min_order', ['amount' => number_format((float) $promoCode->min_order_amount, 3)]),
            ];
        }

        return ['valid' => true, 'promoCode' => $promoCode];
    }

    public function calculateDiscount(PromoCode $promoCode, float $subtotal): float
    {
        $discount = $promoCode->type === PromoType::Percentage
            ? $subtotal * ((float) $promoCode->value / 100)
            : (float) $promoCode->value;

        return round(min($discount, $subtotal), 3);
    }

    public function redeem(PromoCode $promoCode): void
    {
        $promoCode->increment('used_count');
    }
}
