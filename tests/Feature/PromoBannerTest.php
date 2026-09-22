<?php

namespace Tests\Feature;

use App\Enums\PromoType;
use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_promo_is_advertised_without_revealing_its_code(): void
    {
        app()->setLocale('fr');

        PromoCode::create([
            'code' => 'SECRET20',
            'type' => PromoType::Percentage,
            'value' => 20,
            'min_order_amount' => 100,
            'max_uses' => 250,
            'used_count' => 0,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addWeek(),
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Offre spéciale : -20 % sur votre commande')
            ->assertSee("Dès 100 DT d'achat")
            ->assertDontSee('SECRET20');
    }

    public function test_unavailable_promos_are_not_advertised(): void
    {
        app()->setLocale('fr');

        foreach ([
            ['code' => 'INACTIVE', 'is_active' => false],
            ['code' => 'FUTURE', 'starts_at' => now()->addDay()],
            ['code' => 'EXPIRED', 'expires_at' => now()->subDay()],
            ['code' => 'USEDUP', 'max_uses' => 5, 'used_count' => 5],
        ] as $attributes) {
            PromoCode::create([
                'type' => PromoType::Percentage,
                'value' => 10,
                'used_count' => 0,
                'is_active' => true,
                ...$attributes,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Offre spéciale');
    }
}
