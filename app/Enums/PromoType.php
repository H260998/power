<?php

namespace App\Enums;

enum PromoType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => __('admin.promo_type_percentage'),
            self::Fixed => __('admin.promo_type_fixed'),
        };
    }
}
