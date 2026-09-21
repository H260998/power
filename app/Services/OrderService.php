<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PromoCodeService $promoCodeService,
        private readonly SettingsService $settings,
        private readonly StockService $stock,
    ) {}

    /**
     * @throws \RuntimeException when the cart is empty or a variant no longer has enough stock.
     */
    public function createFromCart(array $customer): Order
    {
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw new \RuntimeException('Cannot place an order with an empty cart.');
        }

        $subtotal = $this->cart->subtotal();
        $discount = $this->cart->discount($this->promoCodeService);
        $shipping = $this->cart->shippingFee($this->settings);
        $promoCode = $this->cart->promoCode();

        return DB::transaction(function () use ($lines, $subtotal, $discount, $shipping, $promoCode, $customer) {
            foreach ($lines as $line) {
                $this->stock->decrement($line->variant->id, $line->qty);
            }

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'phone' => $customer['phone'],
                'address' => $customer['address'],
                'city' => $customer['city'],
                'notes' => $customer['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'discount_amount' => $discount,
                'total' => round($subtotal - $discount + $shipping, 3),
                'promo_code_id' => $promoCode?->id,
                'status' => OrderStatus::New,
                'locale' => app()->getLocale(),
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_variant_id' => $line->variant->id,
                    'product_name' => $line->product->name,
                    'size_label' => $line->variant->size?->label,
                    'color_name' => $line->variant->color?->name,
                    'unit_price' => $line->unitPrice,
                    'quantity' => $line->qty,
                    'line_total' => $line->lineTotal,
                ]);
            }

            if ($promoCode) {
                $this->promoCodeService->redeem($promoCode);
            }

            $this->cart->clear();

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'PWR-'.random_int(100000, 999999);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
