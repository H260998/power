<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingSecretTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversion_token_is_encrypted_and_never_rendered_in_admin_html(): void
    {
        $admin = Admin::create([
            'name' => 'Manager',
            'email' => 'manager@example.test',
            'password' => 'StrongPassword1',
        ]);
        $secret = 'EAAG-super-secret-token';

        $this->actingAs($admin, 'admin')
            ->put('/admin/marketing', [
                'meta_pixel_id' => '1234567890',
                'conversion_api_token' => $secret,
            ])
            ->assertSessionHasNoErrors();

        $stored = Setting::where('key', 'conversion_api_token')->value('value');
        $this->assertStringNotContainsString($secret, json_encode($stored));
        $this->assertSame($secret, app(SettingsService::class)->get('conversion_api_token'));

        $this->get('/admin/marketing')
            ->assertOk()
            ->assertDontSee($secret);
    }

    public function test_pixel_id_rejects_script_content(): void
    {
        $admin = Admin::create([
            'name' => 'Manager',
            'email' => 'manager@example.test',
            'password' => 'StrongPassword1',
        ]);

        $this->actingAs($admin, 'admin')
            ->put('/admin/marketing', [
                'meta_pixel_id' => "123');alert(1)//",
            ])
            ->assertSessionHasErrors('meta_pixel_id');

        $this->assertDatabaseMissing('settings', ['key' => 'meta_pixel_id']);
    }
}
