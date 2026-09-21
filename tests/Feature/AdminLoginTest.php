<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Laravel normally skips CSRF verification during tests.
        $this->app->bind(ValidateCsrfToken::class, fn ($app) => new class($app, $app['encrypter']) extends ValidateCsrfToken
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
    }

    public function test_expired_login_returns_to_a_fresh_form_and_can_be_retried(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'test-password-only',
        ]);

        $this->withSession(['_token' => 'current-session-token'])
            ->post('/admin/login', [
                '_token' => 'expired-session-token',
                'email' => $admin->email,
                'password' => 'test-password-only',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['session'])
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest('admin');

        $form = $this->get('/admin/login');
        $form->assertOk()
            ->assertSee(__('admin.session_expired'))
            ->assertHeader('Cache-Control', 'no-store, private');

        preg_match('/name="_token"[^>]*value="([^"]+)"/', $form->getContent(), $matches);
        $this->assertNotEmpty($matches[1] ?? null);

        $this->post('/admin/login', [
            '_token' => $matches[1],
            'email' => $admin->email,
            'password' => 'test-password-only',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_signed_in_admin_opening_login_returns_to_the_admin_dashboard(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'test-password-only',
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_opening_admin_is_redirected_to_the_login_form(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('name="password"', false);
        $this->assertGuest('admin');
    }

    public function test_json_login_keeps_the_csrf_error_response(): void
    {
        $this->postJson('/admin/login', ['_token' => 'invalid'])->assertStatus(419);
        $this->assertGuest('admin');
    }

    public function test_other_admin_actions_keep_csrf_protection(): void
    {
        $this->post('/admin/logout', ['_token' => 'invalid'])->assertStatus(419);
    }
}
