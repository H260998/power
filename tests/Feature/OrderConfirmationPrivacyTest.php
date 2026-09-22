<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderConfirmationPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_confirmation_requires_the_checkout_session(): void
    {
        $order = Order::create([
            'order_number' => 'PWR-123456',
            'first_name' => 'Client',
            'last_name' => 'Test',
            'phone' => '20123456',
            'address' => '1 rue Exemple',
            'city' => 'Tunis',
            'subtotal' => 50,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'total' => 50,
            'status' => OrderStatus::New,
            'locale' => 'fr',
        ]);

        $this->get(route('order.confirmation', $order->order_number))->assertNotFound();

        $this->withSession(['confirmed_orders' => [$order->order_number => true]])
            ->get(route('order.confirmation', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number);
    }
}
