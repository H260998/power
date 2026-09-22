<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SetupTest extends TestCase
{
    use RefreshDatabase;

    private const SETUP_TOKEN = 'test-setup-token-with-more-than-32-characters';

    protected function setUp(): void
    {
        parent::setUp();

        config(['setup.token' => self::SETUP_TOKEN]);
    }

    public function test_setup_page_is_available_without_a_database_session(): void
    {
        $this->get('/setup')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('name="setup_token"', false)
            ->assertSee('name="admin_password_confirmation"', false);
    }

    public function test_invalid_setup_token_cannot_initialize_the_store(): void
    {
        $this->post('/setup', [
            'setup_token' => 'wrong-token',
            'admin_name' => 'Store Manager',
            'admin_email' => 'owner@example.test',
            'admin_password' => 'SecurePassword1',
            'admin_password_confirmation' => 'SecurePassword1',
        ])->assertForbidden();

        $this->assertDatabaseCount('admins', 0);
    }

    public function test_setup_initializes_store_and_then_locks_itself(): void
    {
        $response = $this->post('/setup', [
            'setup_token' => self::SETUP_TOKEN,
            'admin_name' => 'Store Owner',
            'admin_email' => 'OWNER@example.test',
            'admin_password' => 'SecurePassword1',
            'admin_password_confirmation' => 'SecurePassword1',
        ]);

        $response
            ->assertOk()
            ->assertSee(__('setup.complete_title'))
            ->assertDontSee(self::SETUP_TOKEN);

        $admin = Admin::where('email', 'owner@example.test')->firstOrFail();

        $this->assertSame('Store Owner', $admin->name);
        $this->assertTrue(Hash::check('SecurePassword1', $admin->password));
        $this->assertGreaterThan(0, Category::count());
        $this->assertSame(15, Product::count());
        $this->assertGreaterThan(0, Setting::count());

        $this->get('/setup')->assertNotFound();
        $this->post('/setup', [
            'setup_token' => self::SETUP_TOKEN,
        ])->assertNotFound();
    }
}
