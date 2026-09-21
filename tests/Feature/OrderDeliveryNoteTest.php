<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDeliveryNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_notes_require_admin_authentication(): void
    {
        $order = $this->order();

        $this->get(route('admin.orders.delivery-note', $order))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_print_saved_order_details_without_changing_the_order(): void
    {
        $this->withoutVite();
        $order = $this->order();
        $before = $order->refresh()->getAttributes();
        $admin = Admin::create(['name' => 'Admin', 'email' => 'delivery@example.test', 'password' => 'test-password-only']);

        $this->actingAs($admin, 'admin')->withSession(['locale' => 'fr']);
        $this->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee(route('admin.orders.delivery-note', $order), false);

        $this->get(route('admin.orders.delivery-note', $order))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('Bon de livraison')
            ->assertSee('PWR-TEST-DELIVERY')
            ->assertSee('Test Client')
            ->assertSee('20123456')
            ->assertSee('1 rue de Test')
            ->assertSee('Sfax')
            ->assertSee('Appeler avant de livrer.')
            ->assertSee('Produit conservé après suppression')
            ->assertSee('Noir')
            ->assertSee('40.000 DT')
            ->assertSee('-5.000 DT')
            ->assertSee('7.990 DT')
            ->assertSee('42.990 DT')
            ->assertSee('Montant à encaisser')
            ->assertDontSee('data-admin-sidebar', false)
            ->assertDontSee('Mettre à jour le statut');

        $this->assertSame($before, $order->refresh()->getAttributes());
    }

    public function test_arabic_note_handles_missing_optional_fields_and_cancelled_orders(): void
    {
        $order = $this->order();
        $order->update(['notes' => null, 'discount_amount' => 0, 'total' => 47.990, 'status' => OrderStatus::Cancelled]);
        $order->items()->update(['size_label' => null, 'color_name' => null]);
        $admin = Admin::create(['name' => 'Admin', 'email' => 'arabic@example.test', 'password' => 'test-password-only']);

        $this->actingAs($admin, 'admin')->withSession(['locale' => 'ar'])
            ->get(route('admin.orders.delivery-note', $order))
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee(trans('admin.delivery_note', [], 'ar'))
            ->assertSee(trans('admin.status_cancelled', [], 'ar'))
            ->assertDontSee(trans('admin.amount_to_collect', [], 'ar'))
            ->assertDontSee(trans('admin.delivery_notes', [], 'ar'));
    }

    private function order(): Order
    {
        $order = Order::create([
            'order_number' => 'PWR-TEST-DELIVERY',
            'first_name' => 'Test', 'last_name' => 'Client',
            'phone' => '20123456', 'address' => '1 rue de Test', 'city' => 'Sfax',
            'notes' => 'Appeler avant de livrer.',
            'subtotal' => 40, 'discount_amount' => 5, 'shipping_fee' => 7.990, 'total' => 42.990,
            'status' => OrderStatus::New,
        ]);
        $order->items()->create([
            'product_name' => 'Produit conservé après suppression',
            'size_label' => 'S', 'color_name' => 'Noir',
            'unit_price' => 20, 'quantity' => 2, 'line_total' => 40,
        ]);

        return $order;
    }
}
