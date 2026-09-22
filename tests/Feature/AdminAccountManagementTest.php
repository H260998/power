<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_another_administrator(): void
    {
        $admin = Admin::create([
            'name' => 'Store Manager',
            'email' => 'manager@example.test',
            'password' => 'OldPassword1',
        ]);

        $this->actingAs($admin, 'admin');

        $this->get('/admin/settings')
            ->assertOk()
            ->assertSee('Store Manager')
            ->assertSee('manager@example.test')
            ->assertSee('action="'.route('admin.settings.admins.store').'"', false)
            ->assertSee('action="'.route('admin.settings.password.update').'"', false);

        $this
            ->post('/admin/settings/admins', [
                'name' => 'Second Admin',
                'email' => 'SECOND@example.test',
                'password' => 'NewPassword2',
                'password_confirmation' => 'NewPassword2',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $created = Admin::where('email', 'second@example.test')->firstOrFail();

        $this->assertSame('Second Admin', $created->name);
        $this->assertTrue(Hash::check('NewPassword2', $created->password));
    }

    public function test_admin_can_change_their_password_only_with_the_current_password(): void
    {
        $admin = Admin::create([
            'name' => 'Store Manager',
            'email' => 'manager@example.test',
            'password' => 'OldPassword1',
        ]);

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings/password', [
                'current_password' => 'WrongPassword1',
                'new_password' => 'NewPassword2',
                'new_password_confirmation' => 'NewPassword2',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue(Hash::check('OldPassword1', $admin->refresh()->password));

        $this->put('/admin/settings/password', [
            'current_password' => 'OldPassword1',
            'new_password' => 'NewPassword2',
            'new_password_confirmation' => 'NewPassword2',
        ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('NewPassword2', $admin->refresh()->password));
    }

    public function test_account_management_requires_an_authenticated_admin(): void
    {
        $this->post('/admin/settings/admins')->assertRedirect('/admin/login');
        $this->put('/admin/settings/password')->assertRedirect('/admin/login');
    }
}
