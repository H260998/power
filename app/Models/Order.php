<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'first_name',
        'last_name',
        'phone',
        'address',
        'city',
        'notes',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'total',
        'promo_code_id',
        'status',
        'locale',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:3',
            'shipping_fee' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'total' => 'decimal:3',
            'status' => OrderStatus::class,
            'meta' => 'array',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
