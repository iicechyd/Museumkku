<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with valid credentials for an approved user.
     *
     * @return void
     */
    public function test_successful_login_admin()
    {
        $role = Role::create(['role_name' => 'Admin']);
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'is_approved' => true,
            'role_id' => $role->role_id
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_superadmin()
    {
        $role = Role::create(['role_name' => 'Super Admin']);
        $user = User::create([
            'name' => 'JJ deeda',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'is_approved' => true,
            'role_id' => $role->role_id
        ]);

        $response = $this->post('/login', [
            'email' => 'superadmin@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('super_admin/all_users');
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_executive()
    {
        $role = Role::create(['role_name' => 'Executive']);
        $user = User::create([
            'name' => 'Jaonay Man',
            'email' => 'executive@gmail.com',
            'password' => Hash::make('password'),
            'is_approved' => true,
            'role_id' => $role->role_id
        ]);

        $response = $this->post('/login', [
            'email' => 'executive@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('executive/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_failed_not_approved_user()
    {
        $role = Role::create(['role_name' => 'Admin']);
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'is_approved' => false,
            'role_id' => null
        ]);
        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'บัญชีของคุณยังไม่ได้รับการอนุมัติเข้าใช้งาน');
        $this->assertGuest();
    }

    public function test_failed_login_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'test1234',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
    public function test_failed_login_without_role_id()
    {
        $user = User::create([
            'name' => 'No Role User',
            'email' => 'norealrole@example.com',
            'password' => Hash::make('password'),
            'is_approved' => true,
            'role_id' => null
        ]);

        $response = $this->post('/login', [
            'email' => 'norealrole@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'บัญชีของคุณไม่มีบทบาทที่กำหนด');
        $this->assertGuest();
    }
}
