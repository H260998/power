<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckoutPhoneTest extends TestCase
{
    #[DataProvider('validNumbers')]
    public function test_checkout_accepts_tunisian_numbers_without_optional_notes(string $phone): void
    {
        // An empty cart redirects only after checkout validation succeeds.
        $this->post('/checkout', $this->customer($phone))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasNoErrors();
    }

    #[DataProvider('invalidNumbers')]
    public function test_checkout_rejects_invalid_numbers_even_without_browser_validation(mixed $phone): void
    {
        $this->withSession(['locale' => 'fr'])
            ->postJson('/checkout', $this->customer($phone))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone'])
            ->assertJsonPath('errors.phone.0', trans('storefront.phone_tunisia_invalid', [], 'fr'));
    }

    public static function validNumbers(): array
    {
        return [
            'mobile 2' => ['20123456'],
            'fixed 3' => ['30123456'],
            'mobile 4' => ['40123456'],
            'mobile 5' => ['50123456'],
            'fixed 7' => ['71123456'],
            'mobile 9' => ['98123456'],
        ];
    }

    public static function invalidNumbers(): array
    {
        return [
            'empty' => [''],
            'null' => [null],
            'too short' => ['2012345'],
            'too long' => ['201234567'],
            'letters' => ['2012345a'],
            'zero prefix' => ['00123456'],
            'service prefix' => ['10123456'],
            'reserved prefix' => ['60123456'],
            'special service prefix' => ['80123456'],
            'foreign international number' => ['+33612345678'],
            'foreign local number' => ['0612345678'],
            'country prefix belongs outside the field' => ['+21620123456'],
            'international access prefix' => ['0021620123456'],
            'spaces' => ['20 123 456'],
            'punctuation' => ['20-123-456'],
            'embedded newline' => ["2012\n3456"],
            'array' => [['20123456']],
            'numeric JSON value' => [20123456],
        ];
    }

    private function customer(mixed $phone): array
    {
        return [
            'first_name' => 'Test',
            'last_name' => 'Client',
            'phone' => $phone,
            'address' => '1 rue de Tunis',
            'city' => 'Tunis',
        ];
    }
}
