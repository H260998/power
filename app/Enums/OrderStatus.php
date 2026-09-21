<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => __('admin.status_new'),
            self::Confirmed => __('admin.status_confirmed'),
            self::Preparing => __('admin.status_preparing'),
            self::Shipped => __('admin.status_shipped'),
            self::Delivered => __('admin.status_delivered'),
            self::Cancelled => __('admin.status_cancelled'),
        };
    }

    /**
     * Badge colors lifted directly (exact hex) from the design prototype's dashboard badges.
     * "Confirmed" has no prototype reference, so it uses an indigo pairing consistent with the others.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-[#F1F5F9] text-[#334155]',
            self::Confirmed => 'bg-[#E0E7FF] text-[#4338CA]',
            self::Preparing => 'bg-[#FEF3C7] text-[#B45309]',
            self::Shipped => 'bg-[#DBEAFE] text-[#2563EB]',
            self::Delivered => 'bg-[#DCFCE7] text-[#16A34A]',
            self::Cancelled => 'bg-[#FEE2E2] text-[#DC2626]',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
