<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_logged_in_users_cannot_access_user_crud(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_non_super_admin_cannot_access_user_crud(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'Administrator',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertStatus(403);
    }

    public function test_super_admin_can_access_user_crud_and_create_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Access index
        $this->actingAs($superAdmin)
            ->get(route('users.index'))
            ->assertStatus(200);

        // Store new user
        $response = $this->actingAs($superAdmin)
            ->post(route('users.store'), [
                'name' => 'Staf Baru',
                'email' => 'stafbaru@desabinangun.id',
                'role' => 'admin',
                'jabatan' => 'Tim Monitoring',
                'password' => 'SecurePassword2026!',
                'password_confirmation' => 'SecurePassword2026!',
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'stafbaru@desabinangun.id',
            'jabatan' => 'Tim Monitoring',
        ]);
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->delete(route('users.destroy', $superAdmin->id))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactiveUser = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'Administrator',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('auth.login.store'), [
            'email' => $inactiveUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertFalse(\Auth::check());
    }

    public function test_non_super_admin_cannot_access_audit_log(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'jabatan' => 'Administrator',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('audit.index'))
            ->assertStatus(403);
    }

    public function test_super_admin_can_access_audit_log(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('audit.index'))
            ->assertStatus(200);
    }
}
